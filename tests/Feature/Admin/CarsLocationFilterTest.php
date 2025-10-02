<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Cars as AdminCars;
use App\Models\Car;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CarsLocationFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_filter_cars_by_location_dropdown(): void
    {
        // Arrange: create cars in different locations
        $lagosCar = Car::factory()->create([
            'name' => 'Lagos Cruiser',
            'location' => 'Lagos',
            'is_active' => true,
        ]);
        $abujaCar = Car::factory()->create([
            'name' => 'Abuja Rider',
            'location' => 'Abuja',
            'is_active' => true,
        ]);

        // Act & Assert: selecting Lagos should show Lagos car and not Abuja car
        Livewire::test(AdminCars::class)
            ->set('showAdvanced', true)
            ->set('locationFilter', 'Lagos')
            ->assertStatus(200)
            ->assertSee('Lagos Cruiser')
            ->assertDontSee('Abuja Rider');

        // And selecting Abuja should show Abuja car and not Lagos car
        Livewire::test(AdminCars::class)
            ->set('showAdvanced', true)
            ->set('locationFilter', 'Abuja')
            ->assertStatus(200)
            ->assertSee('Abuja Rider')
            ->assertDontSee('Lagos Cruiser');
    }
}
