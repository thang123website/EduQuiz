<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use App\Http\Resources\BlogLightResource;
use App\Http\Resources\BlogDetailResource;
use Illuminate\Support\Facades\Cache;

class BlogController extends Controller
{
    /**
     * Lấy danh sách bài viết (Có lọc, tìm kiếm, phân trang)
     */
    public function index(Request $request)
    {
        $limit = $request->input('limit', 10);
        $search = $request->input('search');
        $categorySlug = $request->input('category_slug');

        $query = Blog::with(['category', 'author'])
            ->where('status', 'publish');

        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($categorySlug) {
            $category = BlogCategory::where('slug', $categorySlug)->first();
            if ($category) {
                // Lấy cả bài viết của danh mục con nếu dùng tree
                $categoryIds = BlogCategory::where('parent_id', $category->id)->pluck('id')->toArray();
                $categoryIds[] = $category->id;
                $query->whereIn('category_id', $categoryIds);
            } else {
                // Trả về mảng rỗng nếu category không tồn tại
                $query->where('id', 0);
            }
        }

        // Lấy bài viết mới nhất
        $blogs = $query->orderBy('created_at', 'desc')->paginate($limit);

        return BlogLightResource::collection($blogs)->additional([
            'status' => 'success',
        ]);
    }

    /**
     * Lấy bài viết nổi bật
     */
    public function popular(Request $request)
    {
        $limit = $request->input('limit', 5);
        $cacheKey = "api_blogs_popular_{$limit}";

        $blogs = Cache::remember($cacheKey, 3600, function () use ($limit) {
            return Blog::with(['category', 'author'])
                ->where('status', 'publish')
                ->orderBy('visit_count', 'desc')
                ->limit($limit)
                ->get();
        });

        return response()->json([
            'status' => 'success',
            'data' => BlogLightResource::collection($blogs)
        ]);
    }

    /**
     * Lấy chi tiết bài viết
     */
    public function show($slug)
    {
        $blog = Blog::with(['category', 'author'])
            ->where('slug', $slug)
            ->where('status', 'publish')
            ->firstOrFail();

        // Tăng lượng truy cập (Không làm chậm request vì cập nhật trực tiếp query builder)
        Blog::where('id', $blog->id)->increment('visit_count');

        return response()->json([
            'status' => 'success',
            'data' => new BlogDetailResource($blog)
        ]);
    }

    /**
     * Lấy bài viết liên quan
     */
    public function related($slug, Request $request)
    {
        $limit = $request->input('limit', 3);

        $blog = Blog::where('slug', $slug)->firstOrFail();

        $relatedBlogs = Blog::with(['category', 'author'])
            ->where('category_id', $blog->category_id)
            ->where('id', '!=', $blog->id)
            ->where('status', 'publish')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => BlogLightResource::collection($relatedBlogs)
        ]);
    }
}
