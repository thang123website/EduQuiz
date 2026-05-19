<?php

namespace Tests\Feature\Chatbot;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use App\Models\User;
use App\Modules\Chatbot\Models\BotConfig;
use App\Modules\Chatbot\Models\ChatSession;

class GeminiChatbotTest extends TestCase
{
    use RefreshDatabase;

    public function test_chatbot_returns_strict_structured_json_schema()
    {
        // 1. Setup Dữ liệu mẫu giả lập
        $user = User::factory()->create();
        $config = BotConfig::create([
            'bot_code' => 'toeic_tutor',
            'system_instruction' => 'You are a strict TOEIC tutor.',
            'temperature' => 0.1
        ]);
        $session = ChatSession::create([
            'user_id' => $user->id,
            'bot_config_id' => $config->id,
            'session_token' => 'test_session_token_12345678901234567890123456789012345678901234'
        ]);

        // 2. Mocking HTTP client kết nối sang Google API
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'is_correct' => true,
                                        'explanation' => 'Chính xác, trạng từ đi kèm động từ thường.',
                                        'suggested_tips' => ['V + Adv']
                                    ])
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        // 3. Thực thi gọi API Route nội bộ của Laravel
        // Thêm API key auth header giả nếu có VerifyApiKeyMiddleware
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/chatbot/message', [
            'session_token' => 'test_session_token_12345678901234567890123456789012345678901234',
            'question_text' => 'Run _______ to catch the bus.',
            'user_answer' => 'quickly',
            'correct_answer' => 'quickly',
        ], ['x-api-key' => 'test-key']);

        // 4. Kiểm tra cấu trúc mảng JSON trả về có đúng chuẩn thiết kế đầu ra không
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'data' => [
                         'is_correct',
                         'explanation',
                         'suggested_tips'
                     ]
                 ]);
                 
        // Đảm bảo tin nhắn đã được ghi nhận lưu trữ lịch sử âm thầm vào Database
        $this->assertDatabaseHas('chat_messages', [
            'chat_session_id' => $session->id,
            'role' => 'user'
        ]);
    }
}
