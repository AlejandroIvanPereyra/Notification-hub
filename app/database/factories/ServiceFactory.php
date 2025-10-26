<?php


namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Service;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition()
    {
        return [
            'name' => $this->faker->unique()->word(),
             'type' => $this->faker->randomElement(['bot', 'webhook', 'api']),
        ];
    }
}