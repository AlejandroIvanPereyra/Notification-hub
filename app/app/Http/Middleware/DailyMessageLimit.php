<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class DailyMessageLimit
{
    protected int $limit = 100; // máximo por día

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // Generamos una clave única por usuario y fecha
        $key = 'user:' . $user->id . ':daily_message_count:' . now()->toDateString();

        // Obtenemos el contador actual
        $count = Cache::get($key, 0);

        if ($count >= $this->limit) {
            return response()->json([
                'error' => "Daily message limit exceeded ({$this->limit})"
            ], 429);
        }

        // Incrementamos el contador (expira al final del día)
        Cache::put($key, $count + 1, now()->endOfDay());

        return $next($request);
    }
}
