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

    /**
     * Get flat tree for select dropdowns
     */
    public function getFlatTree($activeOnly = false): Collection
    {
        $query = QuizCategory::whereNull('parent_id')->orderBy('order_idx');
        if ($activeOnly) {
            $query->where('is_active', true);
        }
        $categories = $query->get();

        $allCategoriesQuery = QuizCategory::query();
        if ($activeOnly) {
            $allCategoriesQuery->where('is_active', true);
        }
        $allCategories = $allCategoriesQuery->get()->groupBy('parent_id');
        
        $result = [];
        foreach ($categories as $category) {
            $this->flattenCategories($category, $allCategories, $result, '');
        }
        
        return new \Illuminate\Database\Eloquent\Collection($result);
    }

    private function flattenCategories($category, $allCategories, &$result, $prefix)
    {
        $category->name_prefixed = $prefix . $category->name;
        $result[] = $category;

        if (isset($allCategories[$category->id])) {
            $children = $allCategories[$category->id]->sortBy('order_idx');
            foreach ($children as $child) {
                $this->flattenCategories($child, $allCategories, $result, $prefix . '— ');
            }
        }
    }
}
