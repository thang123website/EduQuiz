<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SliderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'key' => $this->key,
            'description' => $this->description,
            'settings' => $this->settings,
            'items' => SliderItemResource::collection($this->whenLoaded('activeItems')),
        ];
    }
}
