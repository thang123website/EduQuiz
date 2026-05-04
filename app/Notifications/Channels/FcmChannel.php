<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FcmChannel
{
    /**
     * Send the given notification.
     */
    public function send(object $notifiable, Notification $notification): void
    {
        if (!method_exists($notification, 'toFcm')) {
            return;
        }

        $data = $notification->toFcm($notifiable);
        
        $tokens = DB::table('user_fcm_tokens')
            ->where('user_id', $notifiable->id)
            ->where('is_active', true)
            ->pluck('fcm_token')
            ->toArray();

        if (empty($tokens)) {
            return;
        }

        $messaging = Firebase::messaging();
        
        $fcmNotification = FirebaseNotification::create($data['title'], $data['body']);
        
        if (!empty($data['image'])) {
            $fcmNotification = $fcmNotification->withImageUrl($data['image']);
        }

        $message = CloudMessage::new()
            ->withNotification($fcmNotification)
            ->withData($data['extra_data'] ?? []);

        try {
            $messaging->sendMulticast($message, $tokens);
        } catch (\Exception $e) {
            Log::error('FCM Channel Error: ' . $e->getMessage());
        }
    }
}
