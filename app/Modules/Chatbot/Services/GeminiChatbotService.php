<?php

namespace App\Modules\Chatbot\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\Setting;
use App\Models\Quiz;
use App\Modules\Chatbot\Repositories\ChatbotRepository;
use Exception;

class GeminiChatbotService
{
    protected string $apiKey;
    protected string $modelName;
    protected string $endpoint;
    protected ChatbotRepository $repo;

    public function __construct(ChatbotRepository $repo)
    {
        $this->apiKey = Setting::get('gemini_api_key') ?: config('services.gemini.key', '');
        $this->modelName = Setting::get('gemini_model') ?: 'gemini-1.5-flash';
        
        // Sử dụng phiên bản v1beta để hỗ trợ đầy đủ tính năng responseSchema cường độ cao
        $this->endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$this->modelName}:generateContent";
        $this->repo = $repo;
    }

    public function processMessage(array $validatedData): array
    {
        $session = $this->repo->findSessionByToken($validatedData['session_token']);
        $config = $session->botConfig;

        $isApiCacheEnabled = Setting::get('api_cache_enabled', 0) == '1';
        $apiCacheDuration = (int) Setting::get('api_cache_duration', 3600);

        // 1. Định dạng chuỗi Prompt đầu vào chuẩn hóa
        $userPrompt = "Question: " . $validatedData['question_text'] . "\n" .
                      "User Selected: " . $validatedData['user_answer'] . "\n" .
                      "Correct Answer: " . $validatedData['correct_answer'];

        // Tích hợp Context RAG (Retrieval-Augmented Generation) siêu nhẹ
        // Nếu câu hỏi của User chứa từ khóa tìm đề thi, ta chèn thêm danh sách Đề thi thật vào
        $userQuestionLower = mb_strtolower($validatedData['question_text'], 'UTF-8');
        $quizKeywords = ['đề thi', 'bài kiểm tra', 'quiz', 'toeic', 'ielts', 'có bài nào', 'tìm', 'ôn tập'];
        
        $wantsQuiz = false;
        foreach ($quizKeywords as $keyword) {
            if (strpos($userQuestionLower, $keyword) !== false) {
                $wantsQuiz = true;
                break;
            }
        }

        if ($wantsQuiz) {
            if ($isApiCacheEnabled) {
                $quizzes = Cache::remember('chatbot_rag_quizzes_v2', $apiCacheDuration, function () {
                    return Quiz::latest()->limit(5)->get(['id', 'title'])->toArray();
                });
            } else {
                $quizzes = Quiz::latest()->limit(5)->get(['id', 'title'])->toArray();
            }
            if (!empty($quizzes)) {
                $domain = Setting::get('frontend_url', 'system') ?? config('app.url'); // Lấy domain của hệ thống cấu hình
                $userPrompt .= "\n\n[SYSTEM CONTEXT: DANH SÁCH ĐỀ THI HIỆN CÓ TRÊN EDUQUIZ]:\n";
                foreach ($quizzes as $quiz) {
                    $quizUrl = rtrim($domain, '/') . "/test/" . $quiz['id'];
                    $userPrompt .= "- Tên bài: {$quiz['title']} (Link: {$quizUrl})\n";
                }
                $userPrompt .= "\n(Hãy dùng danh sách trên để giới thiệu cho User. Khi nhắc đến đề thi nào, BẮT BUỘC phải đính kèm đường Link để User click vào học. Bạn có thể cho vào mảng suggested_tips).";
            }
        }

        // 2. Tối ưu Cache (Redis/File) - Trả về ngay nếu câu hỏi, hình ảnh và câu trả lời trùng khớp
        $attachmentHash = !empty($validatedData['attachment_data']) ? md5($validatedData['attachment_data']) : '';
        $cacheKey = 'bot_cache_v2:' . md5($userPrompt . $attachmentHash . $config->system_instruction . $config->temperature);
        if ($isApiCacheEnabled && Cache::has($cacheKey)) {
            $cachedData = Cache::get($cacheKey);
            // Lưu log lịch sử âm thầm
            $this->repo->saveMessage($session->id, 'user', $userPrompt, null, $validatedData['attachment_url'] ?? null);
            $this->repo->saveMessage($session->id, 'model', json_encode($cachedData));
            
            return $cachedData;
        }

        // 3. Lấy lịch sử CŨ từ DB trước để Gemini nắm được mạch hội thoại (tối ưu DB Read)
        $history = $this->repo->getChatHistoryForGemini($session->id);

        // 4. Nhét thêm tin nhắn MỚI của User vào mảng RAM (Chưa lưu DB vội, phòng trường hợp AI lỗi thì không bị lưu rác)
        $userParts = [];
        if (!empty($userPrompt)) {
            $userParts[] = ['text' => $userPrompt];
        } else {
            $userParts[] = ['text' => 'Hãy phân tích hình ảnh này và giải đáp cho tôi.'];
        }
        
        // Nếu có ảnh upload, đính kèm vào mảng parts theo chuẩn Gemini
        if (!empty($validatedData['attachment_data']) && !empty($validatedData['attachment_mime'])) {
            $userParts[] = [
                'inlineData' => [
                    'mimeType' => $validatedData['attachment_mime'],
                    'data' => $validatedData['attachment_data']
                ]
            ];
        }

        $history[] = [
            'role' => 'user',
            'parts' => $userParts
        ];

        // 5. Định nghĩa cấu trúc ĐẦU RA (Output Schema) nghiêm ngặt
        $jsonRule = "\n\nCRITICAL: Bạn BẮT BUỘC phải trả về đúng định dạng JSON sau (không chứa markdown hay text thừa):\n";
        $jsonRule .= "{\n";
        $jsonRule .= '  "is_correct": boolean (true/false),' . "\n";
        $jsonRule .= '  "explanation": "Giải thích chi tiết bằng tiếng Việt có dấu chuẩn",'. "\n";
        $jsonRule .= '  "suggested_tips": ["Mẹo 1", "Mẹo 2"]' . "\n";
        $jsonRule .= "}";

        // 6. Khởi tạo Payload theo chuẩn Google AI Documentation
        $payload = [
            'contents' => $history,
            'systemInstruction' => [
                'parts' => [
                    ['text' => $config->system_instruction . $jsonRule]
                ]
            ],
            'generationConfig' => [
                'temperature' => $config->temperature,
                'responseMimeType' => 'application/json'
            ]
        ];

        // 7. Hỗ trợ nhiều API Key cùng lúc để chống hết hạn mức (Fallback)
        // Chấp nhận cách nhau bởi dấu phẩy hoặc xuống dòng
        $apiKeys = array_filter(array_map('trim', preg_split('/[,\n]+/', $this->apiKey)));
        if (empty($apiKeys)) {
            throw new Exception('Chưa cấu hình API Key. Hệ thống Chatbot không thể hoạt động.');
        }

        $response = null;
        $lastException = null;

        foreach ($apiKeys as $key) {
            try {
                $response = Http::timeout(30)
                    ->retry(2, 500, function ($exception, $request) {
                        return $exception instanceof \Illuminate\Http\Client\ConnectionException ||
                               ($exception instanceof \Illuminate\Http\Client\RequestException && $exception->response->serverError());
                    })
                    ->withHeaders(['x-goog-api-key' => $key])
                    ->post($this->endpoint, $payload);
                    
                if ($response->successful()) {
                    break; // Thành công thì thoát vòng lặp ngay
                }

                $status = $response->status();
                if ($status != 429) {
                    // Nếu lỗi không phải do hết hạn mức, ném lỗi luôn không cần thử key khác
                    Log::error('Gemini API Integration Failed', ['body' => $response->body()]);
                    throw new Exception('Hệ thống Chatbot đang được bảo trì (' . $status . ').');
                }
            } catch (\Illuminate\Http\Client\RequestException $e) {
                $status = $e->response->status();
                if ($status != 429) {
                    if ($status == 400) throw new Exception('Hệ thống Chatbot đang được bảo trì (Cấu hình không hợp lệ).');
                    throw new Exception('Hệ thống Chatbot đang được bảo trì. Vui lòng thử lại sau.');
                }
            } catch (\Exception $e) {
                $lastException = $e;
            }
            // Nếu xuống đến đây (Lỗi 429 hoặc lỗi mạng), vòng lặp sẽ tiếp tục thử Key tiếp theo
        }

        if (!$response || $response->failed()) {
            if ($lastException) {
                throw $lastException;
            }
            throw new Exception('Tất cả API Key đều đã hết hạn mức sử dụng (Quá tải Google API). Vui lòng chờ thêm 1 phút rồi thử lại.');
        }

        // 8. Parse kết quả phản hồi an toàn
        $rawText = $response->json('candidates.0.content.parts.0.text');
        $cleanData = json_decode($rawText, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('AI responded with invalid JSON structural format.');
        }

        // 9. Lưu đồng thời CẢ tin nhắn User VÀ phản hồi Model vào DB sau khi mọi thứ đã thành công
        $this->repo->saveMessage($session->id, 'user', $userPrompt, null, $validatedData['attachment_url'] ?? null);
        
        $totalTokens = $response->json('usageMetadata.totalTokenCount') ?? 0;
        $this->repo->saveMessage($session->id, 'model', $rawText, $totalTokens);

        // 10. Lưu Cache để lần sau User khác hỏi giống hệt sẽ dùng lại luôn (nếu được bật)
        if ($isApiCacheEnabled) {
            Cache::put($cacheKey, $cleanData, now()->addSeconds($apiCacheDuration));
        }

        return $cleanData;
    }
}
