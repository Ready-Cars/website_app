<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\RentCar;
use App\Models\Car;
use App\Models\ServiceType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class NegotiablePricingDisplayTest extends TestCase
{
    use RefreshDatabase;

    public function test_negotiable_service_type_displays_to_be_determined(): void
    {
        $user = User::factory()->create();

        $car = Car::factory()->create([
            'daily_price' => 5000,
            'is_active' => true,
        ]);

        $negotiableService = ServiceType::create([
            'name' => 'Chauffeur Service',
            'pricing_type' => 'negotiable',
            'is_active' => true,
        ]);

        $regularService = ServiceType::create([
            'name' => 'Regular Service',
            'pricing_type' => 'fixed',
            'is_active' => true,
        ]);

        $start = Carbon::today()->toDateString();
        $end = Carbon::tomorrow()->toDateString();

        // Test with negotiable service type
        Livewire::actingAs($user)
            ->test(RentCar::class, ['car' => $car])
            ->set('pickupLocation', 'Ikeja')
            ->set('dropoffLocation', 'VI')
            ->set('startDate', $start)
            ->set('endDate', $end)
            ->set('serviceTypeId', $negotiableService->id)
            ->assertSee('To be Determined')
            ->assertDontSee('₦5,000.00');

        // Test with regular service type
        Livewire::actingAs($user)
            ->test(RentCar::class, ['car' => $car])
            ->set('pickupLocation', 'Ikeja')
            ->set('dropoffLocation', 'VI')
            ->set('startDate', $start)
            ->set('endDate', $end)
            ->set('serviceTypeId', $regularService->id)
            ->assertDontSee('To be Determined')
            ->assertSee('₦');
    }

    public function test_is_negotiable_service_property_works_correctly(): void
    {
        $user = User::factory()->create();
        $car = Car::factory()->create(['is_active' => true]);

        $negotiableService = ServiceType::create([
            'name' => 'Negotiable Service',
            'pricing_type' => 'negotiable',
            'is_active' => true,
        ]);

        $regularService = ServiceType::create([
            'name' => 'Regular Service',
            'pricing_type' => 'fixed',
            'is_active' => true,
        ]);

        $component = Livewire::actingAs($user)
            ->test(RentCar::class, ['car' => $car])
            ->set('serviceTypeId', $negotiableService->id);

        $this->assertTrue($component->instance()->isNegotiableService);

        $component->set('serviceTypeId', $regularService->id);
        $this->assertFalse($component->instance()->isNegotiableService);

        $component->set('serviceTypeId', 0);
        $this->assertFalse($component->instance()->isNegotiableService);
    }
}
