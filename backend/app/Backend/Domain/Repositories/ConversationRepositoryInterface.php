<?php

namespace Backend\Domain\Repositories;

use Backend\Domain\Entities\Conversation;
use Illuminate\Support\Collection;

interface ConversationRepositoryInterface
{
    public function findById(int $id): ?Conversation;
    
    public function getUserConversations(int $userId): Collection;
    
    public function getPrivateConversation(int $userId, int $friendId): ?Conversation;
    
    public function create(string $type, ?string $name = null): Conversation;
    
    public function delete(int $id): bool;
    
    public function attachParticipants(int $conversationId, array $userIds): void;
    
    public function markAsRead(int $conversationId, int $userId): void;
    
    public function touch(int $conversationId): void;
}
