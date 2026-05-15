<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use App\Models\Setting;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'gender' => $this->gender,
            'dob' => $this->dob,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'avatar' => $this->avatar ? asset(Storage::url($this->avatar)) : null,
            'cover_photo' => $this->cover_photo ? asset(Storage::url($this->cover_photo)) : null,
            'status' => $this->status,
            'is_banned' => (bool) $this->ban,
            'timezone' => $this->timezone ?? Setting::get('system_timezone', 'Asia/Ho_Chi_Minh'),
        ];
    }
}
