<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\PaystackService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ValidatePendingPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:validate-pending {--dry-run : Show what would be validated without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Validate pending wallet transactions and booking payments from Paystack to determine if they are successful or not';

    /**
     * Execute the console command.
     */
    public function handle(PaystackService $paystackService): int
    {
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->info('Running in dry-run mode - no changes will be made');
        }

        $this->info('Starting validation of pending payments...');

        $walletResults = $this->validatePendingWalletTransactions($paystackService, $isDryRun);
        $bookingResults = $this->validatePendingBookingPayments($paystackService, $isDryRun);

        $this->displayResults($walletResults, $bookingResults, $isDryRun);

        return Command::SUCCESS;
    }

    /**
     * Validate pending wallet transactions that have Paystack references
     */
    protected function validatePendingWalletTransactions(PaystackService $paystackService, bool $isDryRun): array
    {
        $this->line('');
        $this->info('Validating pending wallet transactions...');

        // Find wallet transactions that might need validation
        // Look for transactions with Paystack references that could be unverified
        $transactions = WalletTransaction::whereJsonContains('meta->provider', 'paystack')
            ->whereNotNull('meta->reference')
            ->where('created_at', '>=', now()->subDays(7)) // Only check recent transactions
            ->get();

        $results = [
            'total' => $transactions->count(),
            'validated' => 0,
            'successful' => 0,
            'failed' => 0,
            'errors' => 0,
        ];

        foreach ($transactions as $transaction) {
            $reference = data_get($transaction->meta, 'reference');

            if (!$reference) {
                continue;
            }

            $this->line("Checking wallet transaction ID {$transaction->id} with reference: {$reference}");

            try {
                $verification = $paystackService->verify($reference);
                $results['validated']++;

                if ($verification['status']) {
                    $results['successful']++;
                    $this->info("✓ Transaction {$transaction->id} verified as successful");

                    // Log verification for audit trail
                    if (!$isDryRun) {
                        Log::info("Wallet transaction validated", [
                            'transaction_id' => $transaction->id,
                            'reference' => $reference,
                            'status' => 'success',
                            'paystack_data' => $verification,
                        ]);
                    }
                } else {
                    $results['failed']++;
                    $this->warn("✗ Transaction {$transaction->id} verification failed or not successful");

                    if (!$isDryRun) {
                        Log::warning("Wallet transaction validation failed", [
                            'transaction_id' => $transaction->id,
                            'reference' => $reference,
                            'status' => 'failed',
                            'paystack_data' => $verification,
                        ]);
                    }
                }
            } catch (\Exception $e) {
                $results['errors']++;
                $this->error("Error validating transaction {$transaction->id}: " . $e->getMessage());

                if (!$isDryRun) {
                    Log::error("Wallet transaction validation error", [
                        'transaction_id' => $transaction->id,
                        'reference' => $reference,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        return $results;
    }

    /**
     * Validate pending booking payments
     */
    protected function validatePendingBookingPayments(PaystackService $paystackService, bool $isDryRun): array
    {
        $this->line('');
        $this->info('Validating pending booking payments...');

        // Find bookings that might have pending payments
        $bookings = Booking::whereNotNull('payment_reference')
            ->whereIn('status', ['pending', 'awaiting_payment'])
            ->where('created_at', '>=', now()->subDays(7)) // Only check recent bookings
            ->get();

        $results = [
            'total' => $bookings->count(),
            'validated' => 0,
            'successful' => 0,
            'failed' => 0,
            'errors' => 0,
            'updated_bookings' => 0,
        ];

        foreach ($bookings as $booking) {
            $reference = $booking->payment_reference;

            $this->line("Checking booking ID {$booking->id} with reference: {$reference}");

            try {
                $verification = $paystackService->verify($reference);
                $results['validated']++;

                if ($verification['status']) {
                    $results['successful']++;
                    $this->info("✓ Booking {$booking->id} payment verified as successful");

                    if (!$isDryRun) {
                        // Update booking status if payment is successful
                        DB::transaction(function () use ($booking, $verification, $reference) {
                            $booking->update(['status' => 'confirmed']);

                            // Create wallet transaction if this was a wallet funding that went through booking
                            if ($booking->user && $verification['amount_kobo'] > 0) {
                                $amountNaira = round($verification['amount_kobo'] / 100, 2);

                                // Check if we already have this wallet transaction
                                $existingTransaction = WalletTransaction::where('user_id', $booking->user_id)
                                    ->whereJsonContains('meta->reference', $reference)
                                    ->first();

                                if (!$existingTransaction) {
                                    $booking->user->increment('wallet_balance', $amountNaira);
                                    $booking->user->refresh();

                                    WalletTransaction::create([
                                        'user_id' => $booking->user_id,
                                        'type' => 'credit',
                                        'amount' => $amountNaira,
                                        'balance_after' => $booking->user->wallet_balance,
                                        'description' => 'Booking payment confirmation',
                                        'meta' => [
                                            'provider' => 'paystack',
                                            'reference' => $reference,
                                            'booking_id' => $booking->id,
                                            'paid_at' => $verification['paid_at'],
                                            'gateway_response' => $verification['gateway_response'],
                                            'validated_at' => now()->toISOString(),
                                        ],
                                    ]);
                                }
                            }
                        });

                        $results['updated_bookings']++;

                        Log::info("Booking payment validated and confirmed", [
                            'booking_id' => $booking->id,
                            'reference' => $reference,
                            'status' => 'success',
                            'paystack_data' => $verification,
                        ]);
                    }
                } else {
                    $results['failed']++;
                    $this->warn("✗ Booking {$booking->id} payment verification failed or not successful");

                    if (!$isDryRun) {
                        Log::warning("Booking payment validation failed", [
                            'booking_id' => $booking->id,
                            'reference' => $reference,
                            'status' => 'failed',
                            'paystack_data' => $verification,
                        ]);
                    }
                }
            } catch (\Exception $e) {
                $results['errors']++;
                $this->error("Error validating booking {$booking->id}: " . $e->getMessage());

                if (!$isDryRun) {
                    Log::error("Booking payment validation error", [
                        'booking_id' => $booking->id,
                        'reference' => $reference,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        return $results;
    }

    /**
     * Display validation results
     */
    protected function displayResults(array $walletResults, array $bookingResults, bool $isDryRun): void
    {
        $this->line('');
        $this->info('=== Validation Results ===');

        if ($isDryRun) {
            $this->line('<fg=yellow>DRY RUN - No changes were made</>');
        }

        $this->line('');
        $this->info('Wallet Transactions:');
        $this->line("Total checked: {$walletResults['total']}");
        $this->line("Validated: {$walletResults['validated']}");
        $this->line("Successful: {$walletResults['successful']}");
        $this->line("Failed: {$walletResults['failed']}");
        $this->line("Errors: {$walletResults['errors']}");

        $this->line('');
        $this->info('Booking Payments:');
        $this->line("Total checked: {$bookingResults['total']}");
        $this->line("Validated: {$bookingResults['validated']}");
        $this->line("Successful: {$bookingResults['successful']}");
        $this->line("Failed: {$bookingResults['failed']}");
        $this->line("Errors: {$bookingResults['errors']}");

        if (!$isDryRun) {
            $this->line("Bookings updated: {$bookingResults['updated_bookings']}");
        }

        $this->line('');
        $totalValidated = $walletResults['validated'] + $bookingResults['validated'];
        $totalSuccessful = $walletResults['successful'] + $bookingResults['successful'];
        $totalErrors = $walletResults['errors'] + $bookingResults['errors'];

        if ($totalValidated > 0) {
            $this->info("Total payments validated: {$totalValidated}");
            $this->info("Total successful: {$totalSuccessful}");

            if ($totalErrors > 0) {
                $this->warn("Total errors: {$totalErrors}");
            }
        } else {
            $this->info('No pending payments found to validate.');
        }
    }
}
