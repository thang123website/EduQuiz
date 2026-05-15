<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use App\Http\Resources\BlogLightResource;
use App\Http\Resources\BlogDetailResource;
use App\Models\Setting;
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
        $sortBy = $request->input('sort_by', 'latest'); // latest, oldest, popular

        $query = Blog::with(['category', 'author'])
            ->where('status', 'publish');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($categorySlug) {
            // Chuyển đổi thành mảng nếu truyền vào là chuỗi cách nhau bằng dấu phẩy
            if (is_string($categorySlug)) {
                $categorySlug = explode(',', $categorySlug);
            }
            
            $categories = BlogCategory::whereIn('slug', $categorySlug)->get();
            
            if ($categories->isNotEmpty()) {
                // Lấy ID của các danh mục cha
                $parentIds = $categories->pluck('id')->toArray();
                // Lấy ID của tất cả danh mục con tương ứng
                $childrenIds = BlogCategory::whereIn('parent_id', $parentIds)->pluck('id')->toArray();
                
                $allCategoryIds = array_unique(array_merge($parentIds, $childrenIds));
                $query->whereIn('category_id', $allCategoryIds);
            } else {
                // Trả về mảng rỗng nếu không có category nào tồn tại
                $query->where('id', 0);
            }
        }

        // Xử lý sắp xếp
        if ($sortBy === 'popular') {
            $query->orderBy('visit_count', 'desc');
        } elseif ($sortBy === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } else {
            // Mặc định là mới nhất
            $query->orderBy('created_at', 'desc');
        }

        $blogs = $query->paginate($limit);

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

        $cacheEnabled = Setting::get('api_cache_enabled', 0) == '1';
        $cacheDuration = (int) Setting::get('api_cache_duration', 3600);

        $fetchData = function () use ($limit) {
            $blogs = Blog::with(['category', 'author'])
                ->where('status', 'publish')
                ->orderBy('visit_count', 'desc')
                ->limit($limit)
                ->get();
                
            return json_decode(BlogLightResource::collection($blogs)->toJson(), true);
        };

        if ($cacheEnabled && $cacheDuration > 0) {
            $data = Cache::remember($cacheKey, $cacheDuration, $fetchData);
        } else {
            $data = $fetchData();
        }

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * Lấy bài viết mới nhất (Dạng array không phân trang, có cache)
     */
    public function latest(Request $request)
    {
        $limit = $request->input('limit', 5);
        $cacheKey = "api_blogs_latest_{$limit}";

        $cacheEnabled = Setting::get('api_cache_enabled', 0) == '1';
        $cacheDuration = (int) Setting::get('api_cache_duration', 3600);

        $fetchData = function () use ($limit) {
            $blogs = Blog::with(['category', 'author'])
                ->where('status', 'publish')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
                
            return json_decode(BlogLightResource::collection($blogs)->toJson(), true);
        };

        if ($cacheEnabled && $cacheDuration > 0) {
            $data = Cache::remember($cacheKey, $cacheDuration, $fetchData);
        } else {
            $data = $fetchData();
        }

        return response()->json([
            'status' => 'success',
            'data' => $data
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
