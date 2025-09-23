<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HeaderStickyTest extends TestCase
{
    use RefreshDatabase;

    public function test_navbar_is_sticky_on_home_page(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        // Ensure the header contains sticky positioning classes
        $response->assertSee('sticky top-0 z-50', escape: false);
    }
}
