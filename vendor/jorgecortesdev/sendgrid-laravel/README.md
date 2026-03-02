# Sendgrid Laravel

[![Latest Version](https://img.shields.io/packagist/v/jorgecortesdev/sendgrid-laravel.svg?style=flat-square)](https://packagist.org/packages/jorgecortesdev/sendgrid-laravel)
[![License](https://img.shields.io/packagist/l/jorgecortesdev/sendgrid-laravel?style=flat-square&link=LICENSE.md)](LICENSE.md)

A Laravel wrapper for the official [SendGrid PHP library](https://github.com/sendgrid/sendgrid-php), providing a simple and elegant way to send emails through SendGrid in your Laravel applications.

## 🚨 Note

> **IMPORTANT:** This package currently provides only the most basic functionality required to send emails using SendGrid. No advanced features or additional abstractions are included. Feel free to submit a PR if you'd like to extend the functionality!

## Installation

You can install the package via Composer:

```bash
composer require jorgecortesdev/sendgrid-laravel
```

## Configuration
it

Update your `.env` file with the SendGrid API key:

```env
SENDGRID_API_KEY=
```

Second, add the following configuration to the `config/services.php` file. The `data_residency` option can be either 'eu' or 'global':

```php
'sendgrid' => [
    'api_key' => env('SENDGRID_API_KEY'),
    'options' => [
        'data_residency' => 'global' 
    ],
],
```

Lastly, add the following configuration array to your array of `mailers` in the `config/mail.php` file:

```php
'sendgrid' => [
    'transport' => 'sendgrid',
],
```

## Usage

Update your `.env` file with the new mailer:

```
MAIL_MAILER=sendgrid
```
Don't forget to add a from address does match a verified Sender Identity.

```
MAIL_FROM_ADDRESS="john@allowed.domain"
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.