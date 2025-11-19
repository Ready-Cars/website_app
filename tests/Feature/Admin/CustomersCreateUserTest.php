<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Customers;
use App\Mail\UserCredentialsMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class CustomersCreateUserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an admin user for testing
        $this->admin = User::factory()->create([
            'is_admin' => true,
            'email' => 'admin@test.com',
        ]);
    }

    public function test_admin_can_open_create_user_modal(): void
    {
        Livewire::actingAs($this->admin)
            ->test(Customers::class)
            ->call('openCreateUser')
            ->assertSet('createUserOpen', true)
            ->assertSet('createName', '')
            ->assertSet('createEmail', '')
            ->assertSet('createPhone', '')
            ->assertSet('createRole', 'customer');
    }

    public function test_admin_can_close_create_user_modal(): void
    {
        Livewire::actingAs($this->admin)
            ->test(Customers::class)
            ->call('openCreateUser')
            ->call('closeCreateUser')
            ->assertSet('createUserOpen', false);
    }

    public function test_admin_can_create_customer_user(): void
    {
        Mail::fake();

        Livewire::actingAs($this->admin)
            ->test(Customers::class)
            ->set('createName', 'John Doe')
            ->set('createEmail', 'john@example.com')
            ->set('createPhone', '1234567890')
            ->set('createRole', 'customer')
            ->call('createUser')
            ->assertHasNoErrors()
            ->assertSet('createUserOpen', false);

        // Assert user was created
        $user = User::where('email', 'john@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertEquals('1234567890', $user->phone);
        $this->assertFalse($user->is_admin);
        $this->assertEquals(0.00, $user->wallet_balance);

        // Assert email was sent
        Mail::assertSent(UserCredentialsMail::class, function ($mail) use ($user) {
            return $mail->user->id === $user->id && $mail->role === 'customer';
        });
    }

    public function test_admin_can_create_admin_user(): void
    {
        Mail::fake();

        Livewire::actingAs($this->admin)
            ->test(Customers::class)
            ->set('createName', 'Jane Admin')
            ->set('createEmail', 'jane@example.com')
            ->set('createRole', 'admin')
            ->call('createUser')
            ->assertHasNoErrors();

        // Assert admin user was created
        $user = User::where('email', 'jane@example.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->is_admin);

        // Assert email was sent with admin role
        Mail::assertSent(UserCredentialsMail::class, function ($mail) use ($user) {
            return $mail->user->id === $user->id && $mail->role === 'admin';
        });
    }

    public function test_create_user_validates_required_fields(): void
    {
        Livewire::actingAs($this->admin)
            ->test(Customers::class)
            ->call('createUser')
            ->assertHasErrors([
                'createName' => 'required',
                'createEmail' => 'required',
            ]);
    }

    public function test_create_user_validates_email_format(): void
    {
        Livewire::actingAs($this->admin)
            ->test(Customers::class)
            ->set('createName', 'John Doe')
            ->set('createEmail', 'invalid-email')
            ->call('createUser')
            ->assertHasErrors(['createEmail' => 'email']);
    }

    public function test_create_user_validates_unique_email(): void
    {
        // Create existing user
        User::factory()->create(['email' => 'existing@example.com']);

        Livewire::actingAs($this->admin)
            ->test(Customers::class)
            ->set('createName', 'John Doe')
            ->set('createEmail', 'existing@example.com')
            ->call('createUser')
            ->assertHasErrors(['createEmail' => 'unique']);
    }

    public function test_create_user_validates_unique_phone(): void
    {
        // Create existing user with phone number
        User::factory()->create(['phone' => '07036725298']);

        Livewire::actingAs($this->admin)
            ->test(Customers::class)
            ->set('createName', 'John Doe')
            ->set('createEmail', 'john@example.com')
            ->set('createPhone', '07036725298')
            ->call('createUser')
            ->assertHasErrors(['createPhone' => 'unique']);
    }

    public function test_create_user_validates_role(): void
    {
        Livewire::actingAs($this->admin)
            ->test(Customers::class)
            ->set('createName', 'John Doe')
            ->set('createEmail', 'john@example.com')
            ->set('createRole', 'invalid-role')
            ->call('createUser')
            ->assertHasErrors(['createRole' => 'in']);
    }

    public function test_phone_number_is_optional(): void
    {
        Mail::fake();

        Livewire::actingAs($this->admin)
            ->test(Customers::class)
            ->set('createName', 'John Doe')
            ->set('createEmail', 'john@example.com')
            ->set('createPhone', '')
            ->set('createRole', 'customer')
            ->call('createUser')
            ->assertHasNoErrors();

        $user = User::where('email', 'john@example.com')->first();
        $this->assertNull($user->phone);
    }

    public function test_success_message_displayed_after_user_creation(): void
    {
        Mail::fake();

        $component = Livewire::actingAs($this->admin)
            ->test(Customers::class)
            ->set('createName', 'John Doe')
            ->set('createEmail', 'john@example.com')
            ->set('createRole', 'customer')
            ->call('createUser');

        $this->assertEquals(
            'User created successfully as customer. Credentials have been sent to their email.',
            session('success')
        );
    }

    public function test_warning_message_displayed_when_email_fails(): void
    {
        // Force email to fail by using invalid mail configuration
        Mail::shouldReceive('to')->andThrow(new \Exception('Mail server error'));

        $component = Livewire::actingAs($this->admin)
            ->test(Customers::class)
            ->set('createName', 'John Doe')
            ->set('createEmail', 'john@example.com')
            ->set('createRole', 'customer')
            ->call('createUser');

        // User should still be created
        $user = User::where('email', 'john@example.com')->first();
        $this->assertNotNull($user);

        // Warning message should be set
        $this->assertEquals(
            'User created but email sending failed. Please manually send credentials.',
            session('warning')
        );
    }
}
