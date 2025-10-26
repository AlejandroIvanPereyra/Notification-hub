<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Providers\Messaging\DiscordProvider;

class DiscordServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('provider.discord', function ($app) {
            return new DiscordProvider();
        });
    }
}
