# Implementation Plan: Secure Bulk Import Questions (Excel)

## 1. Overview and Objective
Implement a robust, safe, and highly optimized Excel bulk import feature for quiz questions in the EduQuiz platform. The implementation will mirror the safety mechanisms found in enterprise systems (like Stackfood), ensuring:
- **Zero Partial Imports**: If one row is invalid, the entire import is rolled back.
- **Memory Efficiency**: Using `FastExcel` and chunking to prevent out-of-memory errors.
- **Data Integrity**: Wrapping everything in Database Transactions.
- **Architecture Alignment**: Inserting data correctly into `questions`, `options`, and the new `question_quiz_part` pivot table.

## 2. Dependencies Setup
Run the following command to install the required lightweight Excel parser:
```bash
composer require rap2hpoutre/fast-excel
```

## 3. Backend Logic (Laravel)

### A. Routes
Add a new route for handling the import specifically for a Quiz Part:
```php
Route::post('quizzes/parts/{part}/import-questions', [QuestionImportController::class, 'import'])
    ->name('quizzes.parts.import-questions');
```

### B. Controller (`QuestionImportController.php`)
**Step 1: File Validation**
- Validate file presence, extensions (`xlsx`, `xls`, `csv`), and size limit (e.g., 5MB).

**Step 2: Excel Parsing & Row Validation (Fail-Fast)**
- Read the file using `FastExcel`.
- Iterate through each row and strictly validate:
  - Required columns: `Type`, `Content`, `Options`, `CorrectIndex`, `DefaultMark`.
  - Logic rules: `DefaultMark >= 0`.
- **Fail-Fast**: If any row fails validation, immediately return a JSON error indicating the exact row number (e.g., "Lỗi ở dòng số 5: Thiếu nội dung câu hỏi"). No database writes occur yet.

**Step 3: Data Mapping & UUID Generation**
- Map the valid rows into structured arrays ready for insertion:
  - `$questionsData`: Array of question attributes (UUID, content, type, default_mark, etc.).
  - `$optionsData`: Array of option attributes (UUID, question_id, text, is_correct).
  - `$pivotData`: Array for `question_quiz_part` (part_id, question_id, order_idx).

**Step 4: Database Transaction & Chunking**
```php
DB::beginTransaction();
try {
    // 1. Insert Questions in chunks of 100
    foreach (array_chunk($questionsData, 100) as $chunk) {
        DB::table('questions')->insert($chunk);
    }

    // 2. Insert Options in chunks of 100
    foreach (array_chunk($optionsData, 100) as $chunk) {
        DB::table('options')->insert($chunk);
    }

    // 3. Insert Pivot Data in chunks of 100
    foreach (array_chunk($pivotData, 100) as $chunk) {
        DB::table('question_quiz_part')->insert($chunk);
    }

    // 4. Recalculate Quiz Totals (question_count, total_points)
    // ...

    DB::commit();
    return response()->json(['success' => true, 'message' => 'Import thành công!']);
} catch (\Exception $e) {
    DB::rollBack();
    return response()->json(['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()], 500);
}
```

## 4. Frontend Integration (Blade Admin Panel)
Currently, the Admin panel uses Blade (`resources/views/admin/quizzes/edit.blade.php`).

### A. Bulk Import Modal
- Create an `#importQuestionsModal`.
- Include a hidden input for `part_id` to know where the questions belong.
- Add a download link for the **Excel Template (`Questions_Template.xlsx`)**.
- Add a file input element (`<input type="file" accept=".xlsx, .xls">`).

### B. AJAX Submission
- Update `openBulkImport(partId)` to show the modal and set the `part_id`.
- Intercept the form submit via JS:
  - Use `FormData` to append the file.
  - Send via `fetch` or `axios` to the backend.
  - Show loading states (e.g., "Đang xử lý, vui lòng không đóng trang...").
  - On success: Close modal, show Toastify success, and reload the questions list.
  - On error: Show SweetAlert or Toastify with the exact row error returned from the backend.

## 5. Excel Template Structure Definition
The `Questions_Template.xlsx` will have the following standardized columns:
- **A: Type** (Loại câu hỏi) -> Vd: `single_choice`, `multiple_answer`
- **B: Content** (Nội dung câu hỏi)
- **C: Options** (Các đáp án) -> Phân cách bằng dấu `|`. Vd: `Apple | Banana | Orange`
- **D: CorrectIndex** (Vị trí đáp án đúng) -> Bắt đầu từ 1. Có thể nhiều đáp án. Vd: `1` hoặc `1|3`
- **E: DefaultMark** (Điểm số) -> Vd: `1.0`
- **F: Explanation** (Giải thích) -> Có thể để trống
- **G: Level** (Độ khó) -> Vd: `easy`, `medium`, `hard`
- **H: Media URL** (Đường dẫn Media) -> Vd: `audio/track1.mp3` hoặc `general/pic.jpg`
- **I: Media Type** (Loại Media) -> `audio`, `image`, hoặc để trống (`none`)

## 6. Edge Cases & Resilience
- **Memory Limit**: File might be too large. FastExcel operates row-by-row (generator), minimizing memory footprint.
- **Data Truncation**: Ensure Excel string lengths do not exceed DB schema limits before insertion.
- **Updates (Upsert)**: For V1, the import will focus on **Append Only** (Thêm mới). Updating existing questions via Excel will be planned for V2 if required.
