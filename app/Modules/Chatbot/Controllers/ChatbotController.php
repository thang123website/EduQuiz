<?php

namespace App\Modules\Chatbot\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Chatbot\Requests\SendMessageRequest;
use App\Modules\Chatbot\Services\GeminiChatbotService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ChatbotController extends Controller
{
    protected GeminiChatbotService $botService;

    public function __construct(GeminiChatbotService $botService)
    {
        $this->botService = $botService;
    }

    public function sendMessage(SendMessageRequest $request): JsonResponse
    {
        try {
            $botConfig = \App\Modules\Chatbot\Models\BotConfig::where('bot_code', 'toeic_tutor')->first();
            
            $session = \App\Modules\Chatbot\Models\ChatSession::firstOrCreate([
                'user_id' => auth()->id(),
                'bot_config_id' => $botConfig->id,
                'session_token' => $request->session_token
            ]);

            // Xử lý upload ảnh (nếu có)
            $attachmentUrl = null;
            $attachmentData = null;
            $attachmentMime = null;
            
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                
                // CÁCH 1: KHÔNG LƯU Ổ CỨNG. Chỉ lấy Base64 trực tiếp từ file Temp
                $attachmentData = base64_encode(file_get_contents($file->getRealPath()));
                $attachmentMime = $file->getClientMimeType();
                $attachmentUrl = null; // Không sinh URL vì không lưu
            }

            $data = [
                'session_token' => $session->session_token,
                'question_text' => $request->message ?? '',
                'user_answer' => 'N/A',
                'correct_answer' => 'N/A',
                'attachment_url' => $attachmentUrl,
                'attachment_data' => $attachmentData,
                'attachment_mime' => $attachmentMime
            ];

            $result = $this->botService->processMessage($data);

            return response()->json([
                'success' => true,
                'data' => $result,
                'user_avatar' => auth()->check() ? auth()->user()->avatar_url : null
            ], Response::HTTP_OK);
            
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Chatbot API Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_OK);
        }
    }
}
