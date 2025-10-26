<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MetricsController extends Controller
{

/**
 * @OA\Get(
 *     path="/api/metrics",
 *     summary="Obtiene las métricas diarias de envío de mensajes por usuario",
 *     description="Devuelve la cantidad de mensajes enviados hoy por cada usuario y cuántos puede enviar antes de alcanzar el límite diario (100 mensajes).",
 *     tags={"Metrics"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Listado de métricas diarias por usuario",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="username", type="string", example="juanperez"),
 *                 @OA\Property(property="email", type="string", example="juan@example.com"),
 *                 @OA\Property(property="messages_sent_today", type="integer", example=23),
 *                 @OA\Property(property="remaining_messages", type="integer", example=77)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Token inválido o no autenticado"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error interno del servidor"
 *     )
 * )
 */
    public function daily()
    {
        $today = now()->startOfDay();

        $metrics = User::withCount(['messages as messages_sent_today' => function ($query) use ($today) {
            $query->whereDate('created_at', '>=', $today);
        }])->get()->map(function ($user) {
            $user->remaining_messages = max(0, 100 - $user->messages_sent_today); // Límite diario
            return $user;
        });

        return response()->json($metrics);
    }
}