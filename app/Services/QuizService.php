<?php

namespace App\Services;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\UserResponse;
use Illuminate\Support\Facades\DB;

class QuizService
{
    /**
     * Start a new quiz attempt and get questions (supports Full/Mini test modes).
     */
    public function generateQuizPayload(string $quizId, string $mode = 'full'): array
    {
        $quiz = Quiz::with(['parts.questions.options', 'questions.options'])->findOrFail($quizId);
        $questions = collect();

        if ($mode === 'mini') {
            if ($quiz->parts->count() > 0) {
                // Take random questions from each part for mini test
                foreach ($quiz->parts as $part) {
                    $take = max(1, intval($part->questions->count() * 0.3)); // 30% of each part
                    $questions = $questions->merge($part->questions->random(min($take, $part->questions->count())));
                }
            } else {
                $take = max(1, intval($quiz->questions->count() * 0.3));
                $questions = $quiz->questions->random(min($take, $quiz->questions->count()));
            }
        } else {
            $questions = $quiz->questions;
        }

        // Hide answers from frontend
        $questions->each(function ($q) {
            $q->options->makeHidden('is_correct');
        });

        return [
            'quiz' => $quiz,
            'questions' => $questions->values(),
            'mode' => $mode
        ];
    }

    /**
     * Submit and grade a quiz attempt.
     */
    public function submitAttempt(string $userId, string $quizId, array $answers, int $timeSpent): QuizAttempt
    {
        return DB::transaction(function () use ($userId, $quizId, $answers, $timeSpent) {
            $quiz = Quiz::with('questions.options')->findOrFail($quizId);
            
            $correctCount = 0;
            $totalPoints = 0;
            $userScore = 0;

            $attempt = QuizAttempt::create([
                'user_id' => $userId,
                'quiz_id' => $quizId,
                'time_spent' => $timeSpent,
                'status' => 'completed',
            ]);

            foreach ($quiz->questions as $question) {
                $totalPoints += $question->grade;
                $userAnswer = $answers[$question->id] ?? null;
                $isCorrect = false;

                if ($userAnswer) {
                    // Logic for single_choice / multiple_answer
                    $correctOption = $question->options->where('is_correct', true)->first();
                    
                    if ($correctOption && $correctOption->id === $userAnswer) {
                        $isCorrect = true;
                        $correctCount++;
                        $userScore += $question->grade;
                    }
                }

                UserResponse::create([
                    'attempt_id' => $attempt->id,
                    'question_id' => $question->id,
                    'selected_option_id' => $userAnswer,
                    'is_correct' => $isCorrect,
                ]);
            }

            $scaledScore = $userScore;
            
            // Scaled Score logic (e.g. TOEIC)
            $settings = $quiz->settings ?? [];
            if (isset($settings['scoring_type']) && $settings['scoring_type'] === 'toeic') {
                // Basic mock TOEIC conversion (Real TOEIC requires separate listening/reading mapping)
                $percentage = $quiz->questions->count() > 0 ? ($correctCount / $quiz->questions->count()) : 0;
                $scaledScore = round($percentage * 990 / 5) * 5; // Round to nearest 5
            }

            $attempt->update([
                'score' => $scaledScore,
                'correct_count' => $correctCount,
                'total_count' => $quiz->questions->count(),
                'status' => $scaledScore >= $quiz->pass_mark ? 'passed' : 'failed',
            ]);

            return $attempt;
        });
    }
}
