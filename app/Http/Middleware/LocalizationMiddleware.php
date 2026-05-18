<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LocalizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->is('api/*')) {
            // Lấy ưu tiên từ X-localization (để tương thích ngược) hoặc Accept-Language (chuẩn HTTP)
            $locale = $request->header('X-localization') ?? $request->header('Accept-Language', 'vi');
            
            // Xử lý trường hợp Accept-Language có phẩy (vd: en-US,en;q=0.9) - chỉ lấy mã đầu tiên
            if (str_contains($locale, ',')) {
                $locale = explode(',', $locale)[0];
            }
            if (str_contains($locale, '-')) {
                $locale = explode('-', $locale)[0]; // en-US -> en
            }
        } else {
            $locale = session()->get('locale', 'vi');
        }

        // Allow only valid locales from settings or standard list (simplified for now)
        // If we want to strictly validate against DB settings, we can do it here,
        // but it's faster to just accept the header and let the app handle fallback.

        // Set the global app locale
        App::setLocale($locale);

        return $next($request);
    }
}
