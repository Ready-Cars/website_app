<?php

namespace Tests\Feature;

use App\Livewire\MyTrips;
use App\Models\Booking;
use App\Models\Car;
use App\Models\User;
use App\Services\PaystackService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MyTripsPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_make_payment_button_shows_for_pending_payment_bookings(): void
    {
        $user = User::factory()->create();
        $car = Car::factory()->create();

        $pendingPaymentBooking = Booking::factory()->create([
            'user_id' => $user->id,
            'car_id' => $car->id,
            'status' => 'pending payment',
            'total' => 100.00,
        ]);

        $confirmedBooking = Booking::factory()->create([
            'user_id' => $user->id,
            'car_id' => $car->id,
            'status' => 'confirmed',
            'total' => 100.00,
        ]);

        $component = Livewire::actingAs($user)->test(MyTrips::class);

        $component
            ->assertSee('Make Payment')
            ->assertDontSee('Download Receipt');
    }

    public function test_make_payment_initializes_paystack_payment(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com']);
        $car = Car::factory()->create();

        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'car_id' => $car->id,
            'status' => 'pending payment',
            'total' => 150.00,
        ]);

        // Mock PaystackService
        $this->mock(PaystackService::class, function ($mock) {
            $mock->shouldReceive('initialize')
                ->once()
                ->with(
                    15000, // 150.00 * 100 kobo
                    'test@example.com',
                    route('booking.payment.callback'),
                    [
                        'booking_id' => 1,
                        'user_id' => 1,
                    ]
                )
                ->andReturn([
                    'authorization_url' => 'https://checkout.paystack.com/test123',
                    'reference' => 'REF_TEST_123',
                ]);
        });

        $component = Livewire::actingAs($user)->test(MyTrips::class);

        $component->call('makePayment', $booking->id)
            ->assertRedirect('https://checkout.paystack.com/test123');

        // Assert payment reference was stored
        $booking->refresh();
        $this->assertEquals('REF_TEST_123', $booking->payment_reference);
    }

    public function test_make_payment_fails_for_non_pending_payment_booking(): void
    {
        $user = User::factory()->create();
        $car = Car::factory()->create();

        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'car_id' => $car->id,
            'status' => 'confirmed',
            'total' => 100.00,
        ]);

        $component = Livewire::actingAs($user)->test(MyTrips::class);

        $component->call('makePayment', $booking->id)
            ->assertHasErrors(['error' => 'Booking not found or not eligible for payment.']);
    }

    public function test_make_payment_fails_for_other_users_booking(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $car = Car::factory()->create();

        $booking = Booking::factory()->create([
            'user_id' => $user2->id,
            'car_id' => $car->id,
            'status' => 'pending payment',
            'total' => 100.00,
        ]);

        $component = Livewire::actingAs($user1)->test(MyTrips::class);

        $component->call('makePayment', $booking->id)
            ->assertHasErrors(['error' => 'Booking not found or not eligible for payment.']);
    }
}
