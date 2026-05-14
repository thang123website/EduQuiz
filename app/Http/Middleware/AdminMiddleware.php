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

        $user = $request->user();

        // 1. Chặn user không có role
        if (!$user->role_id) {
            abort(403, 'Tài khoản của bạn chưa được cấp quyền truy cập trang quản trị.');
        }

        // 2. Chặn nếu không phải là Admin và không được cấp bất kỳ quyền nào
        if (!$user->isAdmin() && !$user->hasAnyPermission()) {
            abort(403, 'Tài khoản của bạn không có quyền truy cập trang quản trị.');
        }

        return $next($request);
    }
}
