<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuizPartResultResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'order_idx' => $this->order_idx,
            'questions' => QuestionResultResource::collection($this->whenLoaded('questions')),
        ];
    }
}
