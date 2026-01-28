<?php

namespace Backend\Application\UseCases;

use Backend\Domain\Repositories\ConversationRepositoryInterface;
use Backend\Domain\Repositories\MessageRepositoryInterface;
use Backend\Domain\Events\MessageSent;

class SendMessageUseCase
{
    public function __construct(
        private MessageRepositoryInterface $messageRepository,
        private ConversationRepositoryInterface $conversationRepository
    ) {}

    public function execute(int $conversationId, int $userId, string $content, string $type = 'user'): array
    {
        // Create message
        $message = $this->messageRepository->create([
            'conversation_id' => $conversationId,
            'user_id' => $userId,
            'content' => $content,
            'type' => $type,
        ]);

        // Update conversation timestamp
        $this->conversationRepository->touch($conversationId);

        // Broadcast message
        broadcast(new MessageSent($message->load('user'), $conversationId));

        return [
            'message' => $message,
            'success' => true
        ];
    }
}
