<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Modules\Chatbot\Models\BotConfig;
use Illuminate\Http\Request;

class ChatbotConfigController extends Controller
{
    public function index()
    {
        // Khởi tạo bot mặc định nếu chưa có
        $botConfig = BotConfig::firstOrCreate(
            ['bot_code' => 'toeic_tutor'],
            [
                'system_instruction' => 'You are a strict TOEIC tutor.',
                'temperature' => 0.2,
                'response_schema' => null
            ]
        );

        $apiKey = Setting::get('gemini_api_key');
        $selectedModel = Setting::get('gemini_model', 'gemini-1.5-flash');

        // Thống kê sử dụng nội bộ
        $stats = [
            'total_requests' => \App\Modules\Chatbot\Models\ChatMessage::where('role', 'model')->count(),
            'today_requests' => \App\Modules\Chatbot\Models\ChatMessage::where('role', 'model')->whereDate('created_at', today())->count(),
            'total_tokens'   => \App\Modules\Chatbot\Models\ChatMessage::where('role', 'model')->sum('tokens_used') ?? 0,
        ];

        $frontendUrl = \App\Models\Setting::get('frontend_url', 'system') ?? config('app.url');
        $apiKeys = array_filter(array_map('trim', preg_split('/[,\n]+/', $apiKey)));
        if (empty($apiKeys)) $apiKeys = ['']; // Ít nhất 1 ô trống

        return view('admin.chatbot_config.index', compact('botConfig', 'apiKeys', 'selectedModel', 'stats', 'frontendUrl'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'gemini_api_key' => 'nullable|array',
            'gemini_api_key.*' => 'nullable|string',
            'gemini_model'   => 'required|string',
            'frontend_url'   => 'nullable|url',
            'system_instruction' => 'required|string',
            'temperature' => 'required|numeric|min:0|max:2',
        ]);

        $apiKeysStr = is_array($request->gemini_api_key) ? implode("\n", array_filter(array_map('trim', $request->gemini_api_key))) : '';
        Setting::set('gemini_api_key', $apiKeysStr, 'api');
        Setting::set('gemini_model', $request->gemini_model, 'api');
        if ($request->filled('frontend_url')) {
            Setting::set('frontend_url', rtrim($request->frontend_url, '/'), 'system');
        }

        $botConfig = BotConfig::where('bot_code', 'toeic_tutor')->first();
        if ($botConfig) {
            $botConfig->update([
                'system_instruction' => $request->system_instruction,
                'temperature' => $request->temperature,
            ]);
        }

        return redirect()->back()->with('success', 'Cấu hình Chatbot đã được cập nhật thành công.');
    }

    public function testChat(Request $request, \App\Modules\Chatbot\Services\GeminiChatbotService $service)
    {
        $request->validate([
            'message' => 'nullable|string',
            'session_token' => 'nullable|string',
            'attachment' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        try {
            // Lấy bot config
            $botConfig = BotConfig::where('bot_code', 'toeic_tutor')->first();
            
            $token = $request->session_token ?: 'admin_test_session_' . auth()->id();

            // Tạo một session tạm thời cho Admin
            $session = \App\Modules\Chatbot\Models\ChatSession::firstOrCreate([
                'user_id' => auth()->id(),
                'bot_config_id' => $botConfig->id,
                'session_token' => $token
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

            // Dữ liệu giả lập
            $data = [
                'session_token' => $session->session_token,
                'question_text' => $request->message,
                'user_answer' => 'N/A',
                'correct_answer' => 'N/A',
                'attachment_url' => $attachmentUrl,
                'attachment_data' => $attachmentData,
                'attachment_mime' => $attachmentMime
            ];

            $result = $service->processMessage($data);

            return response()->json([
                'success' => true,
                'data' => $result
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Admin Chatbot Test Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 200);
        }
    }
    public function history()
    {
        $sessions = \App\Modules\Chatbot\Models\ChatSession::with('user')
            ->withCount('messages')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('admin.chatbot_config.history', compact('sessions'));
    }

    public function clearHistory()
    {
        \App\Modules\Chatbot\Models\ChatSession::query()->delete();
        \App\Modules\Chatbot\Models\ChatMessage::query()->delete();
        
        return redirect()->back()->with('success', 'Đã xóa toàn bộ dữ liệu lịch sử Chatbot thành công để giảm tải Database.');
    }

    public function bulkDeleteHistory(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'string'
        ]);

        \App\Modules\Chatbot\Models\ChatSession::whereIn('id', $request->ids)->delete();
        
        return redirect()->back()->with('success', 'Đã xóa các mục lịch sử được chọn thành công.');
    }
}
