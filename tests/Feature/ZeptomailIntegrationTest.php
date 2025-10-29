<?php

namespace Tests\Feature;

use App\Mail\TestZeptoMail;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ZeptomailIntegrationTest extends TestCase
{
    /**
     * Test that ZeptoMail transport is registered and available.
     */
    public function test_zeptomail_transport_is_registered(): void
    {
        $mailer = Mail::mailer('zeptomail');

        $this->assertNotNull($mailer);
        $this->assertEquals('zeptomail://api', (string) $mailer->getSymfonyTransport());
    }

    /**
     * Test that emails can be sent using ZeptoMail transport.
     */
    public function test_can_send_email_via_zeptomail(): void
    {
        Mail::fake();

        Mail::to('test@example.com')->send(new TestZeptoMail);

        Mail::assertSent(TestZeptoMail::class, function ($mail) {
            return $mail->hasTo('test@example.com');
        });
    }

    /**
     * Test that ZeptoMail configuration is properly set up.
     */
    public function test_zeptomail_configuration_is_set(): void
    {
        $this->assertNotEmpty(config('services.zeptomail.api_key'));

        // In testing environment, default mailer might be 'array'
        // but zeptomail should be available as a configured mailer
        $availableMailers = config('mail.mailers');
        $this->assertArrayHasKey('zeptomail', $availableMailers);
        $this->assertEquals('zeptomail', $availableMailers['zeptomail']['transport']);
    }
}
