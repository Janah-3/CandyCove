<?php

namespace Tests\Facades;

use JorgeCortesDev\SendGridLaravel\Facades\SendGrid;
use JorgeCortesDev\SendGridLaravel\ServiceProvider;
use Tests\TestCase;

class SendGridTest extends TestCase
{
    public function test_resolves_resources(): void
    {
        config()->set('services.sendgrid', [
            'api_key' => 'test-api-key',
        ]);

        (new ServiceProvider(app()))->register();

        SendGrid::setFacadeApplication(app());

        $client = SendGrid::marketing();

        $this->assertEquals('marketing', $client->getPath()[0]);
    }
}
