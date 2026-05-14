<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Http\Resources\BlogCategoryResource;
use Illuminate\Support\Facades\Cache;

class BlogCategoryController extends Controller
{
    public function index()
    {
        // Lấy danh sách category dạng cây (đã flatten) và đếm số bài viết
        $categories = Cache::remember('api_blog_categories_tree', 3600, function () {
            return BlogCategory::getTree();
        });

        return response()->json([
            'status' => 'success',
            'data' => BlogCategoryResource::collection($categories)
        ]);
    }

    public function show($slug)
    {
        $category = BlogCategory::where('slug', $slug)->firstOrFail();
        
        return response()->json([
            'status' => 'success',
            'data' => new BlogCategoryResource($category)
        ]);
    }
}
