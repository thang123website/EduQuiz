<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class MailSettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            ['key' => 'mail_driver',       'value' => 'smtp',                     'description' => 'Mail Driver',       'group' => 'mail', 'is_encrypted' => false],
            ['key' => 'mail_host',         'value' => env('MAIL_HOST', 'smtp.gmail.com'), 'description' => 'SMTP Host', 'group' => 'mail', 'is_encrypted' => false],
            ['key' => 'mail_port',         'value' => env('MAIL_PORT', '587'),     'description' => 'SMTP Port',         'group' => 'mail', 'is_encrypted' => false],
            ['key' => 'mail_username',     'value' => env('MAIL_USERNAME', ''),   'description' => 'SMTP Username',     'group' => 'mail', 'is_encrypted' => false],
            ['key' => 'mail_password',     'value' => '',                         'description' => 'SMTP Password',     'group' => 'mail', 'is_encrypted' => true],
            ['key' => 'mail_encryption',   'value' => env('MAIL_ENCRYPTION', 'tls'), 'description' => 'SMTP Encryption', 'group' => 'mail', 'is_encrypted' => false],
            ['key' => 'mail_from_address', 'value' => env('MAIL_FROM_ADDRESS', 'noreply@eduquiz.vn'), 'description' => 'From Address', 'group' => 'mail', 'is_encrypted' => false],
            ['key' => 'mail_from_name',    'value' => env('MAIL_FROM_NAME', 'EduQuiz'), 'description' => 'From Name', 'group' => 'mail', 'is_encrypted' => false],
        ];

        foreach ($defaults as $item) {
            Setting::updateOrCreate(['key' => $item['key']], $item);
        }
    }
}
