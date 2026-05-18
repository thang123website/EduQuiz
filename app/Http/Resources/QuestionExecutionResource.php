<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuestionExecutionResource extends JsonResource
{
    public function toArray($request)
    {
        $isGroup = $this->children && $this->children->isNotEmpty();

        return [
            'id' => $this->id,
            'is_group' => $isGroup,
            'type' => $this->type,
            'content' => $this->makeUrlsAbsolute($this->content),
            'media_url' => $this->media_url ? get_image_url($this->media_url) : null,
            'media_type' => $this->media_type,
            'default_mark' => (float) $this->default_mark,
            // If it's a group (e.g. Passage), return its children. Otherwise return options.
            'child_questions' => $isGroup ? QuestionExecutionResource::collection($this->whenLoaded('children')) : null,
            'options' => !$isGroup ? OptionExecutionResource::collection($this->whenLoaded('options')) : null,
            // strictly hide explanation and correct options here!
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
