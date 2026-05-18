<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    /**
     * Get Leaderboard
     * Supports filtering by type: all-time, weekly, monthly
     * Supports filtering by quiz_id
     */
    public function index(Request $request)
    {
        $type = $request->input('type', 'all-time');
        $quizId = $request->input('quiz_id');
        $limit = $request->input('limit', 50);

        $query = QuizAttempt::with('user:id,name,avatar')
            ->where('status', 'completed')
            ->select('user_id', DB::raw('SUM(score) as total_score'), DB::raw('SUM(correct_count) as total_correct'), DB::raw('SUM(time_spent) as total_time_spent'))
            ->groupBy('user_id')
            ->orderBy('total_score', 'desc')
            ->orderBy('total_time_spent', 'asc'); // Tie-breaker: less time spent is better

        if ($type === 'weekly') {
            $query->where('created_at', '>=', now()->startOfWeek());
        } elseif ($type === 'monthly') {
            $query->where('created_at', '>=', now()->startOfMonth());
        }

        if ($quizId) {
            $query->where('quiz_id', $quizId);
        }

        $leaderboard = $query->limit($limit)->get();

        $formatted = $leaderboard->map(function ($item, $index) {
            return [
                'rank' => $index + 1,
                'user' => [
                    'id' => $item->user->id ?? null,
                    'name' => $item->user ? trim($item->user->name) : 'Unknown User',
                    'avatar' => $item->user && $item->user->avatar ? get_image_url($item->user->avatar) : null,
                ],
                'total_score' => (float) $item->total_score,
                'total_correct' => (int) $item->total_correct,
                'total_time_spent' => (int) $item->total_time_spent,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $formatted
        ]);
    }
}
