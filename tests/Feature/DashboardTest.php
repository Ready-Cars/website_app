<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_authenticated_customers_cannot_visit_the_dashboard(): void
    {
        $this->actingAs($user = User::factory()->create());

        $this->get('/dashboard')->assertStatus(403);
    }

    public function test_authenticated_admins_can_visit_the_dashboard(): void
    {
        $this->actingAs($admin = User::factory()->admin()->create());

        $this->get('/dashboard')->assertStatus(200);
    }
}
