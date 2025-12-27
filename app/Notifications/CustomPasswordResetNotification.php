<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class CustomPasswordResetNotification extends ResetPassword
{
    /**
     * Build the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        $firstName = explode(' ', $notifiable->name ?? $notifiable->email)[0];

        return (new MailMessage)
            ->subject('Reset your AthleteGum password')
            ->view('emails.password-reset', [
                'firstName' => $firstName,
                'resetUrl' => $resetUrl,
            ]);
    }
}
