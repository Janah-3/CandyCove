<?php

namespace Tests;

use InvalidArgumentException;
use JorgeCortesDev\SendGridLaravel\Exceptions\ApiKeyIsMissing;
use JorgeCortesDev\SendGridLaravel\Facades\SendGrid;
use JorgeCortesDev\SendGridLaravel\ServiceProvider;
use PHPUnit\Framework\Attributes\DataProvider;
use SendGrid\Client as SendGridClient;

class ServiceProviderTest extends TestCase
{
    public function test_binds_the_client_on_the_container(): void
    {
        config()->set('services.sendgrid', [
            'api_key' => 'test-api-key',
        ]);

        (new ServiceProvider(app()))->register();

        $this->assertInstanceOf(SendGridClient::class, app()->get(SendGridClient::class));
    }

    public function test_binds_the_client_on_the_container_as_singleton(): void
    {
        config()->set('services.sendgrid', [
            'api_key' => 'test-api-key',
        ]);

        (new ServiceProvider(app()))->register();

        $client = app()->get(SendGridClient::class);

        $this->assertEquals($client, app()->get(SendGridClient::class));
    }

    public function test_it_requires_an_api_key(): void
    {
        $this->expectException(ApiKeyIsMissing::class);

        (new ServiceProvider(app()))->register();

        app()->get(SendGridClient::class);
    }

    #[DataProvider('dataResidencyProvider')]
    public function test_valid_data_residency($expected, $value): void
    {
        config()->set('services.sendgrid', [
            'api_key' => 'test-api-key',
            'options' => [
                'data_residency' => $value,
            ],
        ]);

        (new ServiceProvider(app()))->register();

        $host = SendGrid::getHost();

        $this->assertEquals($expected, $host);
    }

    public function test_invalid_data_residency(): void
    {
        $this->expectException(InvalidArgumentException::class);

        config()->set('services.sendgrid', [
            'api_key' => 'test-api-key',
            'options' => [
                'data_residency' => 'mx',
            ],
        ]);

        (new ServiceProvider(app()))->register();

        SendGrid::getHost();
    }

    public static function dataResidencyProvider(): array
    {
        return [
            ['https://api.eu.sendgrid.com', 'eu'],
            ['https://api.sendgrid.com', 'global'],
        ];
    }
}
