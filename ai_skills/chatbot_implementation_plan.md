# Kế hoạch Triển khai Module Chatbot AI (Gemini)

Dựa trên yêu cầu của hệ thống EduQuiz, dưới đây là kế hoạch kiến trúc và triển khai Module Chatbot sử dụng trực tiếp Gemini API Key, đảm bảo hiệu năng cao, không nghẽn, và đầu ra có cấu trúc chuẩn (Structured Outputs).

## 1. Kiến Trúc Luồng Đi Dữ Liệu (Data Flow)
Luồng đi của dữ liệu được thiết kế cô lập qua `ChatbotService`:
1. Client (NextJS/Mobile App) gọi `POST /api/v1/chatbot/message`.
2. `ChatbotController` nhận Request và Validate Input qua `SendMessageRequest`.
3. Gọi tới `GeminiChatbotService` để xử lý business logic.
4. `ChatbotRepository` query lịch sử chat và cấu hình từ Database (MySQL).
5. Xử lý Redis Cache để trả về ngay kết quả nếu câu hỏi/câu trả lời trùng lặp.
6. Gửi payload bao gồm *System Instruction* và *JSON Schema* tới **Google Gemini API v1beta**.
7. Nhận dữ liệu trả về theo đúng định dạng JSON Schema ép buộc.
8. (Optional) Đẩy log/lịch sử vào Queue để xử lý bất đồng bộ.
9. Trả về Standard JSON Response cho Client.

## 2. Thiết Kế Database (3NF & High-Concurrency)
Ba bảng chính cần tạo:
- `bot_configs`: Chứa cấu hình (`bot_code`, `system_instruction`, `temperature`, `response_schema`).
- `chat_sessions`: Quản lý phiên (`user_id`, `bot_config_id`, `session_token`).
- `chat_messages`: Lưu lịch sử (`chat_session_id`, `role`, `content`, `tokens_used`).

## 3. Ràng buộc Input & Output
- **Input (Laravel Validation)**: Yêu cầu `session_token`, `question_text`, `user_answer`, `correct_answer`.
- **Output (Gemini Structured Outputs)**: Ép trả về chuẩn xác 3 trường:
  - `is_correct` (boolean)
  - `explanation` (string - giải thích tiếng Việt)
  - `suggested_tips` (array - mẹo ôn tập)

## 4. Các File Code Sẽ Triển Khai
Toàn bộ mã nguồn sẽ nằm trong module độc lập `app/Modules/Chatbot`:
1. **Migration**: `..._create_chatbot_tables.php`
2. **Models**: `BotConfig.php`, `ChatSession.php`, `ChatMessage.php`
3. **Requests**: `SendMessageRequest.php`
4. **Services**: `GeminiChatbotService.php` (Core logic gọi API Gemini)
5. **Repositories**: `ChatbotRepository.php`
6. **Controllers**: `ChatbotController.php`
7. **Routes**: Bổ sung endpoint trong `routes/api.php` với middleware `throttle:15,1`.
8. **Tests**: `GeminiChatbotTest.php` dùng `Http::fake()` để test mà không tốn phí API.

## 5. Chiến lược tối ưu
- **Redis Cache Pattern**: MD5 băm câu hỏi và đáp án của User, giảm 80% số lượt request thừa tới Gemini API nếu đáp án sai giống hệt nhau.
- **Rate Limiting**: Giới hạn IP tránh phá hoại tài nguyên.

---
> [!NOTE]
> Kế hoạch này được đặt tại `/home/thang/EduQuiz/ai_skills/chatbot_implementation_plan.md`. Sau khi bạn duyệt, chúng ta sẽ bắt tay vào triển khai từng file mã nguồn.
