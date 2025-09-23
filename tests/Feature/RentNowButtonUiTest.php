<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Car;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RentNowButtonUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_rent_now_button_is_elegant_and_accessible_on_cars_index(): void
    {
        $car = Car::factory()->create([
            'name' => 'Test Sedan',
            'daily_price' => 150_00, // formatted downstream
        ]);

        $response = $this->get(route('cars.index'));

        $response->assertOk();
        // Button text remains visible
        $response->assertSee('Rent Now', escape: false);
        // Flux button renders as a link with an accessible aria-label containing the car name
        $response->assertSee('aria-label="Rent Test Sedan now"', escape: false);
        // Link points to the proper rent route
        $response->assertSee(route('rent.show', $car), escape: false);
    }
}
