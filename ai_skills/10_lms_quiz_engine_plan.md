# LMS Quiz Engine Implementation Plan

This plan outlines the step-by-step implementation of the high-performance Quiz Engine for EduQuiz, following the "Final Consolidated MySQL Plan".

## 1. Database Schema (High-Performance Foundation)

To ensure scalability and performance, we use **UUID v7 (Time-sortable)** for all Primary Keys and Foreign Keys, stored as `CHAR(36)`.

- [x] **Migrations Implementation**: Consolidated migration created and executed via Docker.
- [x] **Table `categories`**: Self-referencing tree, slug, type, order.
- [x] **Table `quizzes`**: Title, pass mark, duration, difficulty, status.
- [x] **Table `tags` & `quiz_tag`**: Multi-dimensional filtering (e.g., Year 2024, ETS).
- [x] **Table `quiz_parts`**: Structured sections for TOEIC/IELTS (Part 1 to Part 7).
- [x] **Table `questions`**: Markdown, media URL, grade, explanation, TOEIC grouping, `part_id`.
- [x] **Table `options`**: Text and correctness flag.
- [x] **Table `quiz_attempts`**: User score, status, time spent.
- [x] **Table `user_responses`**: Linked to attempts, denormalized `is_correct`, `unique(['attempt_id', 'question_id'])`.
- [x] **Table `user_targets`**: Track user exam date and target score.

---

## 2. Backend Architecture (Service & Repository)

To handle 10k+ users and ensure maintainable code, we will implement the **Repository Pattern**.

### 2.1 Pattern Implementation
- [x] **Repository Layer**: Create `App\Repositories\QuizRepository` and `CategoryRepository`.
    - Use Eager Loading (`with`, `withCount`) to prevent N+1 issues in Admin Tree views.
- [x] **Service Layer**: Create `App\Services\QuizService` for complex logic (e.g., grading, attempt management).

### 2.2 UUID v7 Logic
- [x] Use `HasUuidv7` trait for all new tables.
- [x] Ensure non-incrementing primary keys in model configuration.

---

## 3. Admin & Power Tools

### 3.1 Power Builder & Bulk Upload
- [ ] **Markdown Editor**: Integrated for question content.
- [ ] **Secure Media**: 
    - Integration with S3/Cloudinary.
    - **Private S3 Buckets + Presigned URLs** for TOEIC audio files to prevent piracy.
- [ ] **Bulk Import (Laravel Excel)**:
    - Implement `ToModel`, `WithBatchInserts`, and `WithChunkReading`.
    - Handle 10k+ rows efficiently without memory leaks.

### 3.2 Analytics & Management
- [ ] Real-time statistics of quiz completion rates.
- [ ] Drag & Drop category and tag management with N+1 optimization.
- [ ] Quiz Parts management interface (reorder parts, assign questions).
- [ ] Descriptive question review interface.

---

## 4. Student Quiz Engine

### 4.1 Interface & Logic
- [ ] Focused Quiz UI with sidebar navigation.
- [ ] Real-time timer and auto-save (User Responses).
- [ ] Result summary with detailed explanation review.
- [ ] **Test Modes**: Support "Full Test" and "Mini Test" (random/shortened).
- [ ] **Target Tracking**: Display countdown to exam and compare current score with target.
- [ ] **Scaled Scoring**: Convert absolute grade sum to standard scores (e.g., TOEIC 990 mapping).

---

## 5. Performance Checklist
- [ ] Composite indexes on `(quiz_id, order_idx)` and `(attempt_id, question_id)`.
- [ ] Use `JSON` metadata for flexible settings in `quizzes` table.
- [ ] CDN integration for media assets.
