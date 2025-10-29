<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\ReceiptService;

class BookingController extends Controller
{
    protected ReceiptService $receiptService;

    public function __construct(ReceiptService $receiptService)
    {
        $this->receiptService = $receiptService;
    }

    /**
     * Download receipt PDF for a booking.
     */
    public function downloadReceipt(Booking $booking)
    {
        // Ensure user can only download their own booking receipts
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to booking receipt.');
        }

        // Only allow downloading receipts for confirmed bookings
        if ($booking->status !== 'confirmed') {
            abort(400, 'Receipt is only available for confirmed bookings.');
        }

        return $this->receiptService->downloadReceipt($booking);
    }

    /**
     * Download payment evidence file for a booking (admin only).
     */
    public function downloadPaymentEvidence(Booking $booking)
    {
        // Check if booking has payment evidence
        if (empty($booking->payment_evidence)) {
            abort(404, 'No payment evidence found for this booking.');
        }

        // Get the file path
        $filePath = storage_path('app/public/' . $booking->payment_evidence);

        // Check if file exists
        if (!file_exists($filePath)) {
            abort(404, 'Payment evidence file not found.');
        }

        // Get the original filename and extension
        $originalName = basename($booking->payment_evidence);
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);

        // Create a more descriptive filename
        $downloadName = sprintf('booking-%d-payment-evidence.%s', $booking->id, $extension);

        return response()->download($filePath, $downloadName);
    }
}
