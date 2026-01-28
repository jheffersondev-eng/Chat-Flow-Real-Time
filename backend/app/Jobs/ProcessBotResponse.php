<?php

namespace App\Jobs;

use App\Events\MessageSent;
use App\Services\LLMBotService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessBotResponse implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $conversationId;
    public $userMessage;
    public $user;
    public $conversationHistory;

    public function __construct($conversationId, $userMessage, $user, $conversationHistory = [])
    {
        $this->conversationId = $conversationId;
        $this->userMessage = $userMessage;
        $this->user = $user;
        $this->conversationHistory = $conversationHistory;
    }

    public function handle(LLMBotService $botService)
    {
        try {
            \Log::info('ProcessBotResponse started', ['conversation_id' => $this->conversationId]);
            
            // Simulate typing delay for better UX
            sleep(1);
            
            // Generate AI response
            $aiResponse = $botService->generateResponse(
                $this->userMessage,
                $this->conversationHistory
            );

            \Log::info('AI Response generated', ['response' => substr($aiResponse, 0, 100)]);

            // Create AI message in database
            $message = \App\Models\Message::create([
                'conversation_id' => $this->conversationId,
                'user_id' => null, // AI has no user_id
                'content' => $aiResponse,
                'type' => 'bot',
            ]);

            // Broadcast AI response
            broadcast(new MessageSent($message->load('user'), $this->conversationId));
            
            \Log::info('AI Response broadcasted');
        } catch (\Exception $e) {
            \Log::error('ProcessBotResponse error: ' . $e->getMessage());
            throw $e;
        }
    }
}
