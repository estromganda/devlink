<?php

namespace App\Notifications;

use App\Models\Answer;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AnswerAcceptedNotification extends Notification
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

        return [
            'type' => 'answer_accepted',
            'title' => 'Votre réponse a été acceptée',
            'message' => "Votre réponse à « {$question->title} » a été acceptée.",
            'question_id' => $question->id,
            'question_title' => $question->title,
            'answer_id' => $this->answer->id,
            'actor_id' => $question->user_id,
            'url' => "/questions/{$question->id}",
        ];
    }
}
