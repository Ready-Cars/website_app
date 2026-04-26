<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Register extends Component
{
    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $password = '';

    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['required', 'string', 'max:32', 'regex:/^[0-9+()\-\s]+$/', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered(($user = User::create($validated))));

        Auth::login($user);

        // Process any pending bookings cached from a guest session
        if ($pending = \Illuminate\Support\Facades\Session::pull('pending_booking')) {
            $this->processPendingBooking($pending);
            // After processing a pending booking, redirect specifically to the car page
            $this->redirect(route('rent.show', ['car' => $pending['car_id']]), navigate: true);
            return;
        }

        $this->redirectIntended(route('dashboard', absolute: false), navigate: true);
    }

    /**
     * Process a pending booking cached in the session during a guest interaction.
     */
    protected function processPendingBooking(array $data): void
    {
        try {
            $user = \Illuminate\Support\Facades\Auth::user();
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

            session()->flash('rent_success', 'Welcome! Your pending booking was processed successfully.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to process pending registration booking: ' . $e->getMessage());
        }
    }
}
