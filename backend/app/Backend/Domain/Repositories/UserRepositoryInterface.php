<?php

namespace Backend\Domain\Repositories;

use Backend\Domain\Entities\User;
use Illuminate\Support\Collection;

interface UserRepositoryInterface
{
    public function findById(int $id): ?User;
    
    public function findByEmail(string $email): ?User;
    
    public function create(array $data): User;
    
    public function update(int $id, array $data): bool;
    
    public function search(string $term): Collection;
    
    public function findOrCreateFromOAuth(string $provider, string $providerId, array $userData): User;
}
