<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition(): array
    {
        return [
            // Relación con usuario existente o nuevo
            'user_id' => User::factory(),
            'content' => $this->faker->sentence(8),
            'metadata' => [
                'signature' => $this->faker->userName(),
                'headers' => ['X-Test' => 'Factory'],
            ],
            'sent_at' => null,
        ];
    }
}