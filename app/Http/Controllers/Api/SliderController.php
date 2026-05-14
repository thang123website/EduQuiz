<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use App\Http\Resources\SliderResource;
use Illuminate\Support\Facades\Cache;

class SliderController extends Controller
{
    public function show($key)
    {
        // Cache kết quả trong 1 giờ (3600 giây) để tránh query DB nhiều lần ở Homepage
        $slider = Cache::remember("api_slider_{$key}", 3600, function () use ($key) {
            return Slider::with('activeItems')->where('key', $key)->where('status', 'active')->first();
        });

        if (!$slider) {
            return response()->json([
                'status' => 'error',
                'message' => 'Slider không tồn tại hoặc đã bị khóa.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => new SliderResource($slider)
        ]);
    }
}
