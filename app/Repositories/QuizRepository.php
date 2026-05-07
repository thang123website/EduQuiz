<?php

namespace App\Repositories;

use App\Models\Quiz;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class QuizRepository
{
    /**
     * Get paginated quizzes with advanced filtering and global stats.
     */
    public function getPaginatedForAdmin(array $filters = []): array
    {
        $query = Quiz::with(['category:id,name', 'tags'])
            ->withCount('attempts');

        // Apply filters
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['tag_id'])) {
            $query->whereHas('tags', function ($q) use ($filters) {
                $q->where('tags.id', $filters['tag_id']);
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%');
        }

        // Sorting
        $sortField = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortField, $sortOrder);

        $paginator = $query->paginate($filters['per_page'] ?? 15);

        // Global Stats for the view
        $stats = [
            'total' => Quiz::count(),
            'published' => Quiz::where('status', 'published')->count(),
            'draft' => Quiz::where('status', 'draft')->count(),
            'total_attempts' => DB::table('quiz_attempts')->count(),
        ];

        return [
            'data' => $paginator,
            'stats' => $stats
        ];
    }
}
