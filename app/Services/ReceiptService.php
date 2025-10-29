<?php

namespace App\Services;

use App\Models\Booking;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Response;

class ReceiptService
{
    /**
     * Generate a PDF receipt for a booking.
     */
    public function generateReceiptPdf(Booking $booking): string
    {
        // Load necessary relationships
        $booking->load(['user', 'car', 'serviceType']);

        // Configure PDF options
        $options = new Options;
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);

        // Create new PDF instance
        $dompdf = new Dompdf($options);

        // Generate HTML content for the receipt
        $html = view('receipts.booking-receipt', compact('booking'))->render();

        // Load HTML into dompdf
        $dompdf->loadHtml($html);

        // Set paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render PDF
        $dompdf->render();

        return $dompdf->output();
    }

    /**
     * Download a receipt PDF for a booking.
     */
    public function downloadReceipt(Booking $booking): Response
    {
        $pdf = $this->generateReceiptPdf($booking);
        $filename = 'receipt-'.$booking->id.'-'.now()->format('Y-m-d').'.pdf';

        return response($pdf)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }

    /**
     * Get receipt PDF as binary data for email attachment.
     */
    public function getReceiptPdfData(Booking $booking): string
    {
        return $this->generateReceiptPdf($booking);
    }
}
