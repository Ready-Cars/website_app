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
            $booking->status = 'cancelled';
            $booking->cancellation_reason = $this->cancelReason;
            $booking->save();

            // Send cancellation email to the customer
            try {
                \Illuminate\Support\Facades\Mail::to(\Illuminate\Support\Facades\Auth::user()->email)
                    ->send(new \App\Mail\BookingCancelledMail($booking));
            } catch (\Throwable $e) {
                \Log::warning('Booking cancellation email failed: '.$e->getMessage());
            }

            $this->dispatch('rent-confirmed');
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
