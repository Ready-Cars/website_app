<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Cars as AdminCars;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CarsSaveConfirmUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_open_and_close_save_confirmation_modal_state(): void
    {
        Livewire::test(AdminCars::class)
            ->call('openSaveConfirm')
            ->assertSet('saveConfirmOpen', true)
            ->call('closeSaveConfirm')
            ->assertSet('saveConfirmOpen', false);
    }
}
