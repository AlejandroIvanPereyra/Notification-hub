<?php
namespace App\Providers\Messaging;

use App\Contracts\MessageProviderInterface;
use App\Models\MessageTarget;
use Illuminate\Support\Facades\Http;

class TelegramProvider implements MessageProviderInterface
{
    protected string $botToken;
    protected string $apiUrl;

    public function __construct(array $config)
    {
        $this->botToken = $config['bot_token'];
        $this->apiUrl = "https://api.telegram.org/bot{$this->botToken}/sendMessage";
    }

    public function send(MessageTarget $target): bool
    {
        $response = Http::post($this->apiUrl, [
            'chat_id' => $target->recipient,
            'text' => $target->message->content,
        ]);

        return $response->successful();
    }
}
