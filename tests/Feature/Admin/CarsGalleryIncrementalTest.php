<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Cars as AdminCars;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\TestCase;

class CarsGalleryIncrementalTest extends TestCase
{
    use RefreshDatabase;

    public function test_selecting_gallery_images_incrementally_appends_to_selection(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        $a = UploadedFile::fake()->image('a.jpg', 600, 400);
        $b = UploadedFile::fake()->image('b.jpg', 600, 400);

        $lw = Livewire::test(AdminCars::class)
            ->call('openCreate')
            // First pick
            ->set('newGalleryUpload', $a)
            // Second pick should append, not replace
            ->set('newGalleryUpload', $b);

        $items = $lw->get('galleryUploads');
        $this->assertIsArray($items);
        $this->assertCount(2, $items);
    }
}
