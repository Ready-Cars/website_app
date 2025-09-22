<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingStatusUpdatedNotification extends Notification
{
    use Queueable;

    public Booking $booking;
    public string $previousStatus;
    public string $newStatus;

    public function __construct(Booking $booking, string $previousStatus, string $newStatus)
    {
        $this->booking = $booking->loadMissing('car', 'user');
        $this->previousStatus = strtolower($previousStatus);
        $this->newStatus = strtolower($newStatus);
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $booking = $this->booking;
        return [
            'title' => 'Booking status updated',
            'message' => sprintf(
                'Your booking #%d changed from %s to %s.',
                $booking->id,
                ucfirst($this->previousStatus),
                ucfirst($this->newStatus)
            ),
            'booking_id' => $booking->id,
            'previous_status' => $this->previousStatus,
            'new_status' => $this->newStatus,
            'car_name' => $booking->car->name ?? null,
            'total' => (float) $booking->total,
            'start_date' => optional($booking->start_date)->toDateString(),
            'end_date' => optional($booking->end_date)->toDateString(),
            'url' => route('trips.index', ['booking' => $booking->id]),
        ];
    }
}
