<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QuizCategory;
use Illuminate\Http\Request;
use App\Http\Resources\QuizCategoryResource;
use Illuminate\Support\Facades\Cache;

class QuizCategoryController extends Controller
{
    /**
     * Get a hierarchical list of quiz categories
     */
    public function index(Request $request)
    {
        $difficulty = $request->input('difficulty');
        $questionRange = $request->input('question_range');
        $type = $request->input('type');
        $tags = $request->input('tags');

        // Tạo cache key dựa trên các filter
        $cacheParams = compact('difficulty', 'questionRange', 'type', 'tags');
        $cacheKey = 'api_quiz_categories_tree_deep_' . md5(json_encode($cacheParams));

        $hasFilters = !empty($difficulty) || !empty($questionRange) || !empty($type) || !empty($tags);

        $fetchData = function () use ($difficulty, $questionRange, $type, $tags, $hasFilters) {
            // Get all active categories with direct quiz counts filtered by conditions
            $allCategories = QuizCategory::where('is_active', true)
                ->withCount(['quizzes' => function($q) use ($difficulty, $questionRange, $type, $tags) {
                    $q->where('status', 'published');
                    
                    if ($difficulty) {
                        $q->where('difficulty', $difficulty);
                    }
                    if ($type) {
                        $q->where('type', $type);
                    }
                    if ($tags) {
                        if (is_string($tags)) {
                            $tags = explode(',', $tags);
                        }
                        $q->whereHas('tags', function ($query) use ($tags) {
                            $query->whereIn('slug', $tags);
                        });
                    }
                    if ($questionRange) {
                        if ($questionRange === '<20') {
                            $q->where('question_count', '<', 20);
                        } elseif ($questionRange === '20-40') {
                            $q->whereBetween('question_count', [20, 40]);
                        } elseif ($questionRange === '>40') {
                            $q->where('question_count', '>', 40);
                        }
                    }
                }])
                ->orderBy('order_idx')
                ->get();

            // Group by parent_id for easy traversal
            $grouped = $allCategories->groupBy('parent_id');

            return $this->buildTreeAndCalculateCount($grouped, null, $hasFilters);
        };

        // Cache for 24 hours
        $data = Cache::remember($cacheKey, 86400, $fetchData);

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * Recursively build tree and calculate total quizzes count for N-levels.
     */
    private function buildTreeAndCalculateCount($grouped, $parentId = null, $hasFilters = false)
    {   
        $result = [];

        if (!isset($grouped[$parentId])) {
            return $result;
        }

        foreach ($grouped[$parentId] as $category) {
            $children = $this->buildTreeAndCalculateCount($grouped, $category->id, $hasFilters);
            
            $totalCount = $category->quizzes_count;
            foreach ($children as $child) {
                $totalCount += $child['quizzes_count'];
            }

            // Nếu người dùng có chọn filter mà nhánh này đếm ra 0 bài thi -> Ẩn luôn danh mục này!
            if ($hasFilters && $totalCount === 0) {
                continue;
            }

            $result[] = [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'icon' => $category->icon,
                'type' => $category->type,
                'quizzes_count' => (int) $totalCount,
                'children' => $children
            ];
        }

        return $result;
    }
}
