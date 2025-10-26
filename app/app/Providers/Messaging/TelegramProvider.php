<?php

namespace App\Providers\Messaging;

use App\Contracts\MessageProviderInterface;
use App\Models\MessageTarget;
use Illuminate\Support\Facades\Http;

class TelegramProvider implements MessageProviderInterface
{
    protected string $botToken;
    protected string $apiUrl;

    public function __construct(string $botToken)
    {
        $this->botToken = $botToken;
        $this->apiUrl = "https://api.telegram.org/bot{$this->botToken}/sendMessage";
    }

    public function send(MessageTarget $target): bool
    {
       $message = $target->message; // asegurar relación
        if (!$message) {
            return false;
        }

        \Log::info('Enviando mensaje a Telegram', [
            'chat_id' => $target->recipient,
            'text' => $target->message->content,
        ]);

        $response = Http::post($this->apiUrl, [
            'chat_id' => $target->recipient,
            'text' => $message->content,
        ]);

        \Log::info('Telegram response', ['status' => $response->status(), 'body' => $response->body()]);
        return $response->successful();
    }
}
