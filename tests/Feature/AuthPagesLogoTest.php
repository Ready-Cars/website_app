<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class AuthPagesLogoTest extends TestCase
{
    public function test_login_page_displays_logo_image(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertSee(asset('img.png'), escape: false);
    }
}
