<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\RentCar;
use App\Models\Booking;
use App\Models\Car;
use App\Models\ServiceType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use Carbon\Carbon;

class BookingNegotiableFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_negotiable_service_type_creates_pending_booking_without_wallet_charge(): void
    {
        $user = User::factory()->create([
            'wallet_balance' => 10000,
        ]);

        $car = Car::factory()->create([
            'daily_price' => 5000,
            'is_active' => true,
        ]);

        $neg = ServiceType::create([
            'name' => 'Chauffeur',
            'pricing_type' => 'negotiable',
            'is_active' => true,
        ]);

        $start = Carbon::today()->toDateString();
        $end = Carbon::tomorrow()->toDateString();

        Livewire::actingAs($user)
            ->test(RentCar::class, ['car' => $car])
            ->set('pickupLocation', 'Ikeja')
            ->set('dropoffLocation', 'VI')
            ->set('startDate', $start)
            ->set('endDate', $end)
            ->set('serviceTypeId', $neg->id)
            ->set('agreedToDisclaimer', true)
            ->call('confirmRent')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('bookings', [
            'user_id' => $user->id,
            'car_id' => $car->id,
            'service_type_id' => $neg->id,
            'status' => 'pending',
        ]);

        $user->refresh();
        $this->assertSame(10000.0, (float) $user->wallet_balance, 'Wallet balance should not change for negotiable bookings');
    }

    public function test_fixed_service_type_also_creates_pending_booking_without_wallet_charge(): void
    {
        $user = User::factory()->create([
            'wallet_balance' => 10000,
        ]);

        $car = Car::factory()->create([
            'daily_price' => 5000,
            'is_active' => true,
        ]);

        $fixed = ServiceType::create([
            'name' => 'Self Drive',
            'pricing_type' => 'fixed',
            'is_active' => true,
        ]);

        Livewire::actingAs($user)
            ->test(RentCar::class, ['car' => $car])
            ->set('pickupLocation', 'Ikeja')
            ->set('dropoffLocation', 'VI')
            ->set('startDate', Carbon::today()->toDateString())
            ->set('endDate', Carbon::tomorrow()->toDateString())
            ->set('serviceTypeId', $fixed->id)
            ->set('agreedToDisclaimer', true)
            ->call('confirmRent')
            ->assertHasNoErrors()
            ->assertRedirect(route('trips.index'));

        $this->assertDatabaseHas('bookings', [
            'user_id' => $user->id,
            'car_id' => $car->id,
            'service_type_id' => $fixed->id,
            'status' => 'pending',
        ]);

        $user->refresh();
        $this->assertSame(10000.0, (float) $user->wallet_balance, 'Wallet balance should not change until an admin confirms the price');
    }
}
