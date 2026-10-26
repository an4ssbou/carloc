<?php

namespace App\Support;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class SafeNotifier
{
    public static function send(mixed $notifiable, Notification $notification, array $context = []): void
    {
        if (! $notifiable) {
            return;
        }

        try {
            $notifiable->notify($notification);
        } catch (\Throwable $e) {
            Log::warning('Notification delivery failed.', [
                'notifiable_type' => is_object($notifiable) ? get_class($notifiable) : gettype($notifiable),
                'notifiable_id' => is_object($notifiable) && isset($notifiable->id) ? $notifiable->id : null,
                'notification_type' => get_class($notification),
                'exception_type' => $e::class,
                'event' => $context['event'] ?? 'notification_delivery',
            ]);
        }
    }
}
