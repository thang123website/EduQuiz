<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuizAttempt;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizAttemptController extends Controller
{
    /**
     * Display a listing of quiz attempts.
     */
    public function index(Request $request)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('quiz_attempt.view')) {
            abort(403, 'Bạn không có quyền xem lịch sử thi.');
        }

        $query = QuizAttempt::with(['user', 'quiz.category'])
            ->orderBy('created_at', 'desc');

        // Lọc theo Quiz
        if ($request->filled('quiz_id')) {
            $query->where('quiz_id', $request->quiz_id);
        }

        // Lọc theo User
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Tìm kiếm theo tên user hoặc tiêu đề quiz
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('quiz', function($qq) use ($search) {
                    $qq->where('title', 'like', "%{$search}%");
                });
            });
        }

        $attempts = $query->paginate(15)->withQueryString();
        $quizzes = Quiz::select('id', 'title')->orderBy('title')->get();

        return view('admin.quizzes.attempts.index', compact('attempts', 'quizzes'));
    }

    /**
     * Display the specified quiz attempt.
     */
    public function show(string $id)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('quiz_attempt.view')) {
            abort(403, 'Bạn không có quyền xem lịch sử thi.');
        }

        $attempt = QuizAttempt::with([
            'user', 
            'quiz', 
            'responses.question.options',
            'responses.selectedOption'
        ])->findOrFail($id);

        return view('admin.quizzes.attempts.show', compact('attempt'));
    }

    /**
     * Remove the specified attempt from history.
     */
    public function destroy(string $id)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('quiz_attempt.delete')) {
            abort(403, 'Bạn không có quyền xóa lịch sử thi.');
        }

        $attempt = QuizAttempt::findOrFail($id);
        $attempt->delete();

        return redirect()->back()->with('success', 'Đã xóa lịch sử lượt thi này.');
    }

    /**
     * Bulk delete attempts.
     */
    public function bulkDestroy(Request $request)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('quiz_attempt.delete')) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền xóa lịch sử thi.'], 403);
        }

        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Vui lòng chọn ít nhất một lượt thi để xóa.'], 400);
        }

        QuizAttempt::whereIn('id', $ids)->delete();

        return response()->json([
            'success' => true, 
            'message' => 'Đã xóa thành công ' . count($ids) . ' lượt thi.'
        ]);
    }
}
