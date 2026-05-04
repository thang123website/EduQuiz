<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;

class GeneralNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $data;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
        // Data format: ['title' => '', 'body' => '', 'image' => '', 'url' => '', 'channels' => ['mail', 'database', 'fcm']]
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = $this->data['channels'] ?? ['database'];
        
        \Log::info("Notification via() called for User: {$notifiable->id}. Selected channels: " . implode(', ', $channels));

        // Map 'fcm' string to our custom channel class
        return array_map(function($channel) {
            if ($channel === 'fcm') {
                return \App\Notifications\Channels\FcmChannel::class;
            }
            return $channel;
        }, $channels);
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        \Log::info("Notification toMail() called for User: {$notifiable->email}");

        $image = get_image_url($this->data['image'] ?? null);

        return (new MailMessage)
                    ->subject($this->data['title'] ?? 'Thông báo từ EduQuiz')
                    ->view('emails.general', [
                        'title' => $this->data['title'] ?? 'Thông báo mới',
                        'body'  => $this->data['body'] ?? '',
                        'image' => $image,
                        'url'   => $this->data['url'] ?? null,
                        'name'  => $notifiable->name ?? 'bạn',
                    ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        \Log::info("Notification toDatabase() called for User: {$notifiable->id}");

        return [
            'title' => $this->data['title'] ?? '',
            'body' => $this->data['body'] ?? '',
            'image' => $this->data['image'] ?? null,
            'url' => $this->data['url'] ?? null,
            'type' => $this->data['type'] ?? 'general',
            'sender_id' => $this->data['sender_id'] ?? null,
            'history_id' => $this->data['history_id'] ?? null,
        ];
    }

    /**
     * Get the FCM representation of the notification.
     */
    public function toFcm(object $notifiable): array
    {
        $image = get_image_url($this->data['image'] ?? null);

        return [
            'title' => $this->data['title'],
            'body' => $this->data['body'],
            'image' => $image,
            'extra_data' => [
                'url' => $this->data['url'] ?? '',
                'type' => $this->data['type'] ?? 'general',
            ],
        ];
    }
}
