<?php

namespace App\Providers\Messaging;

use App\Contracts\MessageProviderInterface;
use App\Models\MessageTarget;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DiscordProvider implements MessageProviderInterface
{
    public function __construct() {}

    public function send(MessageTarget $target): bool
    {
        $message = $target->message;

        if (!$message || !$target->recipient) {
            Log::warning('DiscordProvider: falta mensaje o webhook', [
                'recipient' => $target->recipient,
                'message_id' => $target->message_id ?? null,
            ]);
            return false;
        }

        Log::info('Enviando mensaje a Discord', [
            'webhook_url' => $target->recipient,
            'content' => $message->content,
        ]);

        // Discord espera "content" como campo principal
        $response = Http::post($target->recipient, [
            'content' => $message->content,
        ]);

        Log::info('Discord response', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        return $response->successful();
    }
}
