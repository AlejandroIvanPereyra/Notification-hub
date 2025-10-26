<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Providers\Messaging\SlackProvider;

class SlackServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('provider.slack', function ($app) {
            return new SlackProvider();
        });
    }
}
