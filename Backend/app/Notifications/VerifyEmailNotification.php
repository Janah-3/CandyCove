<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailNotification extends VerifyEmail
{
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        // Replace backend URL with frontend URL
        $verificationUrl = str_replace(
            env('APP_URL'),
            env('FRONTEND_URL'),
            $verificationUrl
        );

        return (new MailMessage)
            ->subject('Verify Your CandyCove Email 🍬')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Please click the button below to verify your email address.')
            ->action('Verify Email', $verificationUrl)
            ->line('If you did not create an account, ignore this email.');
    }
}