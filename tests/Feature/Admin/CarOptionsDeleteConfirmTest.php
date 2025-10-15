<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Livewire\Admin\CarOptions;
use App\Models\CarAttributeOption;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CarOptionsDeleteConfirmTest extends TestCase
{
    use RefreshDatabase;

    public function test_delete_requires_confirmation_and_deletes_on_confirm(): void
    {
        // Seed a category option
        $opt = CarAttributeOption::create([
            'type' => 'category',
            'value' => 'TestCategory',
        ]);

        // Load component and locate index of the created option
        $component = Livewire::test(CarOptions::class)->set('tab', 'categories');

        $categories = $component->get('categories');
        $index = null;
        foreach ($categories as $i => $row) {
            if ((int) ($row['id'] ?? 0) === $opt->id) {
                $index = $i;
                break;
            }
        }
        $this->assertNotNull($index, 'Seeded category option not found in component state');

        // Request delete should open confirmation modal state
        $component->call('requestDelete', 'categories', $index)
            ->assertSet('deleteConfirmOpen', true)
            ->assertSet('deleteType', 'categories')
            ->assertSet('deleteIndex', $index);

        // Confirm delete and ensure the record is removed
        $component->call('confirmDelete')
            ->assertSet('deleteConfirmOpen', false);

        $this->assertDatabaseMissing('car_attribute_options', [
            'id' => $opt->id,
        ]);
    }
}
