<?php

namespace JorgeCortesDev\SendGridLaravel\Exceptions;

use RuntimeException;

class SendEmailException extends RuntimeException
{
    public static function create(string $message, int $code): self
    {
        return new self($message, $code);
    }
}
