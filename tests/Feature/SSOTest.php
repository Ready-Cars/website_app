<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SSOTest extends TestCase
{
    use RefreshDatabase;

    public function test_remote_login_authenticates_user_with_valid_signature()
    {
        $email = 'appuser@example.com';
        $name = 'App User';
        $secret = env('SSO_SECRET');
        $sig = hash('sha256', $email . $secret);

        $response = $this->get("/auth/remote-login?email=$email&name=$name&sig=$sig");

        $response->assertRedirect('/cars');
        $this->assertAuthenticated();
        $this->assertEquals(true, session('is_from_app'));
        
        $user = User::where('email', $email)->first();
        $this->assertNotNull($user);
        $this->assertEquals($name, $user->name);
    }

    public function test_remote_login_fails_with_invalid_signature()
    {
        $email = 'appuser@example.com';
        $name = 'App User';
        $sig = 'invalid_signature';

        $response = $this->get("/auth/remote-login?email=$email&name=$name&sig=$sig");

        $response->assertStatus(403);
        $this->assertGuest();
    }
}
