<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MetricsController extends Controller
{
    /**
     * Endpoint de métricas diarias por usuario
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