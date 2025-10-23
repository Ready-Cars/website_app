<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Cars as AdminCars;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CarsUploadUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_add_car_modal_shows_improved_file_pickers(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin);

        Livewire::test(AdminCars::class)
            ->call('openCreate')
            ->call('openCreate')
            ->assertSee('Click to upload')
            ->assertSee('Additional images');
    }
}
