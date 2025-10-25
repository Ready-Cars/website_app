<?php

namespace Tests\Feature;

use Tests\TestCase;

class TermsAndConditionsTest extends TestCase
{
    public function test_terms_page_loads_successfully(): void
    {
        $response = $this->get('/terms');

        $response->assertStatus(200);
    }

    public function test_terms_page_contains_expected_content(): void
    {
        $response = $this->get('/terms');

        $response->assertSee('Terms and Conditions');
        $response->assertSee('Car Rental Service Agreement');
        $response->assertSee('Agreement Overview');
        $response->assertSee('Renter Eligibility');
        $response->assertSee('Insurance and Liability');
        $response->assertSee('Back to Home');
    }

    public function test_terms_page_has_correct_title(): void
    {
        $response = $this->get('/terms');

        $response->assertSee('<title>Terms and Conditions - '.config('app.name').'</title>', false);
    }

    public function test_terms_route_is_named_correctly(): void
    {
        $url = route('terms.index');

        $this->assertEquals('/terms', $url);
    }

    public function test_home_page_contains_navigation_to_terms(): void
    {
        $response = $this->get('/');

        $response->assertSee('Terms & Conditions');
    }
}
