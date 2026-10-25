<?php

use App\Models\ConversationMember;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel(
    'conversation.{conversationId}',
    function ($user, int $conversationId): bool {
        return ConversationMember::query()
            ->where('conversation_id', $conversationId)
            ->where('user_id', $user->id)
            ->whereNull('left_at')
            ->exists();
    }
);
