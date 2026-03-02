<?php

namespace JorgeCortesDev\SendGridLaravel\Exceptions;

use InvalidArgumentException;

class ApiKeyIsMissing extends InvalidArgumentException
{
    public static function create(): self
    {
        return new self(
            'The SendGrid API key is missing.'
        );
    }
}
