<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use Laravel\Sanctum\Sanctum;
use Backend\Domain\Entities\User;
use Backend\Infrastructure\Auth\PersonalAccessToken;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
        Relation::morphMap([
            'App\\Models\\User' => User::class,
        ]);
    }
}
