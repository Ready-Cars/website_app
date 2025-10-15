<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Customers;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CustomersBanConfirmTest extends TestCase
{
    use RefreshDatabase;

    public function test_ban_requires_confirmation_and_bans_on_confirm(): void
    {
        $user = User::factory()->create(['banned_at' => null]);

        Livewire::test(Customers::class)
            ->call('openBan', $user->id)
            ->assertSet('banOpen', true)
            ->assertSet('banUserId', $user->id)
            ->call('confirmBan')
            ->assertSet('banOpen', false)
            ->assertSet('banUserId', null);

        $this->assertNotNull(User::find($user->id)->banned_at);
    }
}
