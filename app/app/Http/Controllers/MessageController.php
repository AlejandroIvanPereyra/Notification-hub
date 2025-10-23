<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\MessageTarget;
use App\Models\Service;
use App\Services\MessageDispatcher;

class MessageController extends Controller
{
    public function send(Request $request, MessageDispatcher $dispatcher)
    {
        $data = $request->validate([
            'content' => 'required|string',
            'targets' => 'required|array', // Ej: [{ "service": "telegram", "recipient": "123456789" }]
        ]);

        $message = Message::create(['content' => $data['content']]);

        foreach ($data['targets'] as $targetData) {
            $service = Service::where('name', $targetData['service'])->firstOrFail();

            MessageTarget::create([
                'message_id' => $message->id,
                'service_id' => $service->id,
                'recipient' => $targetData['recipient'],
            ]);
        }

        $dispatcher->dispatch($message);

        return response()->json(['status' => 'Message dispatched successfully']);
    }
}
