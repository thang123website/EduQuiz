<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuizListResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'thumbnail' => $this->thumbnail ? get_image_url($this->thumbnail) : null,
            'type' => $this->type,
            'duration' => $this->duration,
            'difficulty' => $this->difficulty,
            'is_new' => $this->is_new,
            'is_popular' => $this->is_popular,
            'question_count' => $this->question_count,
            'total_points' => (float) $this->total_points,
            'category' => new QuizCategoryResource($this->whenLoaded('category')),
            'tags' => $this->whenLoaded('tags', function () {
                return $this->tags->pluck('name');
            }),
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
        ];
    }
}
