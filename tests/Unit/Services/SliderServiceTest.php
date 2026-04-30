<?php

namespace Tests\Unit\Services;

use App\Models\Slider;
use App\Models\SliderItem;
use App\Services\SliderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SliderServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SliderService $sliderService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sliderService = new SliderService();
    }

    public function test_it_can_create_a_slider()
    {
        // Arrange
        $data = [
            'name' => 'Home Slider',
            'key' => 'home-slider',
            'status' => 'active',
        ];

        // Act
        $slider = $this->sliderService->create($data);

        // Assert
        $this->assertInstanceOf(Slider::class, $slider);
        $this->assertEquals('home-slider', $slider->key);
        $this->assertDatabaseHas('sliders', ['key' => 'home-slider']);
    }

    public function test_it_can_update_a_slider_and_clear_cache()
    {
        // Arrange
        $slider = Slider::create([
            'name' => 'Old Name',
            'key' => 'old-key',
            'status' => 'active',
        ]);
        
        Cache::put("slider_key_old-key", 'dummy-data');

        // Act
        $updatedSlider = $this->sliderService->update($slider, [
            'name' => 'New Name',
            'key' => 'new-key',
        ]);

        // Assert
        $this->assertEquals('New Name', $updatedSlider->name);
        $this->assertNull(Cache::get("slider_key_old-key"));
        $this->assertDatabaseHas('sliders', ['key' => 'new-key']);
    }

    public function test_it_can_save_a_new_slider_item_with_auto_incrementing_order()
    {
        // Arrange
        $slider = Slider::create(['name' => 'Main', 'key' => 'main']);
        
        // Tạo sẵn 1 item với order = 5
        SliderItem::create([
            'slider_id' => $slider->id,
            'title' => 'Item 1',
            'order' => 5
        ]);

        $data = [
            'title' => 'New Item',
            'image' => '/storage/test.jpg',
            'status' => 'active',
        ];

        // Act
        $item = $this->sliderService->saveItem($slider, $data);

        // Assert
        $this->assertEquals(6, $item->order); // Tự động tăng từ 5 lên 6
        $this->assertEquals($slider->id, $item->slider_id);
    }

    public function test_it_can_update_items_order_and_clear_cache()
    {
        // Arrange
        $slider = Slider::create(['name' => 'Main', 'key' => 'main']);
        $item1 = SliderItem::create(['slider_id' => $slider->id, 'title' => 'A', 'order' => 0]);
        $item2 = SliderItem::create(['slider_id' => $slider->id, 'title' => 'B', 'order' => 1]);

        Cache::put("slider_key_main", 'dummy');

        $orders = [
            ['id' => $item1->id, 'order' => 5],
            ['id' => $item2->id, 'order' => 2],
        ];

        // Act
        $this->sliderService->updateOrder($slider, $orders);

        // Assert
        $this->assertEquals(5, $item1->fresh()->order);
        $this->assertEquals(2, $item2->fresh()->order);
        $this->assertNull(Cache::get("slider_key_main"));
    }
}
