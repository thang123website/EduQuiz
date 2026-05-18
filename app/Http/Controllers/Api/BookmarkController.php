<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use App\Models\Quiz;
use App\Models\Blog;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    /**
     * Get user bookmarks
     */
    public function index(Request $request)
    {
        $userId = auth('sanctum')->id();
        $limit = $request->input('limit', 15);
        $type = $request->input('type'); // 'quiz' or 'blog'

        $query = Bookmark::with('bookmarkable')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc');

        if ($type === 'quiz') {
            $query->where('bookmarkable_type', Quiz::class);
        } elseif ($type === 'blog') {
            $query->where('bookmarkable_type', Blog::class);
        }

        $bookmarks = $query->paginate($limit);

        // Format response
        $formatted = $bookmarks->map(function ($bookmark) {
            $item = $bookmark->bookmarkable;
            if (!$item) return null;

            $typeStr = $bookmark->bookmarkable_type === Quiz::class ? 'quiz' : 'blog';
            
            return [
                'bookmark_id' => $bookmark->id,
                'type' => $typeStr,
                'created_at' => $bookmark->created_at->toIso8601String(),
                'item' => [
                    'id' => $item->id,
                    'title' => $item->title ?? ($item->name ?? ''),
                    'slug' => $item->slug ?? null,
                    'thumbnail' => $item->thumbnail ? get_image_url($item->thumbnail) : null,
                    'category' => $typeStr === 'quiz' && $item->category ? ['name' => $item->category->name] : null,
                    'question_count' => $typeStr === 'quiz' ? ($item->question_count ?? $item->total_questions ?? 0) : null,
                ]
            ];
        })->filter()->values();

        return response()->json([
            'status' => 'success',
            'data' => $formatted,
            'meta' => [
                'current_page' => $bookmarks->currentPage(),
                'last_page' => $bookmarks->lastPage(),
                'per_page' => $bookmarks->perPage(),
                'total' => $bookmarks->total(),
            ]
        ]);
    }

    /**
     * Toggle bookmark for Quiz
     */
    public function toggleQuiz($id)
    {
        $quiz = Quiz::findOrFail($id);
        return $this->toggleBookmark($quiz, Quiz::class);
    }

    /**
     * Toggle bookmark for Blog
     */
    public function toggleBlog($slug)
    {
        // Using firstOrFail with string because slug is string
        $blog = Blog::where('slug', $slug)->firstOrFail();
        return $this->toggleBookmark($blog, Blog::class);
    }

    /**
     * Helper to toggle
     */
    private function toggleBookmark($model, $typeClass)
    {
        $userId = auth('sanctum')->id();

        $bookmark = Bookmark::where('user_id', $userId)
            ->where('bookmarkable_id', $model->id)
            ->where('bookmarkable_type', $typeClass)
            ->first();

        if ($bookmark) {
            $bookmark->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Removed from bookmarks',
                'is_bookmarked' => false
            ]);
        } else {
            Bookmark::create([
                'user_id' => $userId,
                'bookmarkable_id' => $model->id,
                'bookmarkable_type' => $typeClass,
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'Added to bookmarks',
                'is_bookmarked' => true
            ]);
        }
    }
}
