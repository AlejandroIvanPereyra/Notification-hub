<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    public function run()
    {
        $services = [
            ['name' => 'Slack', 'type' => 'webhook', 'base_url' => 'https://hooks.slack.com/', 'is_active' => true],
            ['name' => 'Telegram', 'type' => 'bot_api', 'base_url' => 'https://api.telegram.org/', 'is_active' => true],
            ['name' => 'Discord', 'type' => 'webhook', 'base_url' => 'https://discord.com/api/webhooks/', 'is_active' => false],
            ['name' => 'Microsoft Teams', 'type' => 'graph_api', 'base_url' => 'https://graph.microsoft.com/', 'is_active' => false],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
