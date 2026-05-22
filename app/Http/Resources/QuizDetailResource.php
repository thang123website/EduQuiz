<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuizDetailResource extends JsonResource
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
            'pass_mark' => $this->pass_mark,
            'difficulty' => $this->difficulty,
            'is_new' => $this->is_new,
            'is_popular' => $this->is_popular,
            'question_count' => $this->question_count,
            'total_points' => (float) $this->total_points,
            'participants_count' => $this->attempts()->distinct('user_id')->count(),
            'category' => new QuizCategoryResource($this->whenLoaded('category')),
            'tags' => $this->whenLoaded('tags', function () {
                return $this->tags->pluck('name');
            }),
            'parts' => $this->whenLoaded('parts', function () {
                return $this->parts->map(function ($part) {
                    return [
                        'id' => $part->id,
                        'title' => $part->title,
                        'description' => $part->description,
                        'order_idx' => $part->order_idx,
                        'question_count' => $part->questions->count(), // Requires eager loading parts.questions
                    ];
                });
            }),
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
        ];
    }
}
