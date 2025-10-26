<?php

namespace App\Providers\Messaging;

use App\Contracts\MessageProviderInterface;
use App\Models\MessageTarget;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SlackProvider implements MessageProviderInterface
{
    public function __construct() {}

    public function send(MessageTarget $target): bool
    {
        $message = $target->message;

        if (!$message || !$target->recipient) {
            Log::warning('SlackProvider: falta mensaje o webhook', [
                'recipient' => $target->recipient,
                'message_id' => $target->message_id ?? null,
            ]);
            return false;
        }

        Log::info('Enviando mensaje a Slack', [
            'webhook_url' => $target->recipient,
            'text' => $message->content,
        ]);

        $response = Http::post($target->recipient, [
            'text' => $message->content,
        ]);

        Log::info('Slack response', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        return $response->successful();
    }
}
