<?php

namespace Backend\Domain\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserTyping implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $conversationId;
    public $isTyping;

    public function __construct($user, $conversationId, $isTyping = true)
    {
        $this->user = $user;
        $this->conversationId = $conversationId;
        $this->isTyping = $isTyping;
    }

    public function broadcastOn()
    {
        return new Channel('typing.' . $this->conversationId);
    }

    public function broadcastAs()
    {
        return 'user.typing';
    }

    public function broadcastWith()
    {
        return [
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
            'isTyping' => $this->isTyping,
        ];
    }
}
