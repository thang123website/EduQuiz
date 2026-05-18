<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;

class TranslationController extends Controller
{
    /**
     * Get static translations for a specific language
     */
    public function getTranslations(string $lang): JsonResponse
    {
        // Sanitize the input to prevent directory traversal
        $lang = basename($lang);
        $path = resource_path("lang/{$lang}.json");

        if (!File::exists($path)) {
            // Fallback to default if not found (or return empty)
            $fallbackPath = resource_path('lang/vi.json');
            if (File::exists($fallbackPath)) {
                return response()->json(json_decode(File::get($fallbackPath), true));
            }
            return response()->json([], 404);
        }

        $translations = json_decode(File::get($path), true);

        return response()->json($translations ?? []);
    }
}
