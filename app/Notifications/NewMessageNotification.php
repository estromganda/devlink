<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewMessageNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Message $message,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        $sender = $this->message->sender;
        $conversation = $this->message->conversation;

        return [
            'type' => 'new_message',
            'title' => 'Nouveau message',
            'message' => "{$sender->name} vous a envoyé un message.",
            'conversation_id' => $conversation->id,
            'message_id' => $this->message->id,
            'actor_id' => $sender->id,
            'actor_name' => $sender->name,
            'url' => "/messages/{$conversation->id}",
        ];
    }
}
