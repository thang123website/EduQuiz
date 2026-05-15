<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use App\Http\Resources\SliderResource;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SliderController extends Controller
{
    public function show($key)
    {
        $cacheEnabled = Setting::get('api_cache_enabled', 0) == '1';
        $cacheDuration = (int) Setting::get('api_cache_duration', 3600);

        $fetchData = function () use ($key) {
            $slider = Slider::with('activeItems')->where('key', $key)->where('status', 'active')->first();
            
            if (!$slider) {
                return null;
            }
            
            // Convert toàn bộ resource (bao gồm các collection lồng nhau) ra Array thuần
            return json_decode((new SliderResource($slider))->toJson(), true);
        };

        if ($cacheEnabled && $cacheDuration > 0) {
            $data = Cache::remember("api_slider_{$key}", $cacheDuration, $fetchData);
        } else {
            $data = $fetchData();
        }

        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'Slider không tồn tại hoặc đã bị khóa.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }
}
