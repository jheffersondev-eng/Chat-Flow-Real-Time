<?php

namespace Backend\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Backend\Domain\Repositories\UserRepositoryInterface;
use Backend\Domain\Repositories\ConversationRepositoryInterface;
use Backend\Domain\Repositories\MessageRepositoryInterface;
use Backend\Domain\Repositories\FriendshipRepositoryInterface;
use Backend\Infrastructure\Repositories\UserRepository;
use Backend\Infrastructure\Repositories\ConversationRepository;
use Backend\Infrastructure\Repositories\MessageRepository;
use Backend\Infrastructure\Repositories\FriendshipRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind Repository Interfaces to Implementations
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(ConversationRepositoryInterface::class, ConversationRepository::class);
        $this->app->bind(MessageRepositoryInterface::class, MessageRepository::class);
        $this->app->bind(FriendshipRepositoryInterface::class, FriendshipRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
