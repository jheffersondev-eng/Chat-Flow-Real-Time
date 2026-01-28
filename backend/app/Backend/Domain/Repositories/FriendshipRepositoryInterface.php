<?php

namespace Backend\Domain\Repositories;

use Backend\Domain\Entities\Friendship;
use Illuminate\Support\Collection;

interface FriendshipRepositoryInterface
{
    public function findById(int $id): ?Friendship;
    
    public function create(int $userId, int $friendId, string $status = 'pending'): Friendship;
    
    public function updateStatus(int $id, string $status): bool;
    
    public function delete(int $id): bool;
    
    public function getPendingRequests(int $userId): Collection;
    
    public function getFriends(int $userId): Collection;
    
    public function exists(int $userId, int $friendId): bool;
    
    public function deleteBetween(int $userId, int $friendId): bool;
}
