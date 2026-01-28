<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Events\MessageSent;
use App\Events\UserTyping;
use App\Jobs\ProcessBotResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Send a message
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'conversation_id' => 'required|string',
        ]);

        $user = Auth::user();
        
        \Log::info('Message received', [
            'user_id' => $user->id,
            'content' => $request->content,
            'conversation_id' => $request->conversation_id
        ]);
        
        $message = [
            'id' => uniqid(),
            'content' => $request->content,
            'role' => 'user',
        ];

        // Don't broadcast user message - frontend already adds it locally
        // broadcast(new MessageSent($message, $user, $request->conversation_id));

        // Get conversation history (simplified - replace with actual DB query)
        $conversationHistory = [];

        // Dispatch job to process AI response
        \Log::info('Dispatching ProcessBotResponse job');
        ProcessBotResponse::dispatch(
            $request->conversation_id,
            $request->content,
            $user,
            $conversationHistory
        );

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    /**
     * User is typing
     */
    public function typing(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|string',
            'is_typing' => 'required|boolean',
        ]);

        $user = Auth::user();

        broadcast(new UserTyping($user, $request->conversation_id, $request->is_typing))->toOthers();

        return response()->json(['success' => true]);
    }

    /**
     * Get conversations
     */
    public function getConversations()
    {
        // Simplified - return mock data
        return response()->json([
            'conversations' => [
                [
                    'id' => '1',
                    'title' => 'Welcome Chat',
                    'last_message' => 'How can I help you today?',
                    'timestamp' => now()->toISOString(),
                ],
            ],
        ]);
    }

    /**
     * Get messages for a conversation
     */
    public function getMessages($conversationId)
    {
        // Simplified - return mock data
        return response()->json([
            'messages' => [],
        ]);
    }
}
