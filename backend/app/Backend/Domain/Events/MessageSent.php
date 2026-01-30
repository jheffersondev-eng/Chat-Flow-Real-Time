<?php

namespace Backend\Domain\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $conversationId;

    public function __construct($message, $conversationId)
    {
        $this->message = $message;
        $this->conversationId = $conversationId;
    }

    public function broadcastOn()
    {
        return new Channel('chat.' . $this->conversationId);
    }

    public function broadcastAs()
    {
        return 'message.sent';
    }

    public function broadcastWith()
    {
        // Garante compatibilidade com o frontend
        return [
            'id' => $this->message->id,
            'content' => $this->message->content,
            'role' => $this->message->type === 'bot' ? 'assistant' : 'user',
            'timestamp' => $this->message->created_at,
            'user_id' => $this->message->user_id,
            'user' => $this->message->user,
        ];
    }
}
