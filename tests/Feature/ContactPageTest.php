<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ContactPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_page_loads_successfully(): void
    {
        $response = $this->get(route('contact.index'));

        $response->assertStatus(200);
        $response->assertSee('Contact Us');
        $response->assertSeeLivewire('contact');
    }

    public function test_contact_page_displays_contact_information(): void
    {
        // Set up contact information
        Setting::setValue('contact_email', 'test@example.com');
        Setting::setValue('contact_phone', '+1 (555) 123-4567');
        Setting::setValue('contact_address', '123 Main St, City, State');
        Setting::setValue('contact_description', 'We are here to help you');

        $response = $this->get(route('contact.index'));

        $response->assertSee('test@example.com');
        $response->assertSee('+1 (555) 123-4567');
        $response->assertSee('123 Main St, City, State');
        $response->assertSee('We are here to help you');
    }

    public function test_contact_page_shows_empty_message_when_no_info(): void
    {
        $response = $this->get(route('contact.index'));

        $response->assertSee('Contact information is being updated');
    }

    public function test_admin_can_access_settings_page(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get(route('admin.settings'))
            ->assertStatus(200)
            ->assertSee('Contact Information');
    }

    public function test_admin_can_update_contact_information(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($admin)
            ->test('admin.settings')
            ->set('contactEmail', 'admin@example.com')
            ->set('contactPhone', '+1 (555) 987-6543')
            ->set('contactAddress', '456 Admin St, Admin City')
            ->set('contactDescription', 'Updated contact description')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSee('Settings saved successfully');

        $this->assertEquals('admin@example.com', Setting::get('contact_email'));
        $this->assertEquals('+1 (555) 987-6543', Setting::get('contact_phone'));
        $this->assertEquals('456 Admin St, Admin City', Setting::get('contact_address'));
        $this->assertEquals('Updated contact description', Setting::get('contact_description'));
    }

    public function test_contact_form_validation(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($admin)
            ->test('admin.settings')
            ->set('contactEmail', 'invalid-email')
            ->call('save')
            ->assertHasErrors(['contactEmail']);
    }

    public function test_non_admin_cannot_access_admin_settings(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get(route('admin.settings'))
            ->assertStatus(302); // Laravel redirects unauthorized users
    }
}
