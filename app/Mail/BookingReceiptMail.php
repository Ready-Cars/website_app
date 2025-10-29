<?php

namespace App\Mail;

use App\Models\Booking;
use App\Services\ReceiptService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingReceiptMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Booking $booking;

    /**
     * Create a new message instance.
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking->loadMissing('user', 'car', 'serviceType');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $appName = config('app.name');
        $subject = "Your Booking Receipt - {$appName}";

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.booking.receipt',
            with: [
                'booking' => $this->booking,
                'appName' => config('app.name'),
                'bookingUrl' => route('trips.index', ['booking' => $this->booking->id]),
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $receiptService = app(ReceiptService::class);
        $filename = 'receipt-'.$this->booking->id.'-'.now()->format('Y-m-d').'.pdf';

        return [
            Attachment::fromData(
                fn () => $receiptService->getReceiptPdfData($this->booking),
                $filename
            )->withMime('application/pdf'),
        ];
    }
}
