<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminNewBookingNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public Booking $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking->loadMissing('car', 'user');
    }

    public function build(): self
    {
        $appName = config('app.name');
        $subject = "New Booking Notification - {$appName}";
        $bookingUrl = route('admin.bookings', ['booking' => $this->booking->id]);

        return $this
            ->subject($subject)
            ->markdown('emails.admin.new-booking', [
                'booking' => $this->booking,
                'appName' => $appName,
                'bookingUrl' => $bookingUrl,
            ]);
    }
}
