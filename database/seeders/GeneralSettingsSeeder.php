<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class GeneralSettingsSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            // --- Thông tin cơ bản ---
            [
                'key' => 'site_name',
                'value' => 'EduQuiz',
                'group' => 'general',
                'type' => 'string',
                'description' => 'Tên nền tảng'
            ],
            [
                'key' => 'site_logo_dark',
                'value' => '',
                'group' => 'general',
                'type' => 'image',
                'description' => 'Logo (Bản tối - cho nền sáng)'
            ],
            [
                'key' => 'site_logo_light',
                'value' => '',
                'group' => 'general',
                'type' => 'image',
                'description' => 'Logo (Bản sáng - cho nền tối)'
            ],
            [
                'key' => 'site_favicon',
                'value' => '',
                'group' => 'general',
                'type' => 'image',
                'description' => 'Favicon'
            ],
            // --- Liên hệ ---
            [
                'key' => 'site_phone',
                'value' => '0123456789',
                'group' => 'general',
                'type' => 'string',
                'description' => 'Số điện thoại liên hệ'
            ],
            [
                'key' => 'site_email',
                'value' => 'contact@eduquiz.vn',
                'group' => 'general',
                'type' => 'string',
                'description' => 'Email hỗ trợ'
            ],
            [
                'key' => 'site_address',
                'value' => 'Đà Nẵng, Việt Nam',
                'group' => 'general',
                'type' => 'string',
                'description' => 'Địa chỉ trụ sở'
            ],
            [
                'key' => 'site_copyright',
                'value' => '© 2026 EduQuiz. All rights reserved.',
                'group' => 'general',
                'type' => 'string',
                'description' => 'Thông tin bản quyền (Footer)'
            ],
            // --- Mạng xã hội ---
            [
                'key' => 'social_facebook',
                'value' => 'https://facebook.com/eduquiz',
                'group' => 'general',
                'type' => 'string',
                'description' => 'Link Facebook'
            ],
            [
                'key' => 'social_youtube',
                'value' => '',
                'group' => 'general',
                'type' => 'string',
                'description' => 'Link Youtube'
            ],
            [
                'key' => 'social_tiktok',
                'value' => '',
                'group' => 'general',
                'type' => 'string',
                'description' => 'Link Tiktok'
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
