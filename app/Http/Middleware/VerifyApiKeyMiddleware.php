<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiEnabled = \App\Models\Setting::get('api_enabled');
        
        if ($apiEnabled === '0') {
            return response()->json([
                'status' => 'error',
                'message' => 'API is currently disabled.'
            ], 403);
        }

        $apiKey = \App\Models\Setting::get('api_key');
        
        if (!empty($apiKey)) {
            $providedKey = $request->header('X-API-KEY');
            if ($providedKey !== $apiKey) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized or missing API Key.'
                ], 401);
            }
        }

        return $next($request);
    }
}
