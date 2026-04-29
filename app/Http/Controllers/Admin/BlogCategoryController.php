<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BlogCategoryController extends Controller
{
    public function index()
    {
        Gate::authorize('blog_category.view');
        $categories = BlogCategory::withCount('blogs')->latest()->paginate(15);
        return view('admin.blog-categories.index', compact('categories'));
    }

    public function create()
    {
        Gate::authorize('blog_category.create');
        return view('admin.blog-categories.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('blog_category.create');
        $request->validate([
            'title' => 'required|string|max:255',
            'slug'  => 'nullable|string|max:255|unique:blog_categories,slug',
        ]);

        BlogCategory::create([
            'title' => $request->title,
            'slug'  => $request->slug, // Nếu trống, Observer sẽ tự tạo
        ]);

        return redirect()->route('admin.blog-categories.index')
            ->with('success', 'Thêm danh mục thành công');
    }

    public function edit(BlogCategory $blogCategory)
    {
        Gate::authorize('blog_category.update');
        return view('admin.blog-categories.edit', compact('blogCategory'));
    }

    public function update(Request $request, BlogCategory $blogCategory)
    {
        Gate::authorize('blog_category.update');
        $request->validate([
            'title' => 'required|string|max:255',
            'slug'  => 'nullable|string|max:255|unique:blog_categories,slug,' . $blogCategory->id,
        ]);

        $blogCategory->update([
            'title' => $request->title,
            'slug'  => $request->slug ?: null, // Nếu trống, Observer sẽ tự tạo
        ]);

        return redirect()->route('admin.blog-categories.index')
            ->with('success', 'Cập nhật danh mục thành công');
    }

    public function destroy(BlogCategory $blogCategory)
    {
        Gate::authorize('blog_category.delete');
        $blogCategory->delete();
        return redirect()->route('admin.blog-categories.index')
            ->with('success', 'Xóa danh mục thành công');
    }
}
