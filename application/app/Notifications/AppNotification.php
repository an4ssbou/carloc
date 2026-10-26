<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppNotification extends Notification
{
    use Queueable;

    protected $details;

    public function __construct($details)
    {
        $this->details = $details;
    }


    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject($this->details['subject'])
                    ->view('emails.notification', [
                        'subject' => $this->details['subject'],
                        'body' => $this->details['body'],
                        'actionUrl' => url($this->details['url']),
                        'actionText' => $this->details['action_text'] ?? 'Voir les details',
                        'recipientName' => $notifiable->full_name ?? $notifiable->name ?? $notifiable->email ?? 'client',
                    ]);
    }


    public function toArray($notifiable)
    {
        return [
            'title' => $this->details['subject'],
            'message' => $this->details['body'],
            'url' => $this->details['url'],
        ];
    }
}
