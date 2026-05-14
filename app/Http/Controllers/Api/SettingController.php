<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    /**
     * Lấy các cấu hình công khai của website (Có Caching)
     * Chỉ lấy cấu hình thuộc nhóm 'general' để đảm bảo an toàn
     */
    public function getWebsiteSettings(): JsonResponse
    {
        // Cache danh sách này trong 24 giờ (86400 giây).
        // Sẽ được tự động xóa khi Admin ấn "Lưu cấu hình"
        $settings = Cache::remember('api_settings_general', 86400, function () {
            $rawSettings = Setting::getGroup('general');
            
            // Chuẩn hóa các đường dẫn hình ảnh thành dạng URL tuyệt đối cho Frontend/Mobile
            $imageKeys = ['site_logo_dark', 'site_logo_light', 'site_favicon'];
            foreach ($imageKeys as $key) {
                if (isset($rawSettings[$key]) && !empty($rawSettings[$key])) {
                    $rawSettings[$key] = get_image_url($rawSettings[$key]);
                }
            }
            
            return $rawSettings;
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Lấy cấu hình website thành công.',
            'data' => $settings
        ]);
    }
}
