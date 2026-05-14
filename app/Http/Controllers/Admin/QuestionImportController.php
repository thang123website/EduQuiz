<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuizPart;
use App\Models\Quiz;
use App\Models\MediaFolder;
use App\Models\MediaFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Rap2hpoutre\FastExcel\FastExcel;
use Exception;

class QuestionImportController extends Controller
{
    public function import(Request $request, QuizPart $part)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('exams.create')) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền thao tác.'], 403);
        }

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv,txt|max:10240', // Max 10MB
        ], [
            'file.mimes' => 'File phải có định dạng .xlsx, .xls hoặc .csv'
        ]);

        try {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $tempDir = storage_path('app/temp');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            $fullPath = $tempDir . '/' . uniqid('import_') . '.' . $extension;
            copy($file->getRealPath(), $fullPath);
            
            $collections = (new FastExcel)->import($fullPath);
            
            // Cleanup temp file
            @unlink($fullPath);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi đọc file: ' . $e->getMessage()], 400);
        }

        $questionsData = [];
        $optionsData = [];
        $pivotData = [];

        $parentMap = [];

        DB::beginTransaction();

        try {
            $orderIdx = $part->questions()->count() + 1;

            foreach ($collections as $index => $row) {
                // Map columns
                $groupId = trim($row['Mã Nhóm'] ?? '');
                $type = trim($row['Loại câu hỏi'] ?? '');
                $content = trim($row['Nội dung câu hỏi'] ?? '');
                $optionsRaw = trim($row['Các đáp án'] ?? '');
                $correctIndexRaw = trim($row['Vị trí đáp án đúng'] ?? '');
                $defaultMark = trim($row['Điểm mặc định'] ?? '');
                if ($defaultMark === '') $defaultMark = 1.0;
                $explanation = trim($row['Giải thích'] ?? '');
                if ($explanation === '') $explanation = null;
                $level = trim($row['Độ khó'] ?? '');
                if ($level === '') $level = 'medium';
                $mediaUrlInput = trim($row['Media URL'] ?? '');
                if ($mediaUrlInput === '') $mediaUrlInput = null;
                $mediaTypeInput = trim($row['Loại Media'] ?? '');
                if ($mediaTypeInput === '') $mediaTypeInput = 'none';
                $extraImageUrlInput = trim($row['Extra Image URL'] ?? '');
                if ($extraImageUrlInput === '') $extraImageUrlInput = null;

                if (empty($type) || empty($content)) {
                    throw new Exception("Lỗi ở dòng số " . ($index + 2) . ": Điền thiếu thông tin bắt buộc (Loại, Nội dung).");
                }

                if ($type !== 'group' && (empty($optionsRaw) || (string)$correctIndexRaw === '')) {
                    throw new Exception("Lỗi ở dòng số " . ($index + 2) . ": Câu hỏi thường bắt buộc phải có Đáp án và Vị trí đúng.");
                }

                if (!in_array($type, ['single_choice', 'multiple_answer', 'group'])) {
                    throw new Exception("Lỗi ở dòng số " . ($index + 2) . ": Loại câu hỏi không hợp lệ ($type).");
                }

                // Handle Media Downloader
                $mediaUrl = null;
                $mediaType = strtolower($mediaTypeInput);
                if (!in_array($mediaType, ['image', 'audio', 'none'])) {
                    $mediaType = 'none';
                }

                if (!empty($mediaUrlInput) && $mediaType !== 'none') {
                    $delimiters = ['|', ','];
                    $mediaUrls = [$mediaUrlInput];
                    
                    foreach ($delimiters as $delimiter) {
                        if (strpos($mediaUrlInput, $delimiter) !== false) {
                            $mediaUrls = explode($delimiter, $mediaUrlInput);
                            break;
                        }
                    }

                    foreach ($mediaUrls as $idx => $urlItem) {
                        $urlItem = trim($urlItem);
                        if (empty($urlItem)) continue;

                        $downloadedPath = $this->downloadAndSaveMedia($urlItem, $mediaType, $index);
                        
                        if ($idx === 0) {
                            $mediaUrl = $downloadedPath;
                        } else {
                            if (!filter_var($downloadedPath, FILTER_VALIDATE_URL) && !Str::startsWith($downloadedPath, '/')) {
                                $markdownUrl = '/storage/' . $downloadedPath;
                            } else {
                                $markdownUrl = $downloadedPath;
                            }

                            if ($mediaType === 'image') {
                                $content = "![Image](" . $markdownUrl . ")\n\n" . $content;
                            } else {
                                $content = "[Media](" . $markdownUrl . ")\n\n" . $content;
                            }
                        }
                    }
                }

                // Handle Extra Image (Hỗ trợ nhiều ảnh, cách nhau bởi dấu | hoặc ,)
                if (!empty($extraImageUrlInput)) {
                    $delimiters = ['|', ','];
                    $extraImageUrls = [$extraImageUrlInput];
                    
                    foreach ($delimiters as $delimiter) {
                        if (strpos($extraImageUrlInput, $delimiter) !== false) {
                            $extraImageUrls = explode($delimiter, $extraImageUrlInput);
                            break;
                        }
                    }

                    $markdownImages = [];
                    foreach ($extraImageUrls as $urlItem) {
                        $urlItem = trim($urlItem);
                        if (empty($urlItem)) continue;

                        $downloadedImagePath = $this->downloadAndSaveMedia($urlItem, 'image', $index);
                        
                        // Nếu không phải là URL đầy đủ (đã được tải về làm relative path), thêm prefix /storage/
                        if (!filter_var($downloadedImagePath, FILTER_VALIDATE_URL) && !Str::startsWith($downloadedImagePath, '/')) {
                            $markdownUrl = '/storage/' . $downloadedImagePath;
                        } else {
                            $markdownUrl = $downloadedImagePath;
                        }

                        $markdownImages[] = "![Image](" . $markdownUrl . ")";
                    }

                    if (!empty($markdownImages)) {
                        $content = implode("\n\n", $markdownImages) . "\n\n" . $content;
                    }
                }

                $questionId = (string) Str::uuid();
                
                $parentId = null;
                if ($type === 'group' && !empty($groupId)) {
                    $parentMap[$groupId] = $questionId;
                    $defaultMark = 0; // Nhóm không có điểm
                } elseif ($type !== 'group' && !empty($groupId)) {
                    if (!isset($parentMap[$groupId])) {
                        throw new Exception("Lỗi ở dòng số " . ($index + 2) . ": Mã Nhóm '$groupId' không tìm thấy câu hỏi Nhóm nào được khai báo trước đó.");
                    }
                    $parentId = $parentMap[$groupId];
                }

                $questionsData[] = [
                    'id' => $questionId,
                    'parent_id' => $parentId,
                    'type' => $type,
                    'content' => $content,
                    'media_type' => $mediaType,
                    'media_url' => $mediaUrl,
                    'default_mark' => (float) $defaultMark,
                    'explanation' => $explanation,
                    'level' => $level,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $pivotData[] = [
                    'part_id' => $part->id,
                    'question_id' => $questionId,
                    'order_idx' => $orderIdx++,
                    'mark' => null,
                ];

                // Options (Chỉ tạo options cho câu hỏi không phải group)
                if ($type !== 'group') {
                    $optionsArray = explode('|', $optionsRaw);
                    $correctIndices = array_map('trim', explode('|', (string)$correctIndexRaw));

                    foreach ($optionsArray as $oIdx => $optText) {
                        $isCorrect = in_array((string)($oIdx + 1), $correctIndices);
                        $optionsData[] = [
                            'id' => (string) Str::uuid(),
                            'question_id' => $questionId,
                            'text' => trim($optText),
                            'is_correct' => $isCorrect ? 1 : 0,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
            }

            // Chunk inserts
            foreach (array_chunk($questionsData, 100) as $chunk) {
                DB::table('questions')->insert($chunk);
            }
            foreach (array_chunk($optionsData, 100) as $chunk) {
                DB::table('options')->insert($chunk);
            }
            foreach (array_chunk($pivotData, 100) as $chunk) {
                DB::table('question_quiz_part')->insert($chunk);
            }

            // Recalculate Quiz Counters
            $quiz = $part->quiz;
            $quiz->question_count = DB::table('question_quiz_part')
                ->join('quiz_parts', 'question_quiz_part.part_id', '=', 'quiz_parts.id')
                ->join('questions', 'question_quiz_part.question_id', '=', 'questions.id')
                ->where('quiz_parts.quiz_id', $quiz->id)
                ->where('questions.type', '!=', 'group')
                ->count();
            
            $quiz->total_points = DB::table('question_quiz_part')
                ->join('quiz_parts', 'question_quiz_part.part_id', '=', 'quiz_parts.id')
                ->join('questions', 'question_quiz_part.question_id', '=', 'questions.id')
                ->where('quiz_parts.quiz_id', $quiz->id)
                ->sum(DB::raw('COALESCE(question_quiz_part.mark, questions.default_mark)'));
            
            $quiz->save();

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => 'Import thành công ' . count($questionsData) . ' câu hỏi!'
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 400); // Bad request 
        }
    }
    private function downloadAndSaveMedia($urlInput, $mediaType, $index)
    {
        if (empty($urlInput)) return null;

        if (filter_var($urlInput, FILTER_VALIDATE_URL)) {
            $host = parse_url($urlInput, PHP_URL_HOST);
            $appHost = request()->getHost();

            // Bỏ qua nếu là domain hiện tại
            if ($host === $appHost) {
                return $urlInput;
            }

            try {
                $response = Http::timeout(15)->get($urlInput);
                if ($response->successful()) {
                    $ext = pathinfo(parse_url($urlInput, PHP_URL_PATH), PATHINFO_EXTENSION);
                    if (!$ext) $ext = $mediaType === 'audio' ? 'mp3' : 'jpg';
                    
                    $folderSlug = $mediaType === 'audio' ? 'audio_question' : 'image_question';
                    $folderName = $mediaType === 'audio' ? 'Audio Question' : 'Image Question';
                    
                    $mediaFolder = MediaFolder::firstOrCreate(
                        ['slug' => $folderSlug],
                        [
                            'name' => $folderName, 
                            'user_id' => auth()->id() ?? 1
                        ]
                    );

                    $filename = $folderSlug . '/imported_' . Str::random(8) . '_' . time() . '.' . $ext;
                    
                    $fileContent = $response->body();
                    Storage::disk('public')->put($filename, $fileContent);

                    MediaFile::create([
                        'user_id' => auth()->id() ?? 1,
                        'folder_id' => $mediaFolder->id,
                        'name' => basename(parse_url($urlInput, PHP_URL_PATH) ?? '') ?: basename($urlInput),
                        'alt' => 'Imported Media',
                        'url' => $filename,
                        'mime_type' => $response->header('Content-Type') ?? 'application/octet-stream',
                        'size' => strlen($fileContent),
                        'type' => $mediaType === 'audio' ? 'file' : 'image',
                        'visibility' => 'public',
                    ]);

                    return $filename;
                } else {
                    return $urlInput; // Fallback to external URL
                }
            } catch (\Exception $reqError) {
                return $urlInput; // Fallback to external URL
            }
        }

        // Nếu là relative path hoặc không hợp lệ, giữ nguyên
        return $urlInput;
    }
}
