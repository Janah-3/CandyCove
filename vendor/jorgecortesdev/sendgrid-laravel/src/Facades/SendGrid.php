<?php

declare(strict_types=1);

namespace JorgeCortesDev\SendGridLaravel\Facades;

use Illuminate\Support\Facades\Facade;
use SendGrid\Client;
use SendGrid\Response;

/**
 * @method string getHost()
 * @method Client setHost(string $host)
 * @method string[] getHeaders()
 * @method ?string getVersion()
 * @method string[] getPath()
 * @method mixed[] getCurlOptions()
 * @method Client setCurlOptions(mixed[] $options)
 * @method Client setRetryOnLimit(bool $retry)
 * @method Client setVerifySSLCerts(bool $verifySSLCerts)
 * @method Client setIsConcurrentRequest(bool $isConcurrent)
 * @method Response makeRequest(string $method, string $url, mixed[] $body = null, mixed[] $headers = null, bool $retryOnLimit = false)
 * @method Response[] makeAllRequests(mixed[] $requests = [])
 *
 * General
 * @method Client stats()
 * @method Client search()
 * @method Client monthly()
 * @method Client sums()
 * @method Client monitor()
 * @method Client test()
 *
 * Access settings
 * @method Client access_settings()
 * @method Client activity()
 * @method Client whitelist()
 *
 *  Alerts
 * @method Client alerts()
 *
 *  Api keys
 * @method Client api_keys()
 *
 * ASM
 * @method Client asm()
 * @method Client groups()
 * @method Client suppressions()
 *
 * Browsers
 * @method Client browsers()
 *
 * Campaigns
 * @method Client campaigns()
 * @method Client schedules()
 * @method Client now()
 *
 * Categories
 * @method Client categories()
 *
 * Clients
 * @method Client clients()
 *
 * Marketing
 * @method Client marketing()
 * @method Client contacts()
 * @method Client count()
 * @method Client exports()
 * @method Client imports()
 * @method Client lists()
 * @method Client field_definitions()
 * @method Client segments()
 * @method Client singlesends()
 *
 * Devices
 * @method Client devices()
 *
 * Geo
 * @method Client geo()
 *
 * Ips
 * @method Client ips()
 * @method Client assigned()
 * @method Client pools()
 * @method Client warmup()
 *
 * Mail
 * @method Client mail()
 * @method Client batch()
 *
 * Mailbox Providers
 * @method Client mailbox_providers()
 *
 * Mail settings
 * @method Client mail_settings()
 * @method Client address_whitelist()
 * @method Client bcc()
 * @method Client bounce_purge()
 * @method Client footer()
 * @method Client forward_bounce()
 * @method Client forward_spam()
 * @method Client plain_content()
 * @method Client spam_check()
 * @method Client template()
 *
 * Partner settings
 * @method Client partner_settings()
 * @method Client new_relic()
 *
 * Scopes
 * @method Client scopes()
 *
 * Senders
 * @method Client senders()
 * @method Client resend_verification()
 *
 * Sub Users
 * @method Client subusers()
 * @method Client reputations()
 *
 * Suppressions
 * @method Client suppression()
 * @method Client global()
 * @method Client blocks()
 * @method Client bounces()
 * @method Client invalid_emails()
 * @method Client spam_reports()
 * @method Client unsubscribes()
 *
 * Templates
 * @method Client templates()
 * @method Client versions()
 * @method Client activate()
 *
 * Tracking settings
 * @method Client tracking_settings()
 * @method Client click()
 * @method Client google_analytics()
 * @method Client open()
 * @method Client subscription()
 *
 * User
 * @method Client user()
 * @method Client account()
 * @method Client credits()
 * @method Client email()
 * @method Client password()
 * @method Client profile()
 * @method Client scheduled_sends()
 * @method Client enforced_tls()
 * @method Client settings()
 * @method Client username()
 * @method Client webhooks()
 * @method Client event()
 * @method Client parse()
 *
 * Missed any? Simply add them by doing: @method Client method()
 */
final class SendGrid extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'sendgrid';
    }
}
