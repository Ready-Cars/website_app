<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\Admin\CarOptions;
use App\Livewire\RentCar;
use App\Models\Car;
use App\Models\ServiceType;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TaxRateConfigTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_set_tax_rate_and_rent_flow_uses_it(): void
    {
        // Given a car and a service type
        $car = Car::factory()->create([
            'daily_price' => 100.00,
        ]);
        $serviceType = ServiceType::create([
            'name' => 'Standard',
            'pricing_type' => 'fixed',
            'is_active' => true,
        ]);

        // When admin sets tax rate to 10%
        Livewire::test(CarOptions::class)
            ->set('tab', 'settings')
            ->set('taxRate', '10')
            ->call('saveTaxRate')
            ->assertSessionHas('success');

        $this->assertSame('0.1', Setting::get('tax_rate'));

        // And user opens rent page with 2 days and no extras
        $component = Livewire::test(RentCar::class, ['car' => $car])
            ->set('serviceTypeId', $serviceType->id)
            ->set('startDate', now()->toDateString())
            ->set('endDate', now()->addDays(2)->toDateString());

        // Subtotal = 100 * 2 = 200; taxes = 10% => 20; total = 220
        $component->assertSet('subtotal', 200.0);
        $this->assertEquals(20.0, $component->instance()->taxes);
        $this->assertEquals(220.0, $component->instance()->total);
    }
}
