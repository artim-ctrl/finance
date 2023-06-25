<?php

declare(strict_types = 1);

namespace App\Providers;

use App\Models\Balance;
use App\Models\User;
use Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

final class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(): void
    {
        Gate::define(
            ability: 'own-balance',
            callback: static fn (User $user, Balance $balance) => $user->id === $balance->user_id,
        );
    }
}
