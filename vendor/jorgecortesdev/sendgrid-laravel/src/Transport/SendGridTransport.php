<?php

declare(strict_types=1);

namespace JorgeCortesDev\SendGridLaravel\Transport;

use JorgeCortesDev\SendGridLaravel\Exceptions\SendEmailException;
use SendGrid\Client as SendGridClient;
use SendGrid\Mail\Mail;
use SendGrid\Mail\Subject;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Message;
use Symfony\Component\Mime\MessageConverter;

class SendGridTransport extends AbstractTransport
{
    public function __construct(
        protected SendGridClient $sendGrid,
    ) {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        /** @var Message $rawMessage */
        $rawMessage = $message->getOriginalMessage();
        $originalMessage = MessageConverter::toEmail($rawMessage);

        $email = new Mail;
        $email->setFrom($originalMessage->getFrom()[0]->getAddress(), $originalMessage->getFrom()[0]->getName());

        $subject = new Subject($originalMessage->getSubject());
        $email->setSubject($subject);

        collect($originalMessage->getTo())->each(function (Address $address) use ($email) {
            $email->addTo($address->getAddress(), $address->getName());
        });

        if ($originalMessage->getTextBody()) {
            $email->addContent('text/plain', (string) $originalMessage->getTextBody());
        }

        if ($originalMessage->getHtmlBody()) {
            $email->addContent('text/html', (string) $originalMessage->getHtmlBody());
        }

        // @phpstan-ignore-next-line
        $response = $this->sendGrid->mail()->send()->post($email);

        if ($response->statusCode() !== 202) {
            /** @var array{errors?: array<int, array{message: string}>} $data */
            $data = json_decode($response->body(), true, 512, JSON_THROW_ON_ERROR);

            if (! empty($data['errors'][0]['message'])) {
                throw SendEmailException::create((string) $data['errors'][0]['message'], $response->statusCode());
            }
        }
    }

    public function __toString(): string
    {
        return 'sendgrid';
    }
}
