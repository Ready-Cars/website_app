<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\RentCar;
use App\Models\Car;
use App\Models\ServiceType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BookingButtonLoadingTest extends TestCase
{
    use RefreshDatabase;

    public function test_confirm_button_shows_loading_state_and_is_disabled(): void
    {
        // Create test data
        $user = User::factory()->create(['wallet_balance' => 10000]);
        $car = Car::factory()->create(['daily_price' => 5000]);
        $serviceType = ServiceType::create([
            'name' => 'Standard Service',
            'pricing_type' => 'fixed',
        ]);

        // Act as authenticated user
        $this->actingAs($user);

        // Test the rent car component
        $component = Livewire::test(RentCar::class, ['car' => $car])
            ->set('pickupLocation', 'Lagos')
            ->set('dropoffLocation', 'Abuja')
            ->set('startDate', now()->addDay()->format('Y-m-d'))
            ->set('endDate', now()->addDays(2)->format('Y-m-d'))
            ->set('serviceTypeId', $serviceType->id)
            ->call('openConfirm');

        // Verify the confirm button contains loading attributes
        $component->assertSee('wire:loading.attr="disabled"')
            ->assertSee('wire:target="confirmRent"')
            ->assertSee('wire:loading.remove')
            ->assertSee('wire:loading')
            ->assertSee('Processing...')
            ->assertSee('disabled:opacity-50')
            ->assertSee('disabled:cursor-not-allowed');
    }

    public function test_cancel_button_is_also_disabled_during_loading(): void
    {
        // Create test data
        $user = User::factory()->create(['wallet_balance' => 10000]);
        $car = Car::factory()->create(['daily_price' => 5000]);
        $serviceType = ServiceType::create([
            'name' => 'Standard Service',
            'pricing_type' => 'fixed',
        ]);

        // Act as authenticated user
        $this->actingAs($user);

        // Test the rent car component
        $component = Livewire::test(RentCar::class, ['car' => $car])
            ->set('pickupLocation', 'Lagos')
            ->set('dropoffLocation', 'Abuja')
            ->set('startDate', now()->addDay()->format('Y-m-d'))
            ->set('endDate', now()->addDays(2)->format('Y-m-d'))
            ->set('serviceTypeId', $serviceType->id)
            ->call('openConfirm');

        // Verify the cancel button also has loading attributes
        $component->assertSee('wire:loading.attr="disabled" wire:target="confirmRent"');
    }

    public function test_button_contains_spinner_animation(): void
    {
        // Create test data
        $user = User::factory()->create(['wallet_balance' => 10000]);
        $car = Car::factory()->create(['daily_price' => 5000]);
        $serviceType = ServiceType::create([
            'name' => 'Standard Service',
            'pricing_type' => 'fixed',
        ]);

        // Act as authenticated user
        $this->actingAs($user);

        // Test the rent car component
        $component = Livewire::test(RentCar::class, ['car' => $car])
            ->set('pickupLocation', 'Lagos')
            ->set('dropoffLocation', 'Abuja')
            ->set('startDate', now()->addDay()->format('Y-m-d'))
            ->set('endDate', now()->addDays(2)->format('Y-m-d'))
            ->set('serviceTypeId', $serviceType->id)
            ->call('openConfirm');

        // Verify the spinner SVG and animation classes are present
        $component->assertSee('animate-spin')
            ->assertSee('svg')
            ->assertSee('viewBox="0 0 24 24"')
            ->assertSee('circle')
            ->assertSee('path');
    }
}
