<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\PaystackService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ValidatePendingPaymentsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_runs_successfully_with_no_pending_payments(): void
    {
        $this->artisan('payments:validate-pending --dry-run')
            ->expectsOutput('Running in dry-run mode - no changes will be made')
            ->expectsOutput('Starting validation of pending payments...')
            ->expectsOutput('No pending payments found to validate.')
            ->assertExitCode(0);
    }

    public function test_command_validates_pending_wallet_transactions(): void
    {
        // Create a user and wallet transaction with Paystack reference
        $user = User::factory()->create(['wallet_balance' => 100.00]);

        $transaction = WalletTransaction::create([
            'user_id' => $user->id,
            'type' => 'credit',
            'amount' => 50.00,
            'balance_after' => 150.00,
            'description' => 'Wallet funding (Paystack)',
            'meta' => [
                'provider' => 'paystack',
                'reference' => 'TEST_REF_123',
                'paid_at' => now()->toISOString(),
            ],
        ]);

        // Mock PaystackService
        $this->mock(PaystackService::class, function ($mock) {
            $mock->shouldReceive('verify')
                ->with('TEST_REF_123')
                ->once()
                ->andReturn([
                    'status' => true,
                    'amount_kobo' => 5000,
                    'currency' => 'NGN',
                    'customer_email' => 'test@example.com',
                    'reference' => 'TEST_REF_123',
                    'gateway_response' => 'Successful',
                    'paid_at' => now()->toISOString(),
                ]);
        });

        $this->artisan('payments:validate-pending --dry-run')
            ->expectsOutput('Running in dry-run mode - no changes will be made')
            ->expectsOutput("Checking wallet transaction ID {$transaction->id} with reference: TEST_REF_123")
            ->expectsOutput("✓ Transaction {$transaction->id} verified as successful")
            ->assertExitCode(0);
    }

    public function test_command_validates_pending_booking_payments(): void
    {
        // Create user and booking with payment reference
        $user = User::factory()->create(['wallet_balance' => 0]);

        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
            'payment_reference' => 'BOOKING_REF_456',
            'total' => 100.00,
        ]);

        // Mock PaystackService
        $this->mock(PaystackService::class, function ($mock) {
            $mock->shouldReceive('verify')
                ->with('BOOKING_REF_456')
                ->once()
                ->andReturn([
                    'status' => true,
                    'amount_kobo' => 10000,
                    'currency' => 'NGN',
                    'customer_email' => 'test@example.com',
                    'reference' => 'BOOKING_REF_456',
                    'gateway_response' => 'Successful',
                    'paid_at' => now()->toISOString(),
                ]);
        });

        $this->artisan('payments:validate-pending --dry-run')
            ->expectsOutput('Running in dry-run mode - no changes will be made')
            ->expectsOutput("Checking booking ID {$booking->id} with reference: BOOKING_REF_456")
            ->expectsOutput("✓ Booking {$booking->id} payment verified as successful")
            ->assertExitCode(0);
    }

    public function test_command_updates_booking_status_when_not_dry_run(): void
    {
        // Create user and booking with payment reference
        $user = User::factory()->create(['wallet_balance' => 0]);

        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
            'payment_reference' => 'BOOKING_REF_789',
            'total' => 100.00,
        ]);

        // Mock PaystackService
        $this->mock(PaystackService::class, function ($mock) {
            $mock->shouldReceive('verify')
                ->with('BOOKING_REF_789')
                ->once()
                ->andReturn([
                    'status' => true,
                    'amount_kobo' => 10000,
                    'currency' => 'NGN',
                    'customer_email' => 'test@example.com',
                    'reference' => 'BOOKING_REF_789',
                    'gateway_response' => 'Successful',
                    'paid_at' => now()->toISOString(),
                ]);
        });

        $this->artisan('payments:validate-pending')
            ->expectsOutput("✓ Booking {$booking->id} payment verified as successful")
            ->expectsOutput('Bookings updated: 1')
            ->assertExitCode(0);

        // Assert booking status was updated
        $booking->refresh();
        $this->assertEquals('confirmed', $booking->status);

        // Assert wallet transaction was created
        $this->assertDatabaseHas('wallet_transactions', [
            'user_id' => $user->id,
            'type' => 'credit',
            'amount' => 100.00,
            'description' => 'Booking payment confirmation',
        ]);

        // Assert user wallet balance was updated
        $user->refresh();
        $this->assertEquals(100.00, $user->wallet_balance);
    }

    public function test_command_handles_failed_payment_verification(): void
    {
        // Create a user and wallet transaction with Paystack reference
        $user = User::factory()->create(['wallet_balance' => 100.00]);

        $transaction = WalletTransaction::create([
            'user_id' => $user->id,
            'type' => 'credit',
            'amount' => 50.00,
            'balance_after' => 150.00,
            'description' => 'Wallet funding (Paystack)',
            'meta' => [
                'provider' => 'paystack',
                'reference' => 'FAILED_REF_999',
                'paid_at' => now()->toISOString(),
            ],
        ]);

        // Mock PaystackService to return failed verification
        $this->mock(PaystackService::class, function ($mock) {
            $mock->shouldReceive('verify')
                ->with('FAILED_REF_999')
                ->once()
                ->andReturn([
                    'status' => false,
                    'message' => 'Transaction not found',
                ]);
        });

        $this->artisan('payments:validate-pending --dry-run')
            ->expectsOutput("Checking wallet transaction ID {$transaction->id} with reference: FAILED_REF_999")
            ->expectsOutput("✗ Transaction {$transaction->id} verification failed or not successful")
            ->assertExitCode(0);
    }

    public function test_command_handles_api_errors_gracefully(): void
    {
        // Create a user and wallet transaction with Paystack reference
        $user = User::factory()->create(['wallet_balance' => 100.00]);

        $transaction = WalletTransaction::create([
            'user_id' => $user->id,
            'type' => 'credit',
            'amount' => 50.00,
            'balance_after' => 150.00,
            'description' => 'Wallet funding (Paystack)',
            'meta' => [
                'provider' => 'paystack',
                'reference' => 'ERROR_REF_888',
                'paid_at' => now()->toISOString(),
            ],
        ]);

        // Mock PaystackService to throw exception
        $this->mock(PaystackService::class, function ($mock) {
            $mock->shouldReceive('verify')
                ->with('ERROR_REF_888')
                ->once()
                ->andThrow(new \Exception('Network error'));
        });

        $this->artisan('payments:validate-pending --dry-run')
            ->expectsOutput("Checking wallet transaction ID {$transaction->id} with reference: ERROR_REF_888")
            ->expectsOutput("Error validating transaction {$transaction->id}: Network error")
            ->assertExitCode(0);
    }
}
