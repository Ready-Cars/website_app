<?php

namespace App\Livewire;

use App\Models\Car;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Url;
use Livewire\Component;

class RentCar extends Component
{
    public Car $car;

    #[Url]
    public ?string $startDate = null; // Y-m-d
    #[Url]
    public ?string $endDate = null;   // Y-m-d

    public string $pickupLocation = '';
    public string $dropoffLocation = '';

    public array $extras = [
        'gps' => false,
        'child_seat' => false,
        'insurance' => true,
    ];

    public string $notes = '';

    // Editing existing booking (optional)
    #[Url]
    public ?int $booking = null; // booking id from query string
    protected ?\App\Models\Booking $editing = null;

    // UI: confirm modal state
    public bool $confirmOpen = false;

    // UI: success modal state
    public bool $successOpen = false;
    public string $successMessage = '';

    public function mount(Car $car): void
    {
        $this->car = $car;
        // Prevent renting disabled cars
        if (!$this->car->is_active) {
            session()->flash('error', 'This car is currently unavailable.');
            $this->redirect(route('cars.index'), navigate: true);
            return;
        }

        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();
        $this->startDate = $this->startDate ?: $today->toDateString();
        $this->endDate = $this->endDate ?: $tomorrow->toDateString();

        // If a booking id is provided and the user is authenticated, try to preload booking details
        if ($this->booking && auth()->check()) {
            $this->editing = \App\Models\Booking::query()
                ->whereKey($this->booking)
                ->where('user_id', auth()->id())
                ->where('car_id', $this->car->id)
                ->first();
            if ($this->editing && $this->editing->status !== 'cancelled') {
                $this->pickupLocation = (string) $this->editing->pickup_location;
                $this->dropoffLocation = (string) $this->editing->dropoff_location;
                $this->startDate = optional($this->editing->start_date)->toDateString() ?: $this->startDate;
                $this->endDate = optional($this->editing->end_date)->toDateString() ?: $this->endDate;
                $this->extras = (array) ($this->editing->extras ?? $this->extras);
                $this->notes = (string) ($this->editing->notes ?? '');
            }
        }
    }

    public function openConfirm(): void
    {
        $this->confirmOpen = true;
    }

    public function closeConfirm(): void
    {
        $this->confirmOpen = false;
    }

    public function cancelConfirm(): void
    {
        $this->confirmOpen = false;
        // Notify UI to show a cancel toast
        $this->dispatch('reservation-cancelled');
    }

    public function updatedStartDate(): void
    {
        $this->normalizeDates();
    }

    public function updatedEndDate(): void
    {
        $this->normalizeDates();
    }

    protected function normalizeDates(): void
    {
        if (!$this->startDate || !$this->endDate) {
            return;
        }
        try {
            $start = Carbon::parse($this->startDate)->startOfDay();
            $end = Carbon::parse($this->endDate)->startOfDay();
        } catch (\Exception $e) {
            return;
        }
        if ($end->lessThanOrEqualTo($start)) {
            // Minimum 1 day rental
            $this->endDate = $start->copy()->addDay()->toDateString();
        }
    }

    public function rules(): array
    {
        return [
            'pickupLocation' => ['required', 'string', 'min:2', 'max:120'],
            'dropoffLocation' => ['required', 'string', 'min:2', 'max:120'],
            'startDate' => ['required', 'date', 'after_or_equal:today'],
            'endDate' => ['required', 'date', 'after:startDate'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function getDaysProperty(): int
    {
        try {
            $start = Carbon::parse($this->startDate)->startOfDay();
            $end = Carbon::parse($this->endDate)->startOfDay();
            $days = $start->diffInDays($end);
            return max(1, $days);
        } catch (\Exception $e) {
            return 1;
        }
    }

    public function getExtrasCostProperty(): float
    {
        $cost = 0.0;
        if ($this->extras['gps'] ?? false) {
            $cost += 5.00 * $this->days; // $5 per day
        }
        if ($this->extras['child_seat'] ?? false) {
            $cost += 4.00 * $this->days; // $4 per day
        }
        if ($this->extras['insurance'] ?? false) {
            $cost += 12.00 * $this->days; // $12 per day
        }
        return $cost;
    }

    public function getSubtotalProperty(): float
    {
        return ((float) $this->car->daily_price) * $this->days + $this->extrasCost;
    }

    public function getTaxesProperty(): float
    {
        // Flat 8% tax for demonstration
        return round($this->subtotal * 0.08, 2);
    }

    public function getTotalProperty(): float
    {
        return round($this->subtotal + $this->taxes, 2);
    }

    protected function resetRentalForm(): void
    {
        // Reset all form fields to their defaults after a successful confirmation
        $this->pickupLocation = '';
        $this->dropoffLocation = '';
        $this->notes = '';
        $this->extras = [
            'gps' => false,
            'child_seat' => false,
            'insurance' => true,
        ];
        // Reset dates to today/tomorrow
        $this->startDate = Carbon::today()->toDateString();
        $this->endDate = Carbon::tomorrow()->toDateString();
    }

    public function confirmRent(): void
    {
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $keys = array_keys($e->validator->errors()->messages());
            $first = $keys[0] ?? null;
            if ($first) {
                // Tell the browser to scroll to the first invalid field
                $this->dispatch('scroll-to-field', field: $first);
            }
            throw $e; // keep default error rendering/messages
        }

        // Require authentication before making a booking
        if (!auth()->check()) {
            // Close modal if open
            $this->confirmOpen = false;
            // Redirect guests to login, preserving intended URL
            $this->redirect(route('login'));
            return;
        }

        // Close modal if open
        $this->confirmOpen = false;

        // Wallet check and booking persistence in a transaction
        $user = auth()->user();
        $editing = null;
        if ($this->booking && $user) {
            $editing = \App\Models\Booking::query()
                ->whereKey($this->booking)
                ->where('user_id', $user->id)
                ->where('car_id', $this->car->id)
                ->first();
        }

        // Determine amount to charge (or refund) based on editing state
        $charge = 0.0;
        $refund = 0.0;
        if ($editing && $editing->status !== 'cancelled') {
            $diff = round($this->total - (float)$editing->total, 2);
            if ($diff > 0) { $charge = $diff; }
            if ($diff < 0) { $refund = abs($diff); }
        } else {
            $charge = round($this->total, 2);
        }

        // If charge required, ensure balance is sufficient
        if ($charge > 0 && $user->wallet_balance < $charge) {
            session()->flash('wallet_error', 'Insufficient wallet balance. Please fund your wallet to complete the reservation.');
            $this->redirect(route('wallet.index'), navigate: true);
            return;
        }

        $booking = null;
        \Illuminate\Support\Facades\DB::transaction(function () use ($user, $editing, $charge, $refund, &$booking) {
            // Update wallet balance
            if ($charge > 0) {
                $user->wallet_balance = round($user->wallet_balance - $charge, 2);
            }
            if ($refund > 0) {
                $user->wallet_balance = round($user->wallet_balance + $refund, 2);
            }
            $user->save();

            if ($editing && $editing->status !== 'cancelled') {
                $editing->update([
                    'pickup_location' => $this->pickupLocation,
                    'dropoff_location' => $this->dropoffLocation,
                    'start_date' => $this->startDate,
                    'end_date' => $this->endDate,
                    'extras' => $this->extras,
                    'notes' => $this->notes,
                    'subtotal' => $this->subtotal,
                    'taxes' => $this->taxes,
                    'total' => $this->total,
                    'status' => 'confirmed',
                ]);
            } else {
                $booking = \App\Models\Booking::create([
                    'user_id' => $user->id,
                    'car_id' => $this->car->id,
                    'pickup_location' => $this->pickupLocation,
                    'dropoff_location' => $this->dropoffLocation,
                    'start_date' => $this->startDate,
                    'end_date' => $this->endDate,
                    'extras' => $this->extras,
                    'notes' => $this->notes,
                    'subtotal' => $this->subtotal,
                    'taxes' => $this->taxes,
                    'total' => $this->total,
                    'status' => 'confirmed',
                ]);
            }

            // Log wallet transactions for charge/refund (if any)
            if ($charge > 0) {
                \App\Models\WalletTransaction::create([
                    'user_id' => $user->id,
                    'type' => 'debit',
                    'amount' => $charge,
                    'balance_after' => $user->wallet_balance,
                    'description' => 'Booking charge',
                    'meta' => ['car_id' => $this->car->id, 'booking_id' => $booking?->id ?? $editing?->id],
                ]);
            }
            if ($refund > 0) {
                \App\Models\WalletTransaction::create([
                    'user_id' => $user->id,
                    'type' => 'credit',
                    'amount' => $refund,
                    'balance_after' => $user->wallet_balance,
                    'description' => 'Booking refund',
                    'meta' => ['car_id' => $this->car->id, 'booking_id' => $booking?->id ?? $editing?->id],
                ]);
            }
        });

        // Send confirmation email (non-blocking intention; swallow errors)
        try {
            $final = $editing && $editing->status !== 'cancelled'
                ? $editing->fresh(['car','user'])
                : ($booking ? $booking->load(['car','user']) : null);
            if ($final) {
                \Illuminate\Support\Facades\Mail::to($user->email)
                    ->send(new \App\Mail\BookingConfirmedMail($final));
            }
        } catch (\Throwable $e) {
            \Log::warning('Booking confirmation email failed: '.$e->getMessage());
        }

        // Success message and redirect to My Trips
        $message = "Your reservation request has been received! We'll contact you shortly.";
        session()->flash('rent_success', $message);
        $this->redirect(route('trips.index'), navigate: true);
        return;
    }

    public function render()
    {
        return view('livewire.rent-car');
    }
}
