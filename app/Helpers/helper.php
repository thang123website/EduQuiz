<?php

use App\Models\User;
use App\Models\NotificationHistory;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;
use Carbon\Carbon;

if (!function_exists('sendNotification')) {
    /**
     * Gửi thông báo đa kênh (Email, Database, FCM)
     * 
     * @param array $data ['title', 'body', 'image', 'url']
     * @param array $options ['channels', 'audience_type', 'target_id']
     * @param string|null $user_id (Shortcut cho audience_type=single)
     */
    function sendNotification($data, $options = [], $user_id = null)
    {
        try {
            $audienceType = $options['audience_type'] ?? 'single';
            $targetId = $user_id ?? ($options['target_id'] ?? null);
            $channels = $options['channels'] ?? ['database'];

            // 1. Xác định đối tượng nhận
            $users = match ($audienceType) {
                'all' => User::all(),
                'students' => User::where('role_name', 'Học sinh')->orWhere('role_name', 'student')->get(),
                'admins' => User::where('role_name', 'Quản trị viên')->orWhere('role_name', 'admin')->get(),
                'single' => User::where('id', $targetId)->get(),
                default => collect([]),
            };

            if ($users->isEmpty()) {
                return false;
            }

            // 2. Lưu lịch sử
            $history = NotificationHistory::create([
                'title' => $data['title'] ?? '',
                'body' => $data['body'] ?? '',
                'audience_type' => $audienceType,
                'channels' => $channels,
                'image' => $data['image'] ?? null,
                'url' => $data['url'] ?? null,
                'sender_id' => Auth::id(),
                'user_count' => $users->count(),
            ]);

            // 3. Chuẩn bị dữ liệu gửi
            $notificationData = [
                'title'      => $data['title'] ?? '',
                'body'       => $data['body'] ?? '',
                'channels'   => $channels,
                'url'        => $data['url'] ?? null,
                'image'      => $data['image'] ?? null,
                'sender_id'  => Auth::id(),
                'history_id' => $history->id, // Gắn ID lịch sử vào đây
            ];

            // 4. Gửi thông báo
            Notification::send($users, new GeneralNotification($notificationData));

            return true;
        } catch (\Exception $e) {
            \Log::error("Helper sendNotification Error: " . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('display_datetime')) {
    /**
     * Chuyển đổi thời gian UTC sang múi giờ của hệ thống hoặc của user hiện tại
     * 
     * @param mixed $carbonDate
     * @param string $format
     * @return string
     */
    function display_datetime($carbonDate, $format = 'd/m/Y H:i:s')
    {
        if (!$carbonDate) return '';
        
        $timezone = 'Asia/Ho_Chi_Minh'; // Default fallback
        
        if (auth()->check() && auth()->user()->timezone) {
            $timezone = auth()->user()->timezone;
        } else {
            $timezone = Setting::get('system_timezone', 'Asia/Ho_Chi_Minh');
        }
        
        return Carbon::parse($carbonDate)->timezone($timezone)->format($format);
    }
}

if (!function_exists('auto_translator')) {
    /**
     * Tự động dịch sử dụng endpoint nội bộ của Google (miễn phí)
     * Giống cách Stackfood áp dụng
     * 
     * @param string $q Text cần dịch
     * @param string $sl Ngôn ngữ nguồn (Source Language)
     * @param string $tl Ngôn ngữ đích (Target Language)
     * @return string
     */
    function auto_translator($q, $sl, $tl)
    {
        try {
            // Nếu văn bản trống thì bỏ qua
            if (empty(trim($q))) return $q;

            // Sử dụng POST thay vì GET để tránh lỗi "414 URI Too Long" khi dịch bài viết dài (HTML)
            $url = "https://translate.googleapis.com/translate_a/single?client=gtx&ie=UTF-8&oe=UTF-8&dt=bd&dt=ex&dt=ld&dt=md&dt=qca&dt=rw&dt=rm&dt=ss&dt=t&dt=at&sl=" . $sl . "&tl=" . $tl . "&hl=hl";
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "q=" . urlencode($q));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $response = curl_exec($ch);
            curl_close($ch);
            
            $res = json_decode($response, true);
            
            $translatedText = '';
            // Kết quả của Google trả về mảng các câu, ta cần nối chúng lại
            if (isset($res[0]) && is_array($res[0])) {
                foreach ($res[0] as $sentence) {
                    if (isset($sentence[0])) {
                        $translatedText .= $sentence[0];
                    }
                }
                return str_replace('_', ' ', $translatedText);
            }
        } catch (\Exception $e) {
            \Log::error("Google Translate Helper Error: " . $e->getMessage());
        }
        return $q; // Trả về text gốc nếu lỗi
    }
}

if (!function_exists('get_active_languages')) {
    /**
     * Lấy danh sách ngôn ngữ đang kích hoạt từ hệ thống
     * 
     * @return array
     */
    function get_active_languages()
    {
        $languages = json_decode(\App\Models\Setting::get('system_language', '[]'), true);
        if (empty($languages)) {
            $languages = [['code' => 'vi', 'name' => 'Vietnamese', 'default' => true]];
        }
        return $languages;
    }
}

if (!function_exists('translatable_rules')) {
    /**
     * Tạo validation rules cho các trường đa ngôn ngữ
     * 
     * @param string|array $fields Ví dụ: 'title' hoặc ['title', 'description']
     * @param string|array $rules Rule cơ bản, VD: 'required|string|max:255'
     * @return array
     */
    function translatable_rules($fields, $rules)
    {
        $languages = get_active_languages();
        $defaultLang = $languages[0]['code'] ?? 'vi';
        
        $fields = (array) $fields;
        $result = [];
        
        $baseRules = is_array($rules) ? $rules : explode('|', $rules);
        $isRequired = in_array('required', $baseRules);
        
        $optionalRules = array_filter($baseRules, fn($r) => $r !== 'required');
        array_unshift($optionalRules, 'nullable');
        
        foreach ($fields as $field) {
            $result[$field] = $isRequired ? 'required|array' : 'nullable|array';
            $result["{$field}.{$defaultLang}"] = implode('|', $baseRules);
            $result["{$field}.*"] = implode('|', $optionalRules);
        }
        
        return $result;
    }
}
