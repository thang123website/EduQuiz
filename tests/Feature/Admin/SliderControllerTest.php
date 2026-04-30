<?php

namespace Tests\Feature\Admin;

use App\Models\Slider;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class SliderControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        $role = \App\Models\Role::create(['name' => 'Admin', 'caption' => 'Administrator', 'is_admin' => true]);
        $this->adminUser = User::factory()->create(['role_id' => $role->id]);
    }

    public function test_unauthenticated_user_is_redirected_to_login()
    {
        $this->get(route('admin.sliders.index'))
            ->assertRedirect(route('login'));
    }

    public function test_unauthorized_user_cannot_access_slider_index()
    {
        // Tạo một user bình thường không có quyền
        $normalUser = User::factory()->create();
        $this->actingAs($normalUser);

        $this->get(route('admin.sliders.index'))
            ->assertStatus(403);
    }

    public function test_authorized_user_can_view_slider_index()
    {
        $this->actingAs($this->adminUser);

        Slider::create([
            'name' => 'Banner Home',
            'key' => 'banner-home',
            'status' => 'active',
        ]);

        $this->get(route('admin.sliders.index'))
            ->assertStatus(200)
            ->assertViewIs('admin.sliders.index')
            ->assertSee('Banner Home');
    }

    public function test_user_can_store_a_new_slider_group()
    {
        $this->actingAs($this->adminUser);

        $payload = [
            'name' => 'Test Slider',
            'key' => 'test-slider',
            'description' => 'A test slider',
            'status' => 'active',
        ];

        $this->post(route('admin.sliders.store'), $payload)
            ->assertRedirect(route('admin.sliders.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('sliders', ['key' => 'test-slider']);
    }

    public function test_validation_fails_if_slider_key_is_missing()
    {
        $this->actingAs($this->adminUser);

        $payload = [
            'name' => 'Test Slider',
            // Missing key
            'status' => 'active',
        ];

        $this->post(route('admin.sliders.store'), $payload)
            ->assertSessionHasErrors('key');
    }

    public function test_user_can_store_a_slider_item_via_ajax()
    {
        $this->actingAs($this->adminUser);

        $slider = Slider::create([
            'name' => 'Main',
            'key' => 'main',
            'status' => 'active',
        ]);

        $payload = [
            'title' => 'First Slide',
            'image' => '/storage/images/slide1.jpg',
            'status' => 'active',
        ];

        $this->postJson(route('admin.sliders.items.store', $slider), $payload)
            ->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('slider_items', [
            'slider_id' => $slider->id,
            'title' => 'First Slide',
            'order' => 1, // Tự động đánh số
        ]);
    }
}
