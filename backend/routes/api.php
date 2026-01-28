<?php

use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\FriendshipController;
use App\Http\Controllers\Api\ConversationController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'API funcionando com Laravel 10!',
        'websockets' => 'Rodando na porta 6001',
    ]);
});

Route::middleware('auth:sanctum')->group(function () {
    // Current user
    Route::get('/user', [UserController::class, 'me']);

    // User search
    Route::get('/users/search', [UserController::class, 'search']);

    // Friendships
    Route::post('/friendships', [FriendshipController::class, 'sendRequest']);
    Route::get('/friendships', [FriendshipController::class, 'index']);
    Route::get('/friendships/pending', [FriendshipController::class, 'pending']);
    Route::put('/friendships/{id}/accept', [FriendshipController::class, 'acceptRequest']);
    Route::delete('/friendships/{id}/reject', [FriendshipController::class, 'rejectRequest']);
    Route::delete('/friends/{friendId}', [FriendshipController::class, 'removeFriend']);

    // Conversations
    Route::get('/conversations', [ConversationController::class, 'index']);
    Route::get('/conversations/{id}', [ConversationController::class, 'show']);
    Route::post('/conversations/private', [ConversationController::class, 'getOrCreate']);
    Route::post('/conversations/ai', [ConversationController::class, 'createAIChat']);
    Route::get('/conversations/{id}/messages', [ConversationController::class, 'getMessages']);
    Route::post('/conversations/{id}/messages', [ConversationController::class, 'sendMessage']);
    Route::delete('/conversations/{id}', [ConversationController::class, 'destroy']);

    // Legacy chat routes (deprecated)
    Route::prefix('chat')->group(function () {
        Route::get('/conversations', [ChatController::class, 'getConversations']);
        Route::get('/conversations/{id}/messages', [ChatController::class, 'getMessages']);
        Route::post('/messages', [ChatController::class, 'sendMessage']);
        Route::post('/typing', [ChatController::class, 'typing']);
    });
});
