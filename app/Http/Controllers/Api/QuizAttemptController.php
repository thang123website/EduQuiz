<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\UserResponse;
use App\Models\Option;
use Illuminate\Http\Request;
use App\Http\Resources\QuizPartExecutionResource;
use App\Http\Resources\QuizPartResultResource;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class QuizAttemptController extends Controller
{
    /**
     * Start a new attempt or resume an existing one
     */
    public function start($quizId, Request $request)
    {
        $quiz = Quiz::where('status', 'published')
            ->findOrFail($quizId);

        $userId = auth('sanctum')->id();
        $forceNew = $request->boolean('force_new', false);
        $partIds = $request->input('part_ids', []); // For practice mode

        // Check for existing in_progress attempt
        $attempt = QuizAttempt::where('user_id', $userId)
            ->where('quiz_id', $quizId)
            ->where('status', 'in_progress')
            ->latest('created_at')
            ->first();

        if ($attempt && !$forceNew) {
            // Resume existing
            // In a real app we might also fetch autosaved answers from Cache here to return to frontend
        } else {
            if ($attempt && $forceNew) {
                // Abandon the old one
                $attempt->update(['status' => 'abandoned']);
            }

            // Create new attempt
            $attempt = QuizAttempt::create([
                'user_id' => $userId,
                'quiz_id' => $quizId,
                'status' => 'in_progress',
                'score' => 0,
                'correct_count' => 0,
                'total_count' => $quiz->question_count, // Will refine if part_ids are provided
                'time_spent' => 0,
            ]);
        }

        // Load Parts and Questions
        // Here we build the eager loading constraint to only load parent questions initially,
        // and nested children inside.
        $partsQuery = $quiz->parts();
        
        if (!empty($partIds) && $quiz->type === 'practice') {
            // Filter by requested parts
            $partsQuery->whereIn('id', $partIds);
        }

        $parts = $partsQuery->with([
            'questions' => function ($query) {
                // Load parent questions OR independent questions
                $query->whereNull('parent_id')
                      ->with(['options', 'children.options']); // Eager load children and options
            }
        ])->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'attempt_id' => $attempt->id,
                'quiz' => [
                    'id' => $quiz->id,
                    'title' => $quiz->title,
                    'duration' => $quiz->duration,
                    'type' => $quiz->type,
                ],
                'parts' => QuizPartExecutionResource::collection($parts),
            ]
        ]);
    }

    /**
     * Autosave answers to Cache
     */
    public function autosave($attemptId, Request $request)
    {
        $userId = auth('sanctum')->id();
        
        $attempt = QuizAttempt::where('id', $attemptId)
            ->where('user_id', $userId)
            ->where('status', 'in_progress')
            ->firstOrFail();

        $responses = $request->input('responses', []); // [ ['question_id' => '...', 'option_id' => '...'], ... ]
        
        if (empty($responses)) {
            return response()->json(['status' => 'success']);
        }

        $cacheKey = "quiz_autosave:attempt:{$attemptId}";
        $savedResponses = Cache::get($cacheKey, []);
        
        // Save to Cache
        foreach ($responses as $response) {
            if (isset($response['question_id']) && isset($response['option_id'])) {
                $savedResponses[$response['question_id']] = $response['option_id'];
            }
        }

        // Set expiry for 24 hours just in case user abandons without submitting
        Cache::put($cacheKey, $savedResponses, 86400);

        return response()->json(['status' => 'success']);
    }

    /**
     * Submit and calculate score
     */
    public function submit($attemptId, Request $request)
    {
        $userId = auth('sanctum')->id();
        
        $attempt = QuizAttempt::where('id', $attemptId)
            ->where('user_id', $userId)
            ->where('status', 'in_progress')
            ->firstOrFail();

        $cacheKey = "quiz_autosave:attempt:{$attemptId}";
        $savedResponses = Cache::get($cacheKey, []);

        // Also merge any final responses sent in the submit payload
        $finalResponses = $request->input('responses', []);
        foreach ($finalResponses as $resp) {
            if (isset($resp['question_id']) && isset($resp['option_id'])) {
                $savedResponses[$resp['question_id']] = $resp['option_id'];
            }
        }

        if (empty($savedResponses)) {
            $attempt->update(['status' => 'failed', 'score' => 0, 'time_spent' => $request->input('time_spent', 0)]);
            return response()->json(['status' => 'success', 'message' => 'Submitted empty quiz.', 'attempt_id' => $attemptId]);
        }

        // Fetch all valid questions to prevent foreign key constraint violations from dummy/invalid data
        $questionIds = array_keys($savedResponses);
        $validQuestionIds = \App\Models\Question::whereIn('id', $questionIds)->pluck('id')->toArray();

        // Fetch all selected options from DB to check correctness
        $optionIds = array_values($savedResponses);
        $options = Option::whereIn('id', $optionIds)->get()->keyBy('id');

        $inserts = [];
        $correctCount = 0;
        $totalScore = 0;
        $now = now();

        foreach ($savedResponses as $questionId => $optionId) {
            // Skip invalid questions (e.g. mock data from frontend)
            if (!in_array($questionId, $validQuestionIds)) {
                continue;
            }

            $isCorrect = false;
            if ($options->has($optionId)) {
                $isCorrect = $options->get($optionId)->is_correct;
            }

            if ($isCorrect) {
                $correctCount++;
                // In a real system, we might query question_quiz_part to get the specific mark for this question.
                // For simplicity, we assume default 1.00 or fetch question mark.
                $totalScore += 1.00; 
            }

            $inserts[] = [
                'id' => \Illuminate\Support\Str::uuid()->toString(),
                'attempt_id' => $attemptId,
                'question_id' => $questionId,
                'selected_option_id' => $optionId,
                'is_correct' => $isCorrect,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Use Transaction for bulk insert
        DB::beginTransaction();
        try {
            // Bulk insert responses
            $chunks = array_chunk($inserts, 500);
            foreach ($chunks as $chunk) {
                UserResponse::insert($chunk);
            }

            // Update attempt
            $attempt->update([
                'status' => 'completed',
                'correct_count' => $correctCount,
                'score' => $totalScore,
                'time_spent' => $request->input('time_spent', 0),
            ]);

            // Clear Cache
            Cache::forget($cacheKey);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Quiz submitted successfully',
                'data' => [
                    'attempt_id' => $attemptId,
                    'correct_count' => $correctCount,
                    'score' => $totalScore,
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Quiz Submit Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to submit quiz'], 500);
        }
    }

    /**
     * Get details of a completed attempt
     */
    public function result($attemptId)
    {
        $userId = auth('sanctum')->id();
        
        $attempt = QuizAttempt::with('quiz')->where('id', $attemptId)
            ->where('user_id', $userId)
            ->firstOrFail();

        // Load Parts, Questions, and the User's Responses
        $parts = $attempt->quiz->parts()->with([
            'questions' => function ($query) use ($attemptId) {
                $query->whereNull('parent_id')
                      ->with([
                          'options', 
                          'userResponses' => function($q) use ($attemptId) {
                              $q->where('attempt_id', $attemptId);
                          },
                          'children.options',
                          'children.userResponses' => function($q) use ($attemptId) {
                              $q->where('attempt_id', $attemptId);
                          }
                      ]);
            }
        ])->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'attempt' => [
                    'id' => $attempt->id,
                    'status' => $attempt->status,
                    'score' => (float) $attempt->score,
                    'correct_count' => $attempt->correct_count,
                    'total_count' => $attempt->total_count,
                    'time_spent' => $attempt->time_spent,
                ],
                'quiz' => [
                    'id' => $attempt->quiz->id,
                    'title' => $attempt->quiz->title,
                ],
                'parts' => QuizPartResultResource::collection($parts),
            ]
        ]);
    }
}
