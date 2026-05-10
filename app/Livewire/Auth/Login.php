<?php

namespace App\Livewire\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Login extends Component
{
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        // Process any pending bookings cached from a guest session
        if ($pending = Session::pull('pending_booking')) {
            $this->processPendingBooking($pending);
            // After processing a pending booking, redirect specifically to the car page
            $this->redirect(route('rent.show', ['car' => $pending['car_id']]), navigate: true);
            return;
        }

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    /**
     * Process a pending booking cached in the session during a guest interaction.
     */
    protected function processPendingBooking(array $data): void
    {
        try {
            $user = Auth::user();
            $car = \App\Models\Car::findOrFail($data['car_id']);
            $serviceType = \App\Models\ServiceType::findOrFail($data['service_type_id']);

            // Calculate totals
            $start = \Carbon\Carbon::parse($data['start_date'])->startOfDay();
            $end = \Carbon\Carbon::parse($data['end_date'])->startOfDay();
            $days = max(1, $start->diffInDays($end));

            $subtotal = ((float) $car->daily_price) * $days;
            $taxes = round($subtotal * (float) \App\Models\Setting::get('tax_rate', '0.08'), 2);
            $total = round($subtotal + $taxes, 2);

            $isNegotiable = strtolower((string) $serviceType->pricing_type) === 'negotiable';
            $status = $isNegotiable ? 'pending' : 'confirmed';

            \App\Models\Booking::create([
                'user_id' => $user->id,
                'car_id' => $car->id,
                'pickup_location' => $data['pickup_location'],
                'dropoff_location' => $data['dropoff_location'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'extras' => $data['extras'],
                'notes' => $data['notes'],
                'subtotal' => $subtotal,
                'taxes' => $taxes,
                'total' => $total,
                'service_type_id' => $data['service_type_id'],
                'status' => $status,
            ]);

            session()->flash('rent_success', 'Welcome back! Your pending booking was processed successfully.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to process pending booking: ' . $e->getMessage());
        }
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
}
