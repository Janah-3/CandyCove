<?php

namespace Tests\Transport;

use Illuminate\Mail\MailManager;
use JorgeCortesDev\SendGridLaravel\Transport\SendGridTransport;
use Tests\TestCase;

class SendGridTransportTest extends TestCase
{
    public function test_get_sendgrid_transport(): void
    {
        config()->set('mail.mailers.sendgrid', [
            'transport' => 'sendgrid',
        ]);

        config()->set('services.sendgrid', [
            'api_key' => 'test-api-key',
        ]);

        $mailManager = $this->app->make(MailManager::class);

        $mailer = $mailManager->mailer('sendgrid');

        $transport = $mailer->getSymfonyTransport();

        $this->assertInstanceOf(SendGridTransport::class, $transport);
    }
}
