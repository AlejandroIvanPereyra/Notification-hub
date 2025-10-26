<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\MessageTarget;
use App\Models\Service;
use App\Services\MessageDispatcher;

/**
 * @OA\Tag(
 *     name="Messages",
 *     description="Endpoints para el envío y gestión de mensajes."
 * )
 */

class MessageController extends Controller
{

    
    protected MessageDispatcher $dispatcher;

    public function __construct(MessageDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
       // $this->middleware('jwt:api'); // JWT middleware
    }
   /**
 * @OA\Post(
 *     path="/api/messages/send",
 *     summary="Envía un mensaje a través de un proveedor (Telegram, Slack, etc.)",
 *     tags={"Messages"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"content","targets"},
 *             @OA\Property(property="content", type="string"),
 *             @OA\Property(
 *                 property="targets",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="service", type="string"),
 *                     @OA\Property(property="recipient", type="string")
 *                 )
 *             ),
 *             example={
 *                 "content": "¡Hola desde Notification Hub!",
 *                 "targets": {
 *                     {
 *                         "service": "Telegram",
 *                         "recipient": "6652156621"
 *                     },
 *                     {
 *                         "service": "Slack",
 *                         "recipient": "https://hooks.slack.com/services/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"
 *                     },
 *                     {
 *                         "service": "Discord",
 *                         "recipient": "https://discord.com/api/webhooks/1431766030090375198/ixm2sQhbs3VtLpe_cuz4-JmzHLOhY3fBQ_hIs4DPA-xfYO8C6UB24DckNu_NTSQHZVlb"
 *                     }
 *                 }
 *             }
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Mensaje enviado correctamente",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="Message dispatched successfully"),
 *             @OA\Property(property="user", type="string", example="juan")
 *         )
 *     ),
 *     @OA\Response(response=401, description="Token inválido o no autenticado"),
 *     @OA\Response(response=400, description="Datos inválidos"),
 *     @OA\Response(response=500, description="Error interno al enviar el mensaje")
 * )
 */

  public function send(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $data = $request->validate([
            'content' => 'required|string',
            'targets' => 'required|array',
            'targets.*.service' => 'required|string',
            'targets.*.recipient' => 'required|string',
        ]);

        // agregar firma de usuario al contenido
        $signedContent = $data['content'] . "\n\n-- " . $user->username;

        $message = Message::create([
            'user_id' => $user->id,
            'content' => $signedContent,
        ]);

        $services = Service::whereIn('name', collect($data['targets'])->pluck('service'))
            ->get()
            ->keyBy('name');

        foreach ($data['targets'] as $targetData) {
            $service = $services->get($targetData['service']);
            if (!$service) {
                logger("Servicio no encontrado", $targetData);
                return response()->json([
                    'status' => 'Service not found: ' . $targetData['service'],
                    
                    'user' => $user->username,
                ]);
            }

               
            

            MessageTarget::create([
                'message_id' => $message->id,
                'service_id' => $service->id,
                'recipient' => $targetData['recipient'],
            ]);
            logger("Target creado", $targetData);  
        }

        $message->load('targets.service'); // carga targets y su service relacionado
        logger($message->targets->toArray());
        $result = $this->dispatcher->dispatch($message);
        
        return response()->json([
            'status' => 'Message dispatched successfully',
            'results' => $result,
            'user' => $user->username,
        ]);
    }


    /**
     * @OA\Get(
     *     path="/api/messages",
     *     summary="Lista los mensajes enviados por el usuario autenticado o por todos los usuarios si es administrador",
     *     tags={"Messages"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filtra los mensajes por estado del envío (por ejemplo: sent, failed, pending)",
     *         required=false,
     *         @OA\Schema(type="string", example="sent")
     *     ),
     *     @OA\Parameter(
     *         name="service",
     *         in="query",
     *         description="Filtra los mensajes por nombre del servicio (por ejemplo: telegram, slack, discord)",
     *         required=false,
     *         @OA\Schema(type="string", example="telegram")
     *     ),
     *     @OA\Parameter(
     *         name="from",
     *         in="query",
     *         description="Fecha inicial para filtrar los mensajes (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date", example="2025-10-01")
     *     ),
     *     @OA\Parameter(
     *         name="to",
     *         in="query",
     *         description="Fecha final para filtrar los mensajes (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date", example="2025-10-26")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Listado de mensajes obtenidos correctamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="content", type="string", example="¡Hola desde Notification Hub!"),
     *                 @OA\Property(property="user_id", type="integer", example=5),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-10-25T14:30:00Z"),
     *                 @OA\Property(
     *                     property="targets",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=12),
     *                         @OA\Property(property="recipient", type="string", example="123456789"),
     *                         @OA\Property(property="status", type="string", example="sent"),
     *                         @OA\Property(
     *                             property="service",
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=2),
     *                             @OA\Property(property="name", type="string", example="telegram")
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Token inválido o no autenticado"),
     *     @OA\Response(response=500, description="Error interno al obtener los mensajes")
     * )
     */
    public function listMessages(Request $request)
    {
        $user = auth()->user();

        $query = \App\Models\Message::query();

        // Admin puede ver todo, usuario solo sus mensajes
        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        // Filtros opcionales
        if ($request->has('status')) {
            $query->whereHas('targets', function($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        if ($request->has('service')) {
            $query->whereHas('targets.service', function($q) use ($request) {
                $q->where('name', $request->service);
            });
        }

        if ($request->has('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->has('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $messages = $query->with('targets.service')->get();

        return response()->json($messages);
    }


}
