<?php

namespace Tests\Feature;

use App\Models\Car;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RentPageTypographyTest extends TestCase
{
    use RefreshDatabase;

    public function test_rent_page_uses_refined_heading_font_weight(): void
    {
        $car = Car::factory()->create([
            'name' => 'Volkswagen RS7',
            'daily_price' => 82.02,
        ]);

        $response = $this->get(route('rent.show', $car));

        $response->assertOk();
        $response->assertSee('text-3xl md:text-4xl font-semibold text-slate-900 tracking-tight', false);
        $response->assertSee('text-slate-600 font-normal', false);
    }
}
