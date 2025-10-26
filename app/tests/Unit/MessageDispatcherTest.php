<?php

namespace Tests\Unit\Services;

use App\Factories\ProviderFactory;
use App\Models\Message;
use App\Models\MessageTarget;
use App\Models\Service;
use App\Services\MessageDispatcher;
use Mockery;
use Tests\TestCase;

class MessageDispatcherTest extends TestCase
{
    public function test_dispatch_successfully_sends_messages()
    {
        // Mock del provider
        $providerMock = Mockery::mock();
        $providerMock->shouldReceive('send')
            ->once()
            ->andReturn(true);

        // Mock del ProviderFactory (evita llamada real)
        Mockery::mock('alias:' . ProviderFactory::class)
            ->shouldReceive('make')
            ->andReturn($providerMock);

        // Mock del target (evita tocar DB)
        $target = Mockery::mock(MessageTarget::class)->makePartial();
        $target->id = 10;
        $target->shouldReceive('update')->once(); // evita SQL
        $service = new Service(['id' => 1, 'name' => 'Telegram']);
        $target->setRelation('service', $service);

        // Construimos el mensaje
        $message = new Message(['id' => 100]);
        $message->setRelation('targets', collect([$target]));

        //  Ejecutamos el método
        $dispatcher = new MessageDispatcher();
        $result = $dispatcher->dispatch($message);

        // Verificamos
        $this->assertCount(1, $result);
        $this->assertEquals([
            'target_id' => 10,
            'service' => 'Telegram',
            'status' => 'success',
        ], $result[0]);
    }

    public function test_dispatch_handles_failed_provider()
    {
        // Mock del provider que lanza error
        $providerMock = Mockery::mock();
        $providerMock->shouldReceive('send')
            ->once()
            ->andThrow(new \Exception('Network error'));

        // Mock del ProviderFactory
        Mockery::mock('alias:' . ProviderFactory::class)
            ->shouldReceive('make')
            ->andReturn($providerMock);

        // Mock del target
        $target = Mockery::mock(MessageTarget::class)->makePartial();
        $target->id = 20;
        $target->shouldReceive('update')->once(); // evita SQL
        $service = new Service(['id' => 2, 'name' => 'Slack']);
        $target->setRelation('service', $service);

        // 4Construimos el mensaje
        $message = new Message(['id' => 200]);
        $message->setRelation('targets', collect([$target]));

        // Ejecutamos el método
        $dispatcher = new MessageDispatcher();
        $result = $dispatcher->dispatch($message);

        // Verificamos
        $this->assertCount(1, $result);
        $this->assertEquals('failed', $result[0]['status']);
        $this->assertEquals('Slack', $result[0]['service']);
        $this->assertArrayHasKey('error', $result[0]);
        $this->assertStringContainsString('Network error', $result[0]['error']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
