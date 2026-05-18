<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuizCategoryResource extends JsonResource
{
    public function toArray($request)
    {
        $quizzesCount = $this->quizzes_count ?? 0;
        
        // If it's a parent category and has children loaded, sum the children's count
        if ($this->relationLoaded('children') && $this->children->count() > 0) {
            $quizzesCount += $this->children->sum('quizzes_count');
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'icon' => $this->icon,
            'type' => $this->type,
            'quizzes_count' => (int) $quizzesCount,
            'children' => QuizCategoryResource::collection($this->whenLoaded('children')),
        ];
    }
}
