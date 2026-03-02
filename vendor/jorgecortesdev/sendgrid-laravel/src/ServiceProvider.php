<?php

declare(strict_types=1);

namespace JorgeCortesDev\SendGridLaravel;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use JorgeCortesDev\SendGridLaravel\Exceptions\ApiKeyIsMissing;
use JorgeCortesDev\SendGridLaravel\Transport\SendGridTransport;
use SendGrid;
use SendGrid\Client as SendGridClient;

/**
 * @internal
 */
class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SendGridClient::class, static function (): SendGridClient {
            $apiKey = config('services.sendgrid.api_key');
            $options = (array) (config('services.sendgrid.options') ?? []);

            if (! is_string($apiKey)) {
                throw ApiKeyIsMissing::create();
            }

            $sg = new SendGrid($apiKey, $options);

            if (isset($options['data_residency'])) {
                $sg->setDataResidency($options['data_residency']);
            }

            return $sg->client;
        });

        $this->app->alias(SendGridClient::class, 'sendgrid');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Mail::extend('sendgrid', static function (array $config) {
            $sendGrid = app(SendGridClient::class);

            return new SendGridTransport($sendGrid);
        });
    }
}
