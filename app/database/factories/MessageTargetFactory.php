<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\MessageTarget;
use App\Models\Message;
use App\Models\Service;

/**
 * @extends Factory<MessageTarget>
 */
class MessageTargetFactory extends Factory
{
    protected $model = MessageTarget::class;

    public function definition(): array
    {
        return [
            'message_id' => Message::factory(), // crea un mensaje asociado
            'service_id' => Service::factory(), // crea un servicio asociado
            'recipient' => $this->faker->userName(),
            'status' => $this->faker->randomElement(['pending', 'success', 'failed']),
            'provider_response' => [
                'code' => 200,
                'message' => 'OK',
            ],
        ];
    }
}
