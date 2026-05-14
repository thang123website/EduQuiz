<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BlogCategoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'level' => $this->level ?? 0,
            'blogs_count' => $this->when(isset($this->blogs_count), $this->blogs_count),
        ];
    }
}
