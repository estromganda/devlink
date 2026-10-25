<?php

namespace App\Notifications;

use App\Models\Answer;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewAnswerNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Answer $answer,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        $question = $this->answer->post;
        $author = $this->answer->user;

        return [
            'type' => 'new_answer',
            'title' => 'Nouvelle réponse à votre question',
            'message' => "{$author->name} a répondu à votre question.",
            'question_id' => $question->id,
            'question_title' => $question->title,
            'answer_id' => $this->answer->id,
            'actor_id' => $author->id,
            'actor_name' => $author->name,
            'url' => "/questions/{$question->id}",
        ];
    }
}
