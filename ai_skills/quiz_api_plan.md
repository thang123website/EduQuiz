# Kế hoạch phát triển API cho Hệ thống Quiz (LMS) - Chuẩn Senior (EduQuiz)

Bản kế hoạch này được nâng cấp với góc nhìn kiến trúc hệ thống lớn (phong cách Study4), giải quyết triệt để các bài toán về: Cấu trúc đề thi phức tạp (Parts & Group), Hiệu năng cao (Caching/Redis), Trải nghiệm thi lại (Multiple Attempts), và Bộ lọc nâng cao.

---

## 1. Phân tích Cấu trúc Đề thi & Trải nghiệm (Kiểu Study4)

Hệ thống có cấu trúc DB hỗ trợ hoàn hảo mô hình luyện thi chuyên sâu (như TOEIC, IELTS):
- **Cấu trúc linh hoạt:** Không chỉ là các câu hỏi rời rạc. Đề thi được chia thành các **Parts** (vd: Part 1 - Listening, Part 7 - Reading).
- **Group Câu hỏi (Parent - Child):** Các phần đọc hiểu/nghe dài có một đoạn văn/audio chung (`parent_id`), bên dưới có nhiều câu hỏi nhỏ (`child questions`).
- **Chế độ làm bài đa dạng (`type`):**
  - `full_test`: Làm toàn bộ các phần với thời gian chuẩn.
  - `practice`: Luyện tập tự do, có thể chọn làm **1 hoặc 1 vài Parts** cụ thể, làm xong xem đáp án ngay hoặc không tính thời gian gắt gao.
  - `minitest`: Đề thi rút gọn.

---

## 2. Giải pháp Kiến trúc & Tối ưu Hiệu suất (Senior Level Optimization)

Để đáp ứng hàng ngàn học viên thi cùng lúc mà không sập hệ thống (DB Bottleneck):

1. **Autosave bằng Redis:** Hành động `PUT /autosave` (lưu nháp mỗi phút) nếu ghi thẳng vào MySQL bảng `user_responses` sẽ làm chết DB ngay lập tức. **Giải pháp:** Ghi `autosave` vào **Redis Cache** (bằng hash mapping `attempt_id`). Khi user gọi `POST /submit` mới tiến hành bulk insert từ Redis vào MySQL bằng 1 query duy nhất.
2. **Database Indexing:** Tạo composite indexes trên các bảng tra cứu nhiều: `quiz_attempts(user_id, quiz_id)`, `user_responses(attempt_id, question_id)`.
3. **Queue / Background Jobs cho Chấm điểm:** Đối với Full Test 200 câu TOEIC, việc check đúng/sai và tính điểm có thể tốn vài chục ms. Phải sử dụng DB Transaction. Nếu có logic gửi email kết quả hay cấp chứng chỉ, phải đẩy qua **Laravel Queue** (Job xử lý ngầm).
4. **Denormalization (Chuẩn hoá ngược):** Dữ liệu `question_count`, `total_points` ở bảng `quizzes` phải được tính sẵn (tạo Observer khi thêm câu hỏi), thay vì mỗi lần GET API lại đi đếm (COUNT) lại.

---

## 3. Quản lý Lượt thi (Multiple Attempts)

Học viên có thể làm 1 đề thi **nhiều lần**:
- Bảng `quiz_attempts` không thiết lập unique `user_id` và `quiz_id`. Mỗi lần bấm "Bắt đầu", sẽ sinh ra 1 UUID `attempt_id` mới.
- **Tính năng Resume:** API `POST /start` sẽ kiểm tra nếu User đang có một `attempt` ở trạng thái `in_progress` đối với bài thi này. Trả về `attempt` cũ để user làm tiếp, thay vì tạo mới (tránh rác data do mạng lag bấm nhiều lần). Nút UI có thể đổi thành "Làm tiếp".
- **Tuỳ chọn làm lại từ đầu:** Nút "Làm lại", gửi kèm tham số `force_new=true` vào API `/start` để ép tạo attempt mới và giữ lại history của attempt cũ ở trạng thái `failed` hoặc `abandoned`.

---

## 4. API Endpoints Chi tiết

### 4.1. Khám phá & Bộ lọc Nâng cao (Discovery)

| HTTP Method | Endpoint | Mô tả & Tối ưu |
|-------------|----------|----------------|
| `GET` | `/api/v1/quizzes` | Lấy danh sách đề thi. <br>🔎 **Bộ lọc đa chiều:** Hỗ trợ `?category_slug=...`, `?tags[]=...`, `?difficulty=...`, `?type=practice`.<br>⚙️ **Tìm kiếm (Search):** Áp dụng Full-Text Search (Elasticsearch hoặc Laravel Scout) cho title/description thay vì `LIKE %...%` gây chậm DB.<br>🛠 Trả về có Pagination. |
| `GET` | `/api/v1/quizzes/{slug}` | Trả về thông tin giới thiệu đề thi. **Đặc biệt:** Kèm theo danh sách các `QuizParts` (vd: Part 1 - 10 câu, Part 2 - 30 câu). |

### 4.2. Khởi tạo & Làm bài (Execution)

| HTTP Method | Endpoint | Mô tả Logic (Chuẩn Study4) |
|-------------|----------|---------------------------|
| `POST` | `/api/v1/quizzes/{id}/start` | **Bắt đầu làm bài**<br>- Payload có thể gửi kèm `part_ids[]` nếu chế độ là `practice` (chỉ làm một số phần).<br>- Thuật toán Map Data: Lấy danh sách Parts => Lấy `question_quiz_part` => Lấy Questions. Gom nhóm các câu hỏi có cùng `parent_id` vào chung 1 object `Group`.<br>- **Bảo mật:** Ẩn `is_correct` bằng `QuestionExecutionResource`. |
| `PUT` | `/api/v1/attempts/{attempt_id}/autosave`| **Lưu nháp câu trả lời**<br>- Payload: `[ { question_id: "...", option_id: "..." } ]`<br>- Xử lý: Ghi thẳng vào **Redis** với TTL bằng thời gian làm bài + 1 giờ bù hao. Rất nhẹ cho server. |
| `POST` | `/api/v1/attempts/{attempt_id}/submit` | **Nộp bài & Chấm điểm (Transaction)**<br>- Sync data từ Redis xuống MySQL (`user_responses`).<br>- Chấm điểm: So khớp `option_id` với `options.is_correct` (được cache ở memory lúc chấm để giảm query).<br>- Nếu chọn làm theo `part_ids[]`, chỉ chấm điểm và tính tổng điểm dựa trên các Part đã làm.<br>- Update `quiz_attempts` -> `completed` kèm `score`. |

### 4.3. Kết quả & Xem lại (Result)

| HTTP Method | Endpoint | Mô tả |
|-------------|----------|-------|
| `GET` | `/api/v1/attempts/{attempt_id}/result` | **Xem chi tiết kết quả**<br>- Trả về Resource cấu trúc hệt như `/start` nhưng BẬT các field `is_correct` và `explanation`.<br>- Kèm thêm map `user_selected_option_id` ở mỗi câu hỏi để Frontend tô đỏ/xanh câu đúng sai (Giao diện review bài). |
| `GET` | `/api/v1/quizzes/{id}/attempts` | Lấy lịch sử tất cả các lần làm của 1 user cho 1 bộ đề cụ thể. Frontend có thể vẽ biểu đồ sự tiến bộ của học viên qua các lần làm. |

### 4.4. Thống kê & Lịch sử Cá nhân (User Dashboard)

| HTTP Method | Endpoint | Mô tả |
|-------------|----------|-------|
| `GET` | `/api/v1/users/me/attempts` | **Lịch sử tổng hợp:** Lấy toàn bộ lịch sử làm bài của User trên mọi bộ đề (phân trang, sắp xếp mới nhất). |
| `GET` | `/api/v1/users/me/statistics` | **Thống kê tổng quan:** Số đề đã làm, tổng số thời gian học, tỷ lệ đúng trung bình, điểm trung bình. |
| `POST`| `/api/v1/users/me/targets` | Cập nhật/Xem mục tiêu điểm số (bảng `user_targets`). |

---

## 5. Kiến trúc Resource API (JSON Mapping)

Đây là cách trả về câu hỏi cha/con (Passage/Audio) cực kỳ quan trọng cho các bài thi đọc/nghe:

```json
{
  "part_id": "uuid",
  "part_title": "Part 7: Reading Comprehension",
  "questions": [
    {
      "id": "group_uuid_1",
      "is_group": true,
      "content": "Đoạn văn Passage dài thòong...",
      "media_url": null,
      "child_questions": [
        {
          "id": "question_uuid_1",
          "content": "What is the main purpose of the passage?",
          "options": [ ... ]
        },
        {
          "id": "question_uuid_2",
          "content": "The word 'it' refers to...",
          "options": [ ... ]
        }
      ]
    },
    {
      "id": "question_uuid_3",
      "is_group": false,
      "content": "Câu hỏi ngữ pháp đơn lẻ",
      "options": [ ... ]
    }
  ]
}
```

## 6. Tổng kết
Với bản kế hoạch này:
1. Giải quyết được bài toán thi theo từng Part.
2. Xử lý được câu hỏi lồng nhau (Đoạn văn + Câu hỏi con).
3. Đảm bảo server không sập khi hàng ngàn người autosave (dùng Redis).
4. Phục vụ học viên làm bài nhiều lần, chấm điểm riêng biệt, vẽ biểu đồ tiến bộ.
