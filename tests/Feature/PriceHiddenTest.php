<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\CarsHome;
use App\Livewire\CarsIndex;
use App\Livewire\RentCar;
use App\Models\Car;
use App\Models\Extra;
use App\Models\ServiceType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PriceHiddenTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_page_shows_no_prices(): void
    {
        $user = User::factory()->create();
        $car = Car::factory()->create(['daily_price' => 5000, 'is_active' => true]);
        $service = ServiceType::create(['name' => 'Self Drive', 'pricing_type' => 'fixed', 'is_active' => true]);
        Extra::create(['name' => 'Child Seat', 'price_per_day' => 1500, 'is_active' => true]);

        Livewire::actingAs($user)
            ->test(RentCar::class, ['car' => $car])
            ->set('serviceTypeId', $service->id)
            ->call('openConfirm')
            ->assertSee('Booking summary')
            ->assertSee('Child Seat')
            ->assertDontSee('₦');
    }

    public function test_car_catalog_shows_no_prices(): void
    {
        Car::factory()->create(['name' => 'Toyota Prado', 'daily_price' => 5000, 'is_active' => true]);

        Livewire::test(CarsIndex::class)
            ->assertSee('Toyota Prado')
            ->assertDontSee('₦');
    }

    public function test_home_banner_rotates_price_led_offers(): void
    {
        Car::factory()->create(['name' => 'Lexus GX460', 'daily_price' => 150000, 'is_active' => true]);
        Car::factory()->create(['name' => 'Toyota Prado', 'daily_price' => 90000, 'is_active' => true]);
        Car::factory()->create(['name' => 'Hidden Car', 'daily_price' => 70000, 'is_active' => false]);

        Livewire::test(CarsHome::class)
            ->assertSeeInOrder(['Rent a Lexus GX460', '₦150,000'])
            ->assertSeeInOrder(['Rent a Toyota Prado', '₦90,000'])
            ->assertSee('T&amp;Cs Apply', false)
            ->assertDontSee('₦70,000');
    }
}
