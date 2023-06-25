<?php

declare(strict_types = 1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Response;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        Response::macro(name: 'jsonNoContent', macro: function () {
            return response()->json(status: 204);
        });
    }
}
