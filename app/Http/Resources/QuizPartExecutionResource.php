<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuizPartExecutionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->makeUrlsAbsolute($this->description),
            'order_idx' => $this->order_idx,
            'questions' => QuestionExecutionResource::collection($this->whenLoaded('questions')),
        ];
    }

    /**
     * Convert relative markdown/html image paths to absolute URLs
     */
    protected function makeUrlsAbsolute($content)
    {
        if (empty($content)) return $content;

        $baseUrl = url('');
        
        // Match Markdown images/links: ](/storage/...) or ](storage/...)
        $content = preg_replace('/\]\(\/?storage\//i', '](' . $baseUrl . '/storage/', $content);
        
        // Match HTML images/links: src="/storage/..." or src="storage/..."
        $content = preg_replace('/(src|href)="(\/?storage\/)/i', '$1="' . $baseUrl . '/storage/', $content);

        return $content;
    }
}
