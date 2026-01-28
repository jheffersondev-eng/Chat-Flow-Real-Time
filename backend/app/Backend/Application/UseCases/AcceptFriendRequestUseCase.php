<?php

namespace Backend\Application\UseCases;

use Backend\Domain\Repositories\FriendshipRepositoryInterface;
use Backend\Domain\Repositories\ConversationRepositoryInterface;
use Illuminate\Support\Facades\DB;

class AcceptFriendRequestUseCase
{
    public function __construct(
        private FriendshipRepositoryInterface $friendshipRepository,
        private ConversationRepositoryInterface $conversationRepository
    ) {}

    public function execute(int $friendshipId, int $currentUserId): array
    {
        $friendship = $this->friendshipRepository->findById($friendshipId);

        if (!$friendship) {
            throw new \Exception('Friendship not found');
        }

        if ($friendship->friend_id !== $currentUserId) {
            throw new \Exception('Unauthorized');
        }

        if ($friendship->status !== 'pending') {
            throw new \Exception('Friendship is not pending');
        }

        return DB::transaction(function () use ($friendship, $currentUserId) {
            // Accept friendship
            $this->friendshipRepository->updateStatus($friendship->id, 'accepted');

            // Create private conversation
            $conversation = $this->conversationRepository->create('private');
            $this->conversationRepository->attachParticipants(
                $conversation->id,
                [$currentUserId, $friendship->user_id]
            );

            return [
                'friendship' => $friendship->fresh()->load('user'),
                'conversation' => $conversation,
                'success' => true
            ];
        });
    }
}
