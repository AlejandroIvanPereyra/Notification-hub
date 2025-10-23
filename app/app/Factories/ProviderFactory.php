<?php
namespace App\Factories;

use App\Contracts\MessageProviderInterface;
use App\Models\Service;
use App\Providers\Messaging\TelegramProvider;

class ProviderFactory
{
    public static function make(Service $service): MessageProviderInterface
    {
        $config = $service->config;

        return match (strtolower($service->name)) {
            'telegram' => new TelegramProvider($config),
            default => throw new \Exception("Provider {$service->name} no soportado"),
        };
    }
}
