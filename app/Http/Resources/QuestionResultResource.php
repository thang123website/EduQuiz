<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResultResource extends JsonResource
{
    public function toArray($request)
    {
        $isGroup = $this->children && $this->children->isNotEmpty();
        
        // Find user's response from loaded relationship if exists
        $userResponse = null;
        if ($this->relationLoaded('userResponses') && $this->userResponses->isNotEmpty()) {
            $userResponse = $this->userResponses->first();
        }

        return [
            'id' => $this->id,
            'is_group' => $isGroup,
            'type' => $this->type,
            'content' => $this->makeUrlsAbsolute($this->content),
            'media_url' => $this->media_url ? get_image_url($this->media_url) : null,
            'media_type' => $this->media_type,
            'default_mark' => (float) $this->default_mark,
            'explanation' => $this->makeUrlsAbsolute($this->explanation), // Show explanation in result
            'child_questions' => $isGroup ? QuestionResultResource::collection($this->whenLoaded('children')) : null,
            'options' => !$isGroup ? OptionResultResource::collection($this->whenLoaded('options')) : null,
            
            // User response data
            'user_answer' => !$isGroup && $userResponse ? [
                'selected_option_id' => $userResponse->selected_option_id,
                'is_correct' => $userResponse->is_correct,
            ] : null,
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
