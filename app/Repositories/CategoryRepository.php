<?php

namespace App\Repositories;

use App\Models\QuizCategory;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository
{
    /**
     * Get the full category tree for Admin UI with Eager Loading.
     */
    public function getAdminTree(): Collection
    {
        return QuizCategory::withCount('quizzes')
            ->with(['children' => function ($query) {
                $query->withCount('quizzes');
            }])
            ->whereNull('parent_id')
            ->orderBy('order_idx')
            ->get();
    }

    /**
     * Get active categories for Student frontend.
     */
    public function getActiveTree(): Collection
    {
        return QuizCategory::where('is_active', true)
            ->whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->where('is_active', true);
            }])
            ->orderBy('order_idx')
            ->get();
    }
}
