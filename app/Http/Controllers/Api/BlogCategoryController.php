<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Http\Resources\BlogCategoryResource;
use Illuminate\Support\Facades\Cache;
use App\Models\Setting;

class BlogCategoryController extends Controller
{
    public function index()
    {
        $cacheEnabled = Setting::get('api_cache_enabled', 0) == '1';
        $cacheDuration = (int) Setting::get('api_cache_duration', 3600);

        $fetchData = function () {
            $categories = BlogCategory::getTree();
            return json_decode(BlogCategoryResource::collection($categories)->toJson(), true);
        };

        $locale = app()->getLocale();

        if ($cacheEnabled && $cacheDuration > 0) {
            $data = Cache::remember("api_blog_categories_tree_{$locale}", $cacheDuration, $fetchData);
        } else {
            $data = $fetchData();
        }

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    public function show($slug)
    {
        $cacheEnabled = Setting::get('api_cache_enabled', 0) == '1';
        $cacheDuration = (int) Setting::get('api_cache_duration', 3600);

        $fetchData = function () use ($slug) {
            $category = BlogCategory::where('slug', $slug)->firstOrFail();
            return json_decode((new BlogCategoryResource($category))->toJson(), true);
        };

        $locale = app()->getLocale();

        if ($cacheEnabled && $cacheDuration > 0) {
            $data = Cache::remember("api_blog_category_{$slug}_{$locale}", $cacheDuration, $fetchData);
        } else {
            $data = $fetchData();
        }

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }
}
