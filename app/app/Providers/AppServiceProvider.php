<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Providers\Messaging\TelegramProvider;
use App\Models\Service;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        
        $this->app->singleton('provider.telegram', function () {
            $service = Service::where('name', 'Telegram')->first();
            return new TelegramProvider($service->config);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
