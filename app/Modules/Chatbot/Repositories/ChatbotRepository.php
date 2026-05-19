<?php

namespace App\Modules\Chatbot\Repositories;

use App\Modules\Chatbot\Models\ChatSession;
use App\Modules\Chatbot\Models\ChatMessage;

class ChatbotRepository
{
    public function findSessionByToken(string $token): ChatSession
    {
        return ChatSession::with('botConfig')->where('session_token', $token)->firstOrFail();
    }

    public function saveMessage(int $sessionId, string $role, string $content, ?int $tokensUsed = null, ?string $attachmentUrl = null): ChatMessage
    {
        return ChatMessage::create([
            'chat_session_id' => $sessionId,
            'role' => $role,
            'content' => $content,
            'attachment_url' => $attachmentUrl,
            'tokens_used' => $tokensUsed
        ]);
    }

    public function getChatHistoryForGemini(int $sessionId): array
    {
        // Lấy 10 tin nhắn MỚI NHẤT nhưng sắp xếp lại theo chiều thời gian tiến (cũ -> mới) cho AI hiểu
        $messages = ChatMessage::where('chat_session_id', $sessionId)
            ->latest('id')
            ->take(10)
            ->get()
            ->reverse()
            ->values();

        return $messages->map(function ($msg) {
            return [
                'role' => $msg->role === 'user' ? 'user' : 'model',
                'parts' => [
                    ['text' => $msg->content]
                ]
            ];
        })->toArray();
    }
}
