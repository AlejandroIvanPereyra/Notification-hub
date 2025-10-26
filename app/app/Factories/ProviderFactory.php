<?php

namespace App\Factories;

use App\Contracts\MessageProviderInterface;
use App\Models\Service;

class ProviderFactory
{
    /**
     * Obtiene un provider registrado en el Service Container.
     */
    public static function make(Service $service): MessageProviderInterface
    {
        $bindingKey = 'provider.' . strtolower($service->name);

        if (!app()->bound($bindingKey)) {
            throw new \Exception("Provider [{$service->name}] no está registrado en el contenedor");
        }

        return app($bindingKey);
    }
}
