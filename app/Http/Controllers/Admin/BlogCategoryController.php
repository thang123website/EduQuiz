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
        $categories = BlogCategory::getTree();
        return view('admin.blog-categories.index', compact('categories'));
    }

    public function create()
    {
        Gate::authorize('blog_category.create');
        $parentCategories = BlogCategory::getTree();
        return view('admin.blog-categories.create', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        Gate::authorize('blog_category.create');
        $rules = array_merge(
            translatable_rules('title', 'required|string|max:255'),
            [
                'slug'      => 'nullable|string|max:255|unique:blog_categories,slug',
                'parent_id' => 'nullable|exists:blog_categories,id',
            ]
        );
        $request->validate($rules);

        BlogCategory::create([
            'title'     => $request->title,
            'slug'      => $request->slug,
            'parent_id' => $request->parent_id,
        ]);

        return redirect()->route('admin.blog-categories.index')
            ->with('success', 'Thêm danh mục thành công');
    }

    public function edit(BlogCategory $blogCategory)
    {
        Gate::authorize('blog_category.update');
        // Lấy danh sách cha tiềm năng (loại bỏ chính nó và con của nó để tránh vòng lặp)
        $parentCategories = BlogCategory::getTree();
        // Lọc bỏ chính nó (để đơn giản trong logic select view)
        return view('admin.blog-categories.edit', compact('blogCategory', 'parentCategories'));
    }

    public function update(Request $request, BlogCategory $blogCategory)
    {
        Gate::authorize('blog_category.update');
        $rules = array_merge(
            translatable_rules('title', 'required|string|max:255'),
            [
                'slug'      => 'nullable|string|max:255|unique:blog_categories,slug,' . $blogCategory->id,
                'parent_id' => 'nullable|exists:blog_categories,id|different:id',
            ]
        );
        $request->validate($rules);

        $blogCategory->update([
            'title'     => $request->title,
            'slug'      => $request->slug ?: null,
            'parent_id' => $request->parent_id,
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
