<?php

namespace App\Notifications\Registration;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EmailValidated extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        $this->connection = 'database';
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'subject' => 'email validated!',
        ];
    }
}
