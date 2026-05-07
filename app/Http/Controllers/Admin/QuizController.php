<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizCategory;
use App\Repositories\QuizRepository;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    protected QuizRepository $repository;

    public function __construct(QuizRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of quizzes with filters and stats.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['category_id', 'status', 'search', 'sort_by', 'sort_order', 'per_page']);
        $result = $this->repository->getPaginatedForAdmin($filters);
        
        $categories = QuizCategory::where('is_active', true)->orderBy('name')->get();

        return view('admin.quizzes.index', [
            'quizzes' => $result['data'],
            'stats' => $result['stats'],
            'categories' => $categories
        ]);
    }

    /**
     * Show the form for creating a new quiz.
     */
    public function create()
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('exams.create')) {
            abort(403, 'Bạn không có quyền tạo đề thi.');
        }

        $categories = QuizCategory::where('is_active', true)->orderBy('name')->get();
        return view('admin.quizzes.create', compact('categories'));
    }

    /**
     * Store a newly created quiz.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('exams.create')) {
            abort(403, 'Bạn không có quyền tạo đề thi.');
        }

        $validated = $request->validate([
            'category_id' => 'required|exists:quiz_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|string|max:500',
            'duration' => 'required|integer|min:1',
            'pass_mark' => 'required|integer|min:0|max:100',
            'difficulty' => 'required|in:easy,medium,hard',
            'status' => 'required|in:draft,published,archived',
            'settings' => 'nullable|array',
        ]);

        $quiz = Quiz::create($validated);

        return redirect()->route('admin.quizzes.edit', $quiz->id)
            ->with('success', 'Tạo đề thi thành công. Hãy bắt đầu thêm câu hỏi.');
    }

    /**
     * Show the form for editing the specified quiz.
     */
    public function edit(string $id)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('exams.update')) {
            abort(403, 'Bạn không có quyền chỉnh sửa đề thi.');
        }

        $quiz = Quiz::with(['questions.options', 'questions.children.options'])->findOrFail($id);
        $categories = QuizCategory::where('is_active', true)->orderBy('name')->get();
        
        return view('admin.quizzes.edit', compact('quiz', 'categories'));
    }

    /**
     * Update the specified quiz.
     */
    public function update(Request $request, string $id)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('exams.update')) {
            abort(403, 'Bạn không có quyền chỉnh sửa đề thi.');
        }

        $quiz = Quiz::findOrFail($id);

        $validated = $request->validate([
            'category_id' => 'required|exists:quiz_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|string|max:500',
            'duration' => 'required|integer|min:1',
            'pass_mark' => 'required|integer|min:0|max:100',
            'difficulty' => 'required|in:easy,medium,hard',
            'status' => 'required|in:draft,published,archived',
            'settings' => 'nullable|array',
        ]);

        $quiz->update($validated);

        return redirect()->back()
            ->with('success', 'Cập nhật đề thi thành công');
    }

    /**
     * Remove the specified quiz.
     */
    public function destroy(string $id)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('exams.delete')) {
            abort(403, 'Bạn không có quyền xóa đề thi.');
        }

        $quiz = Quiz::findOrFail($id);
        $quiz->delete();

        return redirect()->route('admin.quizzes.index')
            ->with('success', 'Xóa đề thi thành công');
    }
}
