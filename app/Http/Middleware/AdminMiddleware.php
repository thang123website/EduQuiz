<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Nếu user có role và có thể truy cập admin (hoặc kiểm tra role != student)
        // Hiện tại kiểm tra đơn giản nếu không có role thì chặn
        if (!$request->user()->role_id) {
            abort(403, 'Unauthorized access');
        }

        return $next($request);
    }
}
