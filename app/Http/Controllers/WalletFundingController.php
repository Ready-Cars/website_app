<?php

namespace App\Http\Controllers;

use App\Models\WalletTransaction;
use App\Services\PaystackService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WalletFundingController extends Controller
{
    public function init(Request $request, PaystackService $paystack): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required','numeric','min:100'], // min ₦100
        ]);

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Convert to kobo (NGN)
        $amountKobo = (int) round(((float)$validated['amount']) * 100);
        $callbackUrl = route('wallet.paystack.callback');
        $init = $paystack->initialize($amountKobo, $user->email, $callbackUrl);

        if (!($init['status'] ?? false) || empty($init['authorization_url'])) {
            $msg = $init['message'] ?? 'Unable to initialize payment.';
            return redirect()->route('wallet.index')->with('wallet_error', $msg);
        }

        // Optionally store last reference in session for tracing
        session(['wallet_last_reference' => $init['reference'] ?? null]);

        return redirect()->away($init['authorization_url']);
    }

    public function callback(Request $request, PaystackService $paystack): RedirectResponse
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $reference = (string) $request->query('reference', session('wallet_last_reference'));
        if (!$reference) {
            return redirect()->route('wallet.index')->with('wallet_error', 'No reference provided for verification.');
        }

        try {
            $result = $paystack->verify($reference);
        } catch (\Throwable $e) {
            Log::warning('Paystack verify error: '.$e->getMessage());
            return redirect()->route('wallet.index')->with('wallet_error', 'Could not verify payment at this time.');
        }

        if (!($result['status'] ?? false)) {
            $message = $result['message'] ?? 'Payment not successful.';
            return redirect()->route('wallet.index')->with('wallet_error', $message);
        }

        // Only accept NGN and positive amount
        $amountKobo = (int) ($result['amount_kobo'] ?? 0);
        if ($amountKobo <= 0) {
            return redirect()->route('wallet.index')->with('wallet_error', 'Invalid payment amount.');
        }

        $amountNaira = round($amountKobo / 100, 2);

        // Credit wallet and log transaction
        \Illuminate\Support\Facades\DB::transaction(function () use ($user, $amountNaira, $reference, $result) {
            $user->wallet_balance = round($user->wallet_balance + $amountNaira, 2);
            $user->save();

            WalletTransaction::create([
                'user_id' => $user->id,
                'type' => 'credit',
                'amount' => $amountNaira,
                'balance_after' => $user->wallet_balance,
                'description' => 'Wallet funding (Paystack)',
                'meta' => [
                    'provider' => 'paystack',
                    'reference' => $reference,
                    'paid_at' => $result['paid_at'] ?? null,
                    'gateway_response' => $result['gateway_response'] ?? null,
                ],
            ]);
        });

        return redirect()->route('wallet.index')->with('wallet_success', 'Wallet funded successfully.');
    }
}
