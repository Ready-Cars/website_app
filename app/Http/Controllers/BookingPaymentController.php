<?php

namespace App\Http\Controllers;

use App\Services\BookingManagementService;
use App\Services\PaystackService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BookingPaymentController extends Controller
{
    public function callback(Request $request, BookingManagementService $bookingService, PaystackService $paystackService): RedirectResponse
    {
        $reference = $request->get('reference');

        if (! $reference) {
            return redirect()->route('home')->with('error', 'Invalid payment reference');
        }

        try {
            $result = $bookingService->handlePaymentCallback($reference, $paystackService);

            if ($result['status'] === 'success') {

                return redirect()->route('booking.result')
                    ->with('success', 'Payment successful! Your booking has been confirmed.')
                    ->with('booking', $result['booking'] ?? null);
            }

            return redirect()->route('booking.result')->with('error', 'Payment verification failed. Please contact support.');

        } catch (\Throwable $e) {

            Log::error('Booking payment callback failed: '.$e->getMessage(), [
                'reference' => $reference,
                'exception' => $e,
            ]);

            return redirect()->route('booking.result')->with('error', 'Payment processing failed. Please contact support if you were charged.');
        }
    }
}
