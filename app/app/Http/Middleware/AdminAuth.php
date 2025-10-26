<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminAuth
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        Log::info('AdminAuth Middleware: User role - ' . json_encode($user->role));

        if (!$user || strtolower($user->role->name) !== 'admin') {
            return response()->json(['error' => 'Unauthorized. Admins only.'], 403);
        }

        return $next($request);
    }
}