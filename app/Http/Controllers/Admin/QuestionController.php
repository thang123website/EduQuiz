<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    /**
     * Display the specified question.
     */
    public function show(string $id): JsonResponse
    {
        $question = Question::with('options')->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $question
        ]);
    }

    /**
     * Store a new question and its options.
     */
    public function store(Request $request): JsonResponse
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('exams.create')) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền tạo câu hỏi.'], 403);
        }

        $validated = $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'parent_id' => 'nullable|exists:questions,id',
            'type' => 'required|string|max:50',
            'content' => 'required|string',
            'media_url' => 'nullable|string',
            'media_type' => 'required|in:image,audio,none',
            'grade' => 'required|numeric|min:0',
            'explanation' => 'nullable|string',
            'shuffle_options' => 'boolean',
            'order_idx' => 'integer',
            'options' => 'required_if:type,single_choice,multiple_answer|array',
            'options.*.text' => 'required|string',
            'options.*.is_correct' => 'required|boolean',
        ]);

        return DB::transaction(function () use ($validated) {
            $question = Question::create($validated);

            if (!empty($validated['options'])) {
                foreach ($validated['options'] as $optionData) {
                    $question->options()->create($optionData);
                }
            }

            // Update Quiz denormalized counts
            $quiz = Quiz::find($validated['quiz_id']);
            $quiz->increment('question_count');
            $quiz->increment('total_points', $validated['grade']);

            return response()->json([
                'success' => true,
                'message' => 'Question created successfully',
                'data' => $question->load('options')
            ], 201);
        });
    }

    /**
     * Update an existing question and its options.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('exams.update')) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền chỉnh sửa câu hỏi.'], 403);
        }

        $question = Question::findOrFail($id);
        
        $validated = $request->validate([
            'type' => 'sometimes|required|string|max:50',
            'content' => 'sometimes|required|string',
            'media_url' => 'nullable|string',
            'media_type' => 'sometimes|required|in:image,audio,none',
            'grade' => 'sometimes|required|numeric|min:0',
            'explanation' => 'nullable|string',
            'shuffle_options' => 'boolean',
            'order_idx' => 'integer',
            'options' => 'sometimes|array',
            'options.*.id' => 'nullable|exists:options,id',
            'options.*.text' => 'required|string',
            'options.*.is_correct' => 'required|boolean',
        ]);

        return DB::transaction(function () use ($question, $validated) {
            $oldGrade = $question->grade;
            $question->update($validated);

            if (isset($validated['grade']) && $oldGrade != $validated['grade']) {
                $question->quiz->increment('total_points', $validated['grade'] - $oldGrade);
            }

            if (isset($validated['options'])) {
                // For simplicity in this implementation, we'll sync options by deleting old ones 
                // or updating if ID is provided.
                $existingOptionIds = collect($validated['options'])->pluck('id')->filter()->toArray();
                $question->options()->whereNotIn('id', $existingOptionIds)->delete();

                foreach ($validated['options'] as $optionData) {
                    if (isset($optionData['id'])) {
                        $question->options()->where('id', $optionData['id'])->update($optionData);
                    } else {
                        $question->options()->create($optionData);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Question updated successfully',
                'data' => $question->load('options')
            ]);
        });
    }

    /**
     * Delete a question.
     */
    public function destroy(string $id)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('exams.delete')) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Bạn không có quyền xóa câu hỏi.'], 403);
            }
            abort(403, 'Bạn không có quyền xóa câu hỏi.');
        }

        $question = Question::findOrFail($id);
        
        DB::transaction(function () use ($question) {
            $quiz = $question->quiz;
            $quiz->decrement('question_count');
            $quiz->decrement('total_points', $question->grade);
            
            $question->delete();
        });

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Question deleted successfully'
            ]);
        }

        return redirect()->back()->with('success', 'Xoá câu hỏi thành công.');
    }
}
