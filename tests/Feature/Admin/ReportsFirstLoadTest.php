<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Reports;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ReportsFirstLoadTest extends TestCase
{
    use RefreshDatabase;

    public function test_reports_component_renders_successfully_on_first_load(): void
    {
        Livewire::test(Reports::class)
            ->assertStatus(200)
            ->assertSee('Total Bookings')
            ->assertSee('Top Cars by Bookings');
    }
}
