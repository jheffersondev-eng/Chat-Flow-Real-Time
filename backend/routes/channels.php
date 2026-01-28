<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
*/

Broadcast::channel('chat.{conversationId}', function ($user, $conversationId) {
    // Check if user is participant in conversation
    return true; // Simplificado, adicionar verificação real depois
});

Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

Broadcast::channel('typing.{conversationId}', function ($user, $conversationId) {
    return ['id' => $user->id, 'name' => $user->name];
});
