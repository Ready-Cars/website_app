<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Booking $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking->loadMissing('car', 'user');
    }

    public function build(): self
    {
        return $this
            ->subject('Booking Confirmation - '.config('app.name'))
            ->markdown('emails.booking.confirmed', [
                'booking' => $this->booking,
                'appName' => config('app.name'),
                'tripsUrl' => route('trips.index'),
            ]);
    }
}
