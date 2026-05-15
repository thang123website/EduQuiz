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
