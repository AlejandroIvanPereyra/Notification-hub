<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Providers\Messaging\TelegramProvider;

class TelegramServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('provider.telegram', function ($app) {
            $botToken = config('services.telegram.bot_token'); // config/services.php
            return new TelegramProvider($botToken);
        });
    }
}