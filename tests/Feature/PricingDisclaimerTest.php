<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PricingDisclaimerTest extends TestCase
{
    use RefreshDatabase;

    public function test_disclaimer_appears_on_first_visit(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test('pricing-disclaimer')
            ->assertSet('showDisclaimer', true)
            ->assertSee('Important Pricing Information')
            ->assertSee('All prices displayed on this website are for indication purposes only')
            ->assertSee('Our support staff will reach out to you after your booking');
    }

    public function test_disclaimer_can_be_accepted(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test('pricing-disclaimer')
            ->assertSet('showDisclaimer', true)
            ->call('acceptDisclaimer')
            ->assertSet('showDisclaimer', false);

        // Verify session was set
        $this->assertTrue(session('pricing_disclaimer_accepted'));
    }

    public function test_disclaimer_does_not_appear_after_acceptance(): void
    {
        $user = User::factory()->create();

        // Set session to indicate disclaimer was already accepted
        session(['pricing_disclaimer_accepted' => true]);

        $this->actingAs($user);

        Livewire::test('pricing-disclaimer')
            ->assertSet('showDisclaimer', false)
            ->assertDontSee('Important Pricing Information');
    }
}
