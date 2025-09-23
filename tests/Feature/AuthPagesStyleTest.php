<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class AuthPagesStyleTest extends TestCase
{
    public function test_login_page_uses_dark_brand_side_background(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertSee('bg-[#0e1133]', escape: false);
    }
}
