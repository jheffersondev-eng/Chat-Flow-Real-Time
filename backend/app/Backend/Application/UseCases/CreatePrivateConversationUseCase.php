<?php

namespace Backend\Application\UseCases;

use Backend\Domain\Repositories\ConversationRepositoryInterface;
use Backend\Domain\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CreatePrivateConversationUseCase
{
    public function __construct(
        private ConversationRepositoryInterface $conversationRepository,
        private UserRepositoryInterface $userRepository
    ) {}

    public function execute(int $userId, int $friendId): array
    {
        // Check if conversation already exists
        $existingConversation = $this->conversationRepository
            ->getPrivateConversation($userId, $friendId);

        if ($existingConversation) {
            return [
                'conversation' => $existingConversation,
                'created' => false
            ];
        }

        // Create new conversation
        return DB::transaction(function () use ($userId, $friendId) {
            $conversation = $this->conversationRepository->create('private');
            $this->conversationRepository->attachParticipants(
                $conversation->id,
                [$userId, $friendId]
            );

            // Reload with relationships
            $conversation = $this->conversationRepository->findById($conversation->id);

            return [
                'conversation' => $conversation,
                'created' => true
            ];
        });
    }
}
