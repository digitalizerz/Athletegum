<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CustomPasswordResetNotification extends ResetPassword implements ShouldQueue
{
    use Queueable;
    /**
     * Build the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        // Determine if this is an athlete or regular user based on the model class
        // Athletes use 'athlete.password.reset', regular users use 'password.reset'
        $routeName = get_class($notifiable) === \App\Models\Athlete::class 
            ? 'athlete.password.reset' 
            : 'password.reset';
            
        $resetUrl = url(route($routeName, [
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
