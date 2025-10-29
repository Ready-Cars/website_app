<?php

namespace App\Livewire;

use App\Models\Car;
use App\Models\Extra;
use App\Models\ServiceType;
use App\Models\Setting;
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

    // Selected extras keyed by extra name => bool
    public array $extras = [];

    // Available extras loaded from DB
    public array $availableExtras = [];

    public string $notes = '';

    // Service type selection
    public ?int $serviceTypeId = 0;

    public array $serviceTypeOptions = [];

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
        if (! $this->car->is_active) {
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

        $this->loadAvailableExtras();

        $this->loadServiceTypes();
        // Set default service type
        if ($this->editing && $this->editing->service_type_id) {
            $this->serviceTypeId = (int) $this->editing->service_type_id;
        } elseif ($this->serviceTypeId === null && ! empty($this->serviceTypeOptions)) {
            $this->serviceTypeId = (int) ($this->serviceTypeOptions[0]['id'] ?? null);
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
        if (! $this->startDate || ! $this->endDate) {
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
            'serviceTypeId' => [
                'required',
                'integer',
                Rule::exists('service_types', 'id')->where(fn ($q) => $q->where('is_active', true)),
            ],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    protected function loadServiceTypes(): void
    {
        $this->serviceTypeOptions = ServiceType::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'pricing_type'])
            ->map(fn ($st) => [
                'id' => $st->id,
                'name' => $st->name,
                'pricing_type' => $st->pricing_type,
            ])->all();
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
        foreach ($this->availableExtras as $ex) {
            $key = (string) ($ex['key'] ?? '');
            $price = (float) ($ex['price_per_day'] ?? 0);
            if ($key !== '' && ($this->extras[$key] ?? false)) {
                $cost += $price * $this->days;
            }
        }

        return round($cost, 2);
    }

    public function getSubtotalProperty(): float
    {
        return ((float) $this->car->daily_price) * $this->days + $this->extrasCost;
    }

    public function getTaxesProperty(): float
    {
        $rate = (float) Setting::get('tax_rate', '0.08');
        if ($rate < 0) {
            $rate = 0.0;
        }
        // If admin saved as whole percentage (e.g., 8 or 10), normalize down to fraction
        if ($rate > 1.0) {
            $rate = $rate / 100.0;
        }

        return round($this->subtotal * $rate, 2);
    }

    public function getTotalProperty(): float
    {
        return round($this->subtotal + $this->taxes, 2);
    }

    public function getTaxRateProperty(): float
    {
        $rate = (float) Setting::get('tax_rate', '0.08');
        if ($rate < 0) {
            return 0.0;
        }
        if ($rate > 1.0) {
            $rate = $rate / 100.0;
        }

        return $rate;
    }

    protected function resetRentalForm(): void
    {
        // Reset all form fields to their defaults after a successful confirmation
        $this->pickupLocation = '';
        $this->dropoffLocation = '';
        $this->notes = '';
        // Reset extras to defaults from DB
        $this->loadAvailableExtras(true);
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
        if (! auth()->check()) {
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

        // Prevent banned users from making bookings
        if ($user && ! empty($user->banned_at)) {
            session()->flash('error', 'Your account has been banned. Please contact the administrator.');
            $this->redirect(route('cars.index'), navigate: true);

            return;
        }
        $editing = null;
        if ($this->booking && $user) {
            $editing = \App\Models\Booking::query()
                ->whereKey($this->booking)
                ->where('user_id', $user->id)
                ->where('car_id', $this->car->id)
                ->first();
        }

        // Determine service type and charge behavior
        $serviceType = ServiceType::find($this->serviceTypeId);
        $isNegotiable = $serviceType && strtolower((string) $serviceType->pricing_type) === 'negotiable';
        $newStatus = $isNegotiable ? 'pending' : 'confirmed';

        // Determine amount to charge (or refund) based on editing state
        $charge = 0.0;
        $refund = 0.0;
        if (! $isNegotiable) {
            if ($editing && $editing->status !== 'cancelled') {
                $diff = round($this->total - (float) $editing->total, 2);
                if ($diff > 0) {
                    $charge = $diff;
                }
                if ($diff < 0) {
                    $refund = abs($diff);
                }
            } else {
                $charge = round($this->total, 2);
            }
        }

        // If charge required, ensure balance is sufficient
        if ($charge > 0 && $user->wallet_balance < $charge) {
            session()->flash('wallet_error', 'Insufficient wallet balance. Please fund your wallet to complete the reservation.');
            $this->redirect(route('wallet.index'), navigate: true);

            return;
        }

        $booking = null;
        \Illuminate\Support\Facades\DB::transaction(function () use ($user, $editing, $charge, $refund, $newStatus, &$booking) {
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
                    'service_type_id' => $this->serviceTypeId,
                    'status' => $newStatus,
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
                    'service_type_id' => $this->serviceTypeId,
                    'status' => $newStatus,
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

        // Success message and redirect to My Trips
        $message = $newStatus === 'pending'
            ? "Your reservation request has been received. No payment has been taken yet. We'll contact you to confirm the price."
            : 'Your reservation request has been received!';
        session()->flash('rent_success', $message);
        $this->redirect(route('trips.index'), navigate: true);

    }

    protected function loadAvailableExtras(bool $resetSelection = false): void
    {
        $list = Extra::query()->where('is_active', true)->orderBy('name')->get(['name', 'price_per_day', 'default_selected'])->toArray();
        // Build selection keys from names (safe keys for Livewire binding)
        $selection = [];
        $avail = [];
        foreach ($list as $ex) {
            $name = (string) ($ex['name'] ?? '');
            if ($name === '') {
                continue;
            }
            $key = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', $name));
            $key = trim(preg_replace('/_+/', '_', $key), '_');
            $default = (bool) ($ex['default_selected'] ?? false);
            $selection[$key] = $default;
            $avail[] = [
                'key' => $key,
                'name' => $name,
                'price_per_day' => (float) ($ex['price_per_day'] ?? 0),
            ];
        }
        $this->availableExtras = $avail;
        if ($resetSelection || empty($this->extras)) {
            $this->extras = $selection;
        } else {
            // Merge existing selection with available extras (drop removed ones)
            $merged = [];
            foreach ($selection as $name => $def) {
                $merged[$name] = array_key_exists($name, $this->extras) ? (bool) $this->extras[$name] : $def;
            }
            $this->extras = $merged;
        }
    }

    public function render()
    {
        return view('livewire.rent-car');
    }
}
