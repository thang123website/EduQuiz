<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QuizAttempt;
use App\Models\UserTarget;
use Illuminate\Http\Request;
use App\Models\Quiz;
use Illuminate\Support\Facades\DB;

class UserDashboardController extends Controller
{
    /**
     * Get attempts for a specific quiz
     */
    public function quizAttempts($quizId, Request $request)
    {
        $limit = $request->input('limit', 10);
        $userId = auth('sanctum')->id();

        $attempts = QuizAttempt::where('user_id', $userId)
            ->where('quiz_id', $quizId)
            ->orderBy('created_at', 'desc')
            ->paginate($limit);

        return response()->json([
            'status' => 'success',
            'data' => $attempts->items(),
            'meta' => [
                'current_page' => $attempts->currentPage(),
                'last_page' => $attempts->lastPage(),
                'per_page' => $attempts->perPage(),
                'total' => $attempts->total(),
            ]
        ]);
    }

    /**
     * Get all attempts for the user
     */
    public function allAttempts(Request $request)
    {
        $limit = $request->input('limit', 10);
        $userId = auth('sanctum')->id();

        $attempts = QuizAttempt::with('quiz')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($limit);

        return response()->json([
            'status' => 'success',
            'data' => $attempts->map(function ($attempt) {
                return [
                    'id' => $attempt->id,
                    'quiz' => [
                        'id' => $attempt->quiz->id,
                        'title' => $attempt->quiz->title,
                        'thumbnail' => $attempt->quiz->thumbnail ? get_image_url($attempt->quiz->thumbnail) : null,
                        'type' => $attempt->quiz->type,
                    ],
                    'score' => (float) $attempt->score,
                    'correct_count' => $attempt->correct_count,
                    'total_count' => $attempt->total_count,
                    'time_spent' => $attempt->time_spent,
                    'status' => $attempt->status,
                    'created_at' => $attempt->created_at->toIso8601String(),
                ];
            }),
            'meta' => [
                'current_page' => $attempts->currentPage(),
                'last_page' => $attempts->lastPage(),
                'per_page' => $attempts->perPage(),
                'total' => $attempts->total(),
            ]
        ]);
    }

    /**
     * Get overall statistics
     */
    public function statistics()
    {
        $userId = auth('sanctum')->id();

        // Use aggregates for performance
        $stats = QuizAttempt::where('user_id', $userId)
            ->where('status', 'completed')
            ->selectRaw('
                COUNT(id) as total_quizzes,
                SUM(time_spent) as total_time_spent,
                AVG(score) as avg_score,
                SUM(correct_count) as total_correct,
                SUM(total_count) as grand_total_questions
            ')
            ->first();

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_quizzes' => (int) $stats->total_quizzes,
                'total_time_spent' => (int) $stats->total_time_spent,
                'avg_score' => round((float) $stats->avg_score, 2),
                'accuracy_rate' => $stats->grand_total_questions > 0 
                    ? round(($stats->total_correct / $stats->grand_total_questions) * 100, 2) 
                    : 0,
            ]
        ]);
    }

    /**
     * Update user targets
     */
    public function updateTargets(Request $request)
    {
        $userId = auth('sanctum')->id();

        $validated = $request->validate([
            'target_type' => 'required|string|max:50',
            'target_score' => 'required|integer|min:0',
            'exam_date' => 'nullable|date',
        ]);

        $target = UserTarget::updateOrCreate(
            ['user_id' => $userId, 'target_type' => $validated['target_type']],
            ['target_score' => $validated['target_score'], 'exam_date' => $validated['exam_date']]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Targets updated successfully',
            'data' => $target
        ]);
    }
}
