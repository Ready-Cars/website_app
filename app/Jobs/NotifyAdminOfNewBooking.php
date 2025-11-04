<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class NotifyAdminOfNewBooking implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public $user, public $editing, public $booking)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        $this->notifyAdmin($this->user, $this->editing, $this->booking);
    }

    public function notifyAdmin($user, $editing, $booking)
    {
        try {
            $finalBooking = $editing && $editing->status !== 'cancelled'
                ? $editing->fresh(['car', 'user'])
                : ($booking ? $booking->load(['car', 'user']) : null);

            if ($finalBooking) {
                // Get all admin users
                $admins = \App\Models\User::where('is_admin', true)->get();

                foreach ($admins as $admin) {
                    try {
                        \Illuminate\Support\Facades\Mail::to($admin->email)
                            ->send(new \App\Mail\AdminNewBookingNotificationMail($finalBooking));
                    } catch (\Throwable $e) {
                        \Log::warning('Admin notification email failed for admin '.$admin->id.': '.$e->getMessage());
                    }
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('Admin booking notification failed: '.$e->getMessage());
        }

        // Send confirmation email only if confirmed
        try {
            $final = $editing && $editing->status !== 'cancelled'
                ? $editing->fresh(['car', 'user'])
                : ($booking ? $booking->load(['car', 'user']) : null);
            if ($final && $final->status === 'confirmed') {
                $prev = 'pending';
                \Illuminate\Support\Facades\Mail::to($user->email)
                    ->send(new \App\Mail\BookingStatusUpdatedMail($final, $prev, 'confirmed'));
                // In-app notification
                try {
                    $user->notify(new \App\Notifications\BookingStatusUpdatedNotification($final, $prev, 'confirmed'));
                } catch (\Throwable $e) {
                    \Log::warning('Booking confirmation in-app notification failed: '.$e->getMessage());
                }

                // Send receipt email with PDF attachment
                try {
                    \Illuminate\Support\Facades\Mail::to($user->email)
                        ->send(new \App\Mail\BookingReceiptMail($final));
                } catch (\Throwable $e) {
                    \Log::warning('Booking receipt email failed: '.$e->getMessage());
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('Booking confirmation email failed: '.$e->getMessage());
        }

    }
}
