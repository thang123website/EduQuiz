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
        
        $categories = app(\App\Repositories\CategoryRepository::class)->getFlatTree(true);

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

        $categories = app(\App\Repositories\CategoryRepository::class)->getFlatTree(true);
        $tags = \App\Models\Tag::orderBy('name')->get();
        return view('admin.quizzes.create', compact('categories', 'tags'));
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
            'type' => 'required|in:full_test,practice,minitest',
            'duration' => 'required|integer|min:1',
            'pass_mark' => 'required|integer|min:0|max:100',
            'difficulty' => 'required|in:easy,medium,hard',
            'status' => 'required|in:draft,published,archived',
            'settings' => 'nullable|array',
        ]);

        $validated['is_popular'] = $request->has('is_popular');
        $validated['is_new'] = $request->has('is_new');

        $quiz = Quiz::create($validated);
        
        if ($request->has('tags')) {
            $quiz->tags()->sync($request->input('tags'));
        }

        // Tự động tạo 1 Part mặc định cho các bài thi cơ bản không cần chia phần
        $quiz->parts()->create([
            'title' => 'Nội dung bài thi',
            'description' => 'Phần thi mặc định',
            'order_idx' => 1
        ]);

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

        $quiz = Quiz::with(['parts.questions.options', 'parts.questions.children.options', 'tags'])->findOrFail($id);
        $categories = app(\App\Repositories\CategoryRepository::class)->getFlatTree(true);
        $tags = \App\Models\Tag::orderBy('name')->get();
        
        return view('admin.quizzes.edit', compact('quiz', 'categories', 'tags'));
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
            'type' => 'required|in:full_test,practice,minitest',
            'duration' => 'required|integer|min:1',
            'pass_mark' => 'required|integer|min:0|max:100',
            'difficulty' => 'required|in:easy,medium,hard',
            'status' => 'required|in:draft,published,archived',
            'settings' => 'nullable|array',
        ]);

        $validated['is_popular'] = $request->has('is_popular');
        $validated['is_new'] = $request->has('is_new');

        $quiz->update($validated);

        if ($request->has('tags')) {
            $quiz->tags()->sync($request->input('tags'));
        } else {
            $quiz->tags()->detach();
        }

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
