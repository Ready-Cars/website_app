<?php

namespace App\Livewire;

use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MyTrips extends Component
{
    public string $tab = 'upcoming'; // upcoming | past

    // Modal/UI state
    public bool $viewOpen = false;
    public bool $cancelOpen = false;
    public ?Booking $selected = null;
    public string $cancelReason = '';

    public function mount(): void
    {
        // If a direct booking link is provided (?booking=ID), pre-open that booking if it belongs to the user
        $bookingId = (int) request()->query('booking', 0);
        if ($bookingId > 0 && Auth::check()) {
            $this->selected = Booking::query()
                ->with('car')
                ->whereKey($bookingId)
                ->where('user_id', Auth::id())
                ->first();
            $this->viewOpen = (bool) $this->selected;
        }
    }

    public function switchTab(string $tab): void
    {
        $this->tab = in_array($tab, ['upcoming', 'past']) ? $tab : 'upcoming';
    }

    public function view(int $bookingId): void
    {
        $this->selected = Booking::query()
            ->with('car')
            ->whereKey($bookingId)
            ->where('user_id', Auth::id())
            ->first();
        $this->viewOpen = (bool) $this->selected;
        $this->cancelOpen = false;
        $this->cancelReason = '';
    }

    public function closeView(): void
    {
        $this->viewOpen = false;
        $this->selected = null;
        $this->cancelOpen = false;
        $this->cancelReason = '';
    }

    public function openCancel(): void
    {
        if ($this->selected && $this->selected->status !== 'cancelled') {
            $this->cancelOpen = true;
            $this->cancelReason = '';
        }
    }

    public function cancelConfirm(): void
    {
        $this->validate([
            'cancelReason' => ['required','string','min:3','max:500'],
        ]);

        if (! $this->selected) return;

        $booking = Booking::query()
            ->whereKey($this->selected->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($booking && $booking->status !== 'cancelled') {
            // Check cancellation cutoff policy from settings
            $hours = \App\Models\Setting::getInt('cancellation_cutoff_hours', 24);
            if ($hours > 0) {
                $cutoff = \Carbon\Carbon::parse($booking->start_date)->startOfDay()->subHours($hours);
                if (now()->greaterThan($cutoff)) {
                    session()->flash('error', 'Cancellation period has passed. You can no longer cancel this booking.');
                    $this->cancelOpen = false;
                    return;
                }
            }

            // Use the service to handle status change and conditional refund
            try {
                $updated = app(\App\Services\BookingManagementService::class)
                    ->changeStatus($booking, 'cancelled', $this->cancelReason);

                // Email notification is now handled centrally in BookingManagementService
                $this->dispatch('rent-confirmed', message: 'Booking cancelled successfully');
            } catch (\Throwable $e) {
                session()->flash('error', $e->getMessage());
            }
        }

        $this->cancelOpen = false;
        $this->closeView();
    }

    protected function bookings()
    {
        $query = Booking::query()
            ->with('car')
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at');

        if ($this->tab === 'upcoming') {
            $query->whereDate('end_date', '>=', now()->toDateString());
        } else {
            $query->whereDate('end_date', '<', now()->toDateString());
        }

        return $query->get();
    }

    public function render()
    {
        return view('livewire.my-trips', [
            'trips' => $this->bookings(),
        ]);
    }
}
