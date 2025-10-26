<?php

namespace App\Services;

use App\Factories\ProviderFactory;
use App\Models\Message;

class MessageDispatcher
{
    public function dispatch(Message $message): array
    {
        $results = [];

        $message->loadMissing('targets.service');

        foreach ($message->targets as $target) {
            $service = $target->service;
            try {
                $provider = ProviderFactory::make($service);
                $ok = $provider->send($target);

                $target->update([
                    'status' => $ok ? 'success' : 'failed',
                    'provider_response' => ['success' => $ok],
                ]);

                $results[] = [
                    'target_id' => $target->id,
                    'service' => $service->name,
                    'status' => $ok ? 'success' : 'failed',
                ];
            } catch (\Throwable $e) {
                $target->update([
                    'status' => 'failed',
                    'provider_response' => ['error' => $e->getMessage()],
                ]);

                $results[] = [
                    'target_id' => $target->id,
                    'service' => $service->name,
                    'status' => 'failed',
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }
}
