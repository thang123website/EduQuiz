<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuizCategory;
use App\Repositories\CategoryRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QuizCategoryController extends Controller
{
    protected CategoryRepository $repository;

    public function __construct(CategoryRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the categories in tree format.
     */
    public function index()
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('quiz_category.view')) {
            abort(403, 'Bạn không có quyền xem danh mục bài thi.');
        }

        $categories = $this->repository->getAdminTree();
        $allCategories = $this->repository->getFlatTree();
        
        return view('admin.quiz-categories.index', compact('categories', 'allCategories'));
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('quiz_category.create')) {
            abort(403, 'Bạn không có quyền tạo danh mục bài thi.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:quiz_categories,id',
            'type' => 'required|string|max:50',
            'icon' => 'nullable|string|max:100',
            'order_idx' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        if (!$request->has('is_active')) {
            $validated['is_active'] = false;
        }

        $validated['slug'] = Str::slug($validated['name']) . '-' . rand(1000, 9999);

        QuizCategory::create($validated);

        return redirect()->route('admin.quiz-categories.index')
            ->with('success', 'Tạo danh mục mới thành công');
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, string $id)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('quiz_category.update')) {
            abort(403, 'Bạn không có quyền chỉnh sửa danh mục bài thi.');
        }

        $category = QuizCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:quiz_categories,id|different:id',
            'type' => 'required|string|max:50',
            'icon' => 'nullable|string|max:100',
            'order_idx' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        if (!$request->has('is_active')) {
            $validated['is_active'] = false;
        }

        if ($category->name !== $validated['name']) {
            $validated['slug'] = Str::slug($validated['name']) . '-' . rand(1000, 9999);
        }

        $category->update($validated);

        return redirect()->route('admin.quiz-categories.index')
            ->with('success', 'Cập nhật danh mục thành công');
    }

    /**
     * Remove the specified category.
     */
    public function destroy(string $id)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('quiz_category.delete')) {
            abort(403, 'Bạn không có quyền xóa danh mục bài thi.');
        }

        $category = QuizCategory::findOrFail($id);
        
        // Check if has children
        if ($category->children()->count() > 0) {
            return redirect()->back()->with('error', 'Không thể xóa danh mục đang có danh mục con.');
        }

        // Check if has quizzes
        if ($category->quizzes()->count() > 0) {
            return redirect()->back()->with('error', 'Không thể xóa danh mục đang có đề thi.');
        }

        $category->delete();

        return redirect()->route('admin.quiz-categories.index')
            ->with('success', 'Xóa danh mục thành công');
    }
}
