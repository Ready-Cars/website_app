<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HeaderLogoTest extends TestCase
{
    use RefreshDatabase;

    public function test_header_displays_logo_image_and_dark_navbar(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('https://readycars.ng/img/logo.png', escape: false);
        // Basic check for the dark navbar class
        $response->assertSee('bg-[#0e1133]', escape: false);
    }
}
