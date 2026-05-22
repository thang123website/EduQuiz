<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizCategory;
use Illuminate\Http\Request;
use App\Http\Resources\QuizListResource;
use App\Http\Resources\QuizDetailResource;
use Illuminate\Support\Facades\Cache;

class QuizController extends Controller
{
    /**
     * Get list of quizzes (with filters, pagination)
     */
    public function index(Request $request)
    {
        $limit = $request->input('limit', 10);
        $search = $request->input('search');
        $categorySlug = $request->input('category_slug');
        $tags = $request->input('tags'); // array or comma separated
        $type = $request->input('type');
        $difficulty = $request->input('difficulty');
        $sortBy = $request->input('sort_by', 'latest'); // latest, popular

        $query = Quiz::with(['category' => function($q) {
            $q->withCount('quizzes');
        }, 'tags'])
            ->where('status', 'published');

        // 1. Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                // In a real large app, use Scout. Here we use LIKE for simplicity unless explicitly configured for Scout.
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // 2. Filter by Category
        if ($categorySlug) {
            if (is_string($categorySlug)) {
                $categorySlug = explode(',', $categorySlug);
            }
            
            $categories = QuizCategory::whereIn('slug', $categorySlug)->get();
            if ($categories->isNotEmpty()) {
                $matchedIds = $categories->pluck('id')->toArray();
                
                // Traverse tree in memory to find all N-level descendants
                $allCategoriesGrouped = QuizCategory::all()->groupBy('parent_id');
                
                $getAllDescendants = function($ids, $groupedCategories) use (&$getAllDescendants) {
                    $descendants = [];
                    foreach ($ids as $id) {
                        if (isset($groupedCategories[$id])) {
                            $childIds = $groupedCategories[$id]->pluck('id')->toArray();
                            $descendants = array_merge($descendants, $childIds, $getAllDescendants($childIds, $groupedCategories));
                        }
                    }
                    return $descendants;
                };
                
                $descendantIds = $getAllDescendants($matchedIds, $allCategoriesGrouped);
                $allCategoryIds = array_unique(array_merge($matchedIds, $descendantIds));
                
                $query->whereIn('category_id', $allCategoryIds);
            } else {
                $query->where('id', 0); // No match
            }
        }

        // 3. Filter by Tags
        if ($tags) {
            if (is_string($tags)) {
                $tags = explode(',', $tags);
            }
            $query->whereHas('tags', function ($q) use ($tags) {
                $q->whereIn('slug', $tags);
            });
        }

        // 4. Filter by Type
        if ($type) {
            $query->where('type', $type);
        }

        // 5. Filter by Difficulty
        if ($difficulty) {
            $query->where('difficulty', $difficulty);
        }

        // 6. Sorting
        if ($sortBy === 'popular') {
            $query->orderBy('is_popular', 'desc')->orderBy('created_at', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $quizzes = $query->paginate($limit);

        return QuizListResource::collection($quizzes)->additional([
            'status' => 'success',
        ]);
    }

    /**
     * Get popular quizzes (Cached)
     */
    public function popular(Request $request)
    {
        $limit = $request->input('limit', 5);
        $locale = app()->getLocale();
        $cacheKey = "api_quizzes_popular_{$limit}_{$locale}";

        $fetchData = function () use ($limit) {
            $quizzes = Quiz::with(['category' => function($q) {
                $q->withCount('quizzes');
            }, 'tags'])
                ->where('status', 'published')
                ->where('is_popular', true)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
                
            return json_decode(QuizListResource::collection($quizzes)->toJson(), true);
        };

        // Cache for 1 hour
        $data = Cache::remember($cacheKey, 3600, $fetchData);

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * Get latest quizzes (Cached)
     */
    public function latest(Request $request)
    {
        $limit = $request->input('limit', 5);
        $locale = app()->getLocale();
        $cacheKey = "api_quizzes_latest_{$limit}_{$locale}";

        $fetchData = function () use ($limit) {
            $quizzes = Quiz::with(['category' => function($q) {
                $q->withCount('quizzes');
            }, 'tags'])
                ->where('status', 'published')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
                
            return json_decode(QuizListResource::collection($quizzes)->toJson(), true);
        };

        // Cache for 1 hour
        $data = Cache::remember($cacheKey, 3600, $fetchData);

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * Show quiz details (without questions)
     */
    public function show($idOrSlug)
    {
        $query = Quiz::with(['category' => function($q) {
            $q->withCount('quizzes');
        }, 'tags', 'parts.questions'])
            ->where('status', 'published');

        // Check if ID (UUID length 36) or Slug
        if (strlen($idOrSlug) === 36) {
            $query->where('id', $idOrSlug);
        } else {
            // Slug doesn't exist on quizzes table! Let's check migration... wait, quizzes table doesn't have slug!
            // I'll default to id only.
            $query->where('id', $idOrSlug);
        }

        $quiz = $query->firstOrFail();

        return response()->json([
            'status' => 'success',
            'data' => new QuizDetailResource($quiz)
        ]);
    }
}
