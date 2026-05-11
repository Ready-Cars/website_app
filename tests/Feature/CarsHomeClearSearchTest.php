<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarsHomeClearSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_clear_search_action_is_hidden_without_active_filters(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertDontSee('Clear search', escape: false);
    }

    public function test_clear_search_action_is_visible_when_filters_are_active(): void
    {
        $response = $this->get(route('home', [
            'location' => 'Carsonmouth',
        ]));

        $response->assertOk();
        $response->assertSee('Clear search', escape: false);
    }
}
