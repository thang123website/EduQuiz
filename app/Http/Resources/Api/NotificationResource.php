<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = is_array($this->data) ? $this->data : [];
        
        // Chuẩn hóa đường dẫn ảnh thành URL tuyệt đối nếu có
        if (isset($data['image']) && !empty($data['image'])) {
            $data['image'] = get_image_url($data['image']);
        }

        return [
            'id' => $this->id,
            'type' => class_basename($this->type),
            'data' => $data,
            'read_at' => $this->read_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
