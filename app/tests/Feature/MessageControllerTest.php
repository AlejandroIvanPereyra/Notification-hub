<?php

namespace Tests\Feature;

use App\Models\Role;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Service;
use App\Models\Message;
use App\Models\MessageTarget;
use App\Services\MessageDispatcher;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class MessageControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear roles requeridos
        Role::create(['id' => 2, 'name' => 'user']);
        Role::create(['id' => 1, 'name' => 'admin']);

        // Crear servicios válidos
        Service::create(['name' => 'Telegram', 'type' => 'bot']);
        Service::create(['name' => 'Slack', 'type' => 'webhook']);
    }

    /** @test */
    public function it_returns_unauthenticated_if_no_user()
    {
        $response = $this->postJson('/api/messages/send', [
            'content' => 'Hola Mundo',
            'targets' => [
                ['service' => 'Telegram', 'recipient' => '123456'],
            ],
        ]);

        $response->assertStatus(401)
                 ->assertJson(['message' => 'Token not provided']);
    }

    /** @test */
    public function it_sends_a_message_successfully()
    {
        $user = User::factory()->create(['username' => 'juan']);
        $token = JWTAuth::fromUser($user);

        // Mock del dispatcher
        $this->mock(MessageDispatcher::class, function ($mock) {
            $mock->shouldReceive('dispatch')
                 ->once()
                 ->andReturn(['Telegram' => 'sent', 'Slack' => 'sent']);
        });

        $payload = [
            'content' => '¡Hola desde Notification Hub!',
            'targets' => [
                ['service' => 'Telegram', 'recipient' => '123456'],
                ['service' => 'Slack', 'recipient' => 'https://hooks.slack.com/...'],
            ],
        ];

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->postJson('/api/messages/send', $payload);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'Message dispatched successfully',
                     'user' => 'juan',
                 ]);

        $this->assertDatabaseHas('messages', [
            'user_id' => $user->id,
            'content' => '¡Hola desde Notification Hub!' . "\n\n-- juan",
        ]);

        $this->assertDatabaseCount('message_targets', 2);
    }

    /** @test */
    public function it_returns_error_if_service_not_found()
    {
        $user = User::factory()->create(['username' => 'juan']);
        $token = JWTAuth::fromUser($user);

        $payload = [
            'content' => 'Hola Mundo',
            'targets' => [
                ['service' => 'NoExiste', 'recipient' => '123'],
            ],
        ];

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->postJson('/api/messages/send', $payload);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'Service not found: NoExiste',
                     'user' => 'juan',
                 ]);
    }

    /** @test */
    public function it_lists_messages_for_authenticated_user()
    {
        $user = User::factory()->create(['username' => 'juan']);
        $token = JWTAuth::fromUser($user);

        $message = Message::factory()->create(['user_id' => $user->id]);
        MessageTarget::factory()->create([
            'message_id' => $message->id,
            'service_id' => Service::where('name', 'Telegram')->first()->id,
        ]);

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->getJson('/api/messages');

        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $message->id]);
    }
}
