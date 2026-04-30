<?php

namespace App\Services;

use App\Models\Slider;
use App\Models\SliderItem;
use Illuminate\Support\Facades\Cache;

class SliderService
{
    /**
     * Lấy slider theo key (dùng ở Frontend).
     * Kết quả được cache để tăng hiệu năng.
     */
    public function getByKey(string $key): ?Slider
    {
        return Cache::remember("slider_key_{$key}", now()->addHours(6), function () use ($key) {
            return Slider::where('key', $key)
                ->where('status', 'active')
                ->with('activeItems')
                ->first();
        });
    }

    /**
     * Tạo slider group mới.
     */
    public function create(array $data): Slider
    {
        return Slider::create($data);
    }

    /**
     * Cập nhật slider group và xóa cache cũ.
     */
    public function update(Slider $slider, array $data): Slider
    {
        $oldKey = $slider->key;
        $slider->update($data);

        // Xóa cache của key cũ và key mới (nếu thay đổi key)
        Cache::forget("slider_key_{$oldKey}");
        Cache::forget("slider_key_{$slider->key}");

        return $slider->fresh();
    }

    /**
     * Xóa slider group và toàn bộ items (cascade).
     */
    public function delete(Slider $slider): void
    {
        Cache::forget("slider_key_{$slider->key}");
        $slider->delete(); // cascadeOnDelete tự xóa slider_items
    }

    /**
     * Tạo hoặc cập nhật một slide item.
     */
    public function saveItem(Slider $slider, array $data, ?SliderItem $item = null): SliderItem
    {
        if ($item) {
            $item->update($data);
        } else {
            // Tự động gán thứ tự tiếp theo
            $data['slider_id'] = $slider->id;
            $data['order'] = $slider->items()->max('order') + 1;
            $item = SliderItem::create($data);
        }

        Cache::forget("slider_key_{$slider->key}");
        return $item->fresh();
    }

    /**
     * Xóa một slide item.
     */
    public function deleteItem(SliderItem $item): void
    {
        Cache::forget("slider_key_{$item->slider->key}");
        $item->delete();
    }

    /**
     * Cập nhật thứ tự items (từ Drag & Drop).
     * $orders = [['id' => 1, 'order' => 0], ['id' => 2, 'order' => 1], ...]
     */
    public function updateOrder(Slider $slider, array $orders): void
    {
        foreach ($orders as $item) {
            SliderItem::where('id', $item['id'])
                ->where('slider_id', $slider->id) // Bảo mật: chỉ update item thuộc slider này
                ->update(['order' => $item['order']]);
        }

        Cache::forget("slider_key_{$slider->key}");
    }
}
