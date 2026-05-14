<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BlogDetailResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'content' => $this->content, // Render HTML
            'image_url' => $this->image_url,
            'visit_count' => $this->visit_count,
            'enable_comment' => $this->enable_comment,
            'category' => new BlogCategoryResource($this->whenLoaded('category')),
            'author' => [
                'name' => optional($this->author)->name,
                'avatar' => optional($this->author)->avatar_url,
            ],
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
