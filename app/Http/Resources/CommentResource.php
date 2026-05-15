<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => [
                'id' => optional($this->user)->id,
                'name' => optional($this->user)->name,
                'avatar' => optional($this->user)->avatar_url,
            ],
            'content' => $this->content,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
            'replies' => CommentResource::collection($this->whenLoaded('replies')),
        ];
    }
}
