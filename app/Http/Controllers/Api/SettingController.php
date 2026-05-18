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
            // Chuẩn hóa JSON cho system_language và trích xuất cấu hình ngôn ngữ cho Next.js
            if (isset($rawSettings['system_language']) && is_string($rawSettings['system_language'])) {
                $languages = json_decode($rawSettings['system_language'], true) ?? [];
                $rawSettings['system_language'] = $languages; // Parse string thành Array

                $defaultLang = 'vi'; // Fallback
                $supportedLocales = [];

                foreach ($languages as $lang) {
                    if (isset($lang['status']) && $lang['status'] == 1) { // Chỉ lấy ngôn ngữ đang hoạt động
                        $supportedLocales[] = $lang['code'];
                        if (isset($lang['default']) && $lang['default'] == true) {
                            $defaultLang = $lang['code'];
                        }
                    }
                }

                // Tạo sẵn 2 biến tiện lợi để Next.js gọi vào middleware dễ dàng
                $rawSettings['site_default_language'] = $defaultLang;
                $rawSettings['site_supported_languages'] = !empty($supportedLocales) ? implode(',', $supportedLocales) : 'vi';
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
