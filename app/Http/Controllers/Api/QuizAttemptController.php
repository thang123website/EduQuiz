<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\UserResponse;
use App\Models\Option;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Http\Resources\QuizPartExecutionResource;
use App\Http\Resources\QuizPartResultResource;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

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
                'part_ids' => !empty($partIds) ? $partIds : null,
                'status' => 'in_progress',
                'score' => 0,
                'correct_count' => 0,
                'total_count' => 0, // Will be calculated after loading parts
                'time_spent' => 0,
            ]);
        }

        // Ensure part_ids are updated if an old attempt is reused
        if ($attempt && empty($attempt->part_ids) && !empty($partIds)) {
            $attempt->update(['part_ids' => $partIds]);
        }

        // Load Parts and Questions
        // Here we build the eager loading constraint to only load parent questions initially,
        // and nested children inside.
        $partsQuery = $quiz->parts();
        
        if (!empty($partIds)) {
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

        // Calculate actual total count based on loaded parts
        $totalQuestions = 0;
        foreach ($parts as $part) {
            foreach ($part->questions as $question) {
                if ($question->children->isNotEmpty()) {
                    $totalQuestions += $question->children->count();
                } else {
                    $totalQuestions += 1;
                }
            }
        }

        // Update total_count on attempt
        if ($attempt->total_count !== $totalQuestions) {
            $attempt->update(['total_count' => $totalQuestions]);
        }

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
                $qId = $response['question_id'];
                $optId = $response['option_id'];
                
                if (is_array($optId)) {
                    $savedResponses[$qId] = $optId;
                } else {
                    $savedResponses[$qId] = [$optId];
                }
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
                $qId = $resp['question_id'];
                $optId = $resp['option_id'];
                $savedResponses[$qId] = is_array($optId) ? $optId : [$optId];
            }
        }

        if (empty($savedResponses)) {
            $attempt->update(['status' => 'failed', 'score' => 0, 'time_spent' => $request->input('time_spent', 0)]);
            return response()->json(['status' => 'success', 'message' => 'Submitted empty quiz.', 'attempt_id' => $attemptId]);
        }

        // Fetch all valid questions to prevent foreign key constraint violations from dummy/invalid data
        $questionIds = array_keys($savedResponses);
        $questions = Question::with('options')->whereIn('id', $questionIds)->get()->keyBy('id');
        $validQuestionIds = $questions->keys()->toArray();

        // Fetch all selected options from DB to check correctness
        $optionIds = [];
        foreach ($savedResponses as $opts) {
            $optsArr = is_array($opts) ? $opts : [$opts];
            foreach ($optsArr as $o) {
                $optionIds[] = $o;
            }
        }
        $options = Option::whereIn('id', array_unique($optionIds))->get()->keyBy('id');

        $inserts = [];
        $correctCount = 0;
        $totalScore = 0;
        $now = now();

        foreach ($savedResponses as $questionId => $optIds) {
            // Skip invalid questions (e.g. mock data from frontend)
            if (!in_array($questionId, $validQuestionIds)) {
                continue;
            }

            $question = $questions->get($questionId);
            $optIdsArr = is_array($optIds) ? $optIds : [$optIds];

            // Evaluate correctness based on question type
            $isCorrect = false;
            if ($question->type === 'multiple_answer') {
                $correctOptionIds = $question->options->where('is_correct', true)->pluck('id')->toArray();
                sort($correctOptionIds);
                $selectedOptIds = $optIdsArr;
                sort($selectedOptIds);
                
                $isCorrect = ($correctOptionIds == $selectedOptIds);
            } else {
                if (count($optIdsArr) > 0 && $options->has($optIdsArr[0])) {
                    $isCorrect = $options->get($optIdsArr[0])->is_correct;
                }
            }

            if ($isCorrect) {
                $correctCount++;
                // Use question default_mark
                $totalScore += (float) $question->default_mark; 
            }

            foreach ($optIdsArr as $optId) {
                $optIsCorrect = false;
                if ($options->has($optId)) {
                    $optIsCorrect = $options->get($optId)->is_correct;
                }

                $inserts[] = [
                    'id' => Str::uuid()->toString(),
                    'attempt_id' => $attemptId,
                    'question_id' => $questionId,
                    'selected_option_id' => $optId,
                    'is_correct' => $optIsCorrect,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
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

            // Calculate answered, incorrect, skipped
            $answeredCount = UserResponse::where('attempt_id', $attemptId)->distinct('question_id')->count('question_id');
            $skippedCount = max(0, $attempt->total_count - $answeredCount);
            $incorrectCount = max(0, $answeredCount - $correctCount);

            return response()->json([
                'status' => 'success',
                'message' => 'Quiz submitted successfully',
                'data' => [
                    'attempt_id' => $attemptId,
                    'correct_count' => $correctCount,
                    'incorrect_count' => $incorrectCount,
                    'skipped_count' => $skippedCount,
                    'score' => $totalScore,
                ]
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Quiz Submit Error: " . $e->getMessage());
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

        // Build parts query based on what the user attempted
        $partsQuery = $attempt->quiz->parts();
        if (!empty($attempt->part_ids)) {
            $partsQuery->whereIn('id', $attempt->part_ids);
        }

        // Load Parts, Questions, and the User's Responses
        $parts = $partsQuery->with([
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

        // Calculate answered, incorrect, skipped
        $answeredCount = UserResponse::where('attempt_id', $attemptId)->distinct('question_id')->count('question_id');
        $skippedCount = max(0, $attempt->total_count - $answeredCount);
        $incorrectCount = max(0, $answeredCount - $attempt->correct_count);

        return response()->json([
            'status' => 'success',
            'data' => [
                'attempt' => [
                    'id' => $attempt->id,
                    'status' => $attempt->status,
                    'score' => (float) $attempt->score,
                    'correct_count' => $attempt->correct_count,
                    'incorrect_count' => $incorrectCount,
                    'skipped_count' => $skippedCount,
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
