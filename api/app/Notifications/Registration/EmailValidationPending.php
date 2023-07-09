<?php

namespace App\Notifications\Registration;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\VonageMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class EmailValidationPending extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        $this->onConnection('database');
        $this->onQueue('default');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(User $notifiable): array
    {
        return $notifiable->notify_sms ? ['vonage', 'mail', 'database'] : ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(User $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Email validation')
            ->line('Please validate your email to complete the registration.')
            ->action('Validate email here', $this->getMailActionUrl($notifiable));
    }

    public function getMailActionUrlParams(User $notifiable): array
    {
        return [
            'id' => $notifiable->id,
            'hash' => sha1($notifiable->email),
        ];
    }

    public function getMailActionUrl(User $notifiable)
    {
        return URL::signedRoute('validate_email', $this->getMailActionUrlParams($notifiable));
    }

    public function toVonage(User $notifiable): VonageMessage
    {
        return (new VonageMessage)
            ->clientReference((string) $notifiable->id)
            ->content('Please check your email to finish your register');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(User $notifiable): array
    {
        return [
            'subject' => 'Email validation',
            'message' => 'Please validate your email to complete the registration.',
        ];
    }
}
