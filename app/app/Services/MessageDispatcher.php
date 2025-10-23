<?php


namespace App\Services;

use App\Factories\ProviderFactory;
use App\Models\Message;

class MessageDispatcher
{
    public function dispatch(Message $message): array
    {
        $results = [];

        foreach ($message->targets as $target) {
            $service = $target->service; // relación MessageTarget->service()
            try {
                $provider = ProviderFactory::make($service);
                $ok = $provider->send($target);
                $results[] = [
                    'target_id' => $target->id,
                    'service' => $service->name,
                    'status' => $ok ? 'sent' : 'failed'
                ];
            } catch (\Throwable $e) {
                $results[] = [
                    'target_id' => $target->id,
                    'service' => $service->name,
                    'status' => 'failed',
                    'error' => $e->getMessage()
                ];
                // actualizar target por si no lo hizo el provider
                $target->update([
                    'status' => 'failed',
                    'provider_response' => ['exception' => $e->getMessage()],
                ]);
            }
        }

        return $results;
    }
}
