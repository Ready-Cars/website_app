<?php

namespace Tests\Feature;

use App\Models\Car;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicSeoMetaTagsTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_renders_core_seo_meta_tags(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('<title>Premium Car Rentals in Nigeria - '.config('app.name').'</title>', false);
        $response->assertSee('<meta name="description" content="Book premium chauffeur-driven rentals with ReadyCars for airport pickups, corporate travel, and city rides across Nigeria." />', false);
        $response->assertSee('<meta name="robots" content="index,follow" />', false);
        $response->assertSee('<link rel="canonical" href="'.route('home').'" />', false);
        $response->assertSee('<meta property="og:type" content="website" />', false);
        $response->assertSee('<meta name="twitter:card" content="summary_large_image" />', false);
    }

    public function test_public_static_pages_have_canonical_and_indexable_meta_tags(): void
    {
        $this->get(route('cars.index'))
            ->assertOk()
            ->assertSee('<meta name="robots" content="index,follow" />', false)
            ->assertSee('<link rel="canonical" href="'.route('cars.index').'" />', false);

        $this->get(route('contact.index'))
            ->assertOk()
            ->assertSee('<meta name="robots" content="index,follow" />', false)
            ->assertSee('<link rel="canonical" href="'.route('contact.index').'" />', false);

        $this->get(route('terms.index'))
            ->assertOk()
            ->assertSee('<meta name="robots" content="index,follow" />', false)
            ->assertSee('<link rel="canonical" href="'.route('terms.index').'" />', false);
    }

    public function test_rent_page_uses_car_specific_seo_tags(): void
    {
        $car = Car::factory()->create([
            'name' => 'Mercedes S-Class',
            'location' => 'Lagos',
            'description' => 'A premium executive car rental for business trips and airport transfers.',
            'image_url' => 'https://example.com/mercedes.jpg',
        ]);

        $response = $this->get(route('rent.show', $car));

        $response->assertOk();
        $response->assertSee('<title>Rent Mercedes S-Class - '.config('app.name').'</title>', false);
        $response->assertSee('<meta property="og:type" content="product" />', false);
        $response->assertSee('<meta property="og:image" content="https://example.com/mercedes.jpg" />', false);
        $response->assertSee('<link rel="canonical" href="'.route('rent.show', $car).'" />', false);
    }

    public function test_booking_result_page_is_not_indexed(): void
    {
        $response = $this->get(route('booking.result'));

        $response->assertOk();
        $response->assertSee('<meta name="robots" content="noindex,nofollow" />', false);
        $response->assertSee('<link rel="canonical" href="'.route('booking.result').'" />', false);
    }
}
