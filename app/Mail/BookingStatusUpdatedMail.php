<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Booking $booking;
    public string $previousStatus;
    public string $newStatus;

    public function __construct(Booking $booking, string $previousStatus, string $newStatus)
    {
        $this->booking = $booking->loadMissing('car', 'user');
        $this->previousStatus = strtolower($previousStatus);
        $this->newStatus = strtolower($newStatus);
    }

    public function build(): self
    {
        $appName = config('app.name');
        $subject = "Your Booking Status Updated to ".ucfirst($this->newStatus)." - {$appName}";
        $bookingUrl = route('trips.index', ['booking' => $this->booking->id]);

        return $this
            ->subject($subject)
            ->markdown('emails.booking.status-updated', [
                'booking' => $this->booking,
                'previousStatus' => $this->previousStatus,
                'newStatus' => $this->newStatus,
                'appName' => $appName,
                'bookingUrl' => $bookingUrl,
            ]);
    }
}
