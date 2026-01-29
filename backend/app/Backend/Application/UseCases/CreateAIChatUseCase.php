<?php

namespace Backend\Application\UseCases;

use Backend\Domain\Repositories\ConversationRepositoryInterface;
use Backend\Domain\Repositories\MessageRepositoryInterface;

class CreateAIChatUseCase
{
    public function __construct(
        private ConversationRepositoryInterface $conversationRepository,
        private MessageRepositoryInterface $messageRepository
    ) {}

    public function execute(int $userId, ?string $initialMessage = null): array
    {
        // Check for existing empty AI chat
        $userConversations = $this->conversationRepository->getUserConversations($userId);
        $existingEmptyAIChat = $userConversations->first(function ($conversation) {
            return $conversation->type === 'ai' && $conversation->messages->isEmpty();
        });

        if ($existingEmptyAIChat) {
            return [
                'conversation' => $existingEmptyAIChat,
                'created' => false
            ];
        }

        // Create new AI conversation
        $conversation = $this->conversationRepository->create('ai', 'Chat com IA');
        $this->conversationRepository->attachParticipants($conversation->id, [$userId]);

        // Add initial message if provided
        if ($initialMessage) {
            $this->messageRepository->create([
                'conversation_id' => $conversation->id,
                'user_id' => $userId,
                'content' => $initialMessage,
                'type' => 'user',
            ]);
        }

        return [
            'conversation' => $conversation->fresh()->load('participants', 'messages'),
            'created' => true
        ];
    }
}
