<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    public function index()
    {
        Gate::authorize('blog.view');
        $blogs = Blog::with(['category', 'author'])
            ->latest()
            ->paginate(15);
        return view('admin.blog.index', compact('blogs'));
    }

    public function create()
    {
        Gate::authorize('blog.create');
        $categories = BlogCategory::getTree();
        return view('admin.blog.create', compact('categories'));
    }

    public function store(Request $request)
    {
        Gate::authorize('blog.create');
        $request->validate([
            'title'          => 'required|string|max:255',
            'slug'           => 'nullable|string|max:255|unique:blog,slug',
            'category_id'    => 'nullable|exists:blog_categories,id',
            'description'    => 'nullable|string',
            'content'        => 'required|string',
            'image'          => 'nullable|string', // Chuyển thành string để nhận URL từ Media Manager
            'enable_comment' => 'nullable|boolean',
            'status'         => 'required|in:pending,publish',
        ]);

        Blog::create([
            'title'          => $request->title,
            'slug'           => $request->slug,
            'category_id'    => $request->category_id,
            'author_id'      => auth()->id(),
            'description'    => $request->description,
            'content'        => $request->content,
            'image'          => $request->image,
            'enable_comment' => $request->boolean('enable_comment', true),
            'status'         => $request->status,
        ]);

        return redirect()->route('admin.blog.index')
            ->with('success', 'Thêm bài viết thành công');
    }

    public function edit(Blog $blog)
    {
        Gate::authorize('blog.update');
        $categories = BlogCategory::getTree();
        return view('admin.blog.edit', compact('blog', 'categories'));
    }

    public function update(Request $request, Blog $blog)
    {
        Gate::authorize('blog.update');
        $request->validate([
            'title'          => 'required|string|max:255',
            'slug'           => 'nullable|string|max:255|unique:blog,slug,' . $blog->id,
            'category_id'    => 'nullable|exists:blog_categories,id',
            'description'    => 'nullable|string',
            'content'        => 'required|string',
            'image'          => 'nullable|string', // Chuyển thành string
            'enable_comment' => 'nullable|boolean',
            'status'         => 'required|in:pending,publish',
        ]);

        $blog->update([
            'title'          => $request->title,
            'slug'           => $request->slug,
            'category_id'    => $request->category_id,
            'description'    => $request->description,
            'content'        => $request->content,
            'image'          => $request->image,
            'enable_comment' => $request->boolean('enable_comment', true),
            'status'         => $request->status,
        ]);

        return redirect()->route('admin.blog.index')
            ->with('success', 'Cập nhật bài viết thành công');
    }

    public function destroy(Blog $blog)
    {
        Gate::authorize('blog.delete');

        // Không xóa file ảnh ở đây vì ảnh thuộc về Media Manager quản lý
        $blog->delete();

        return redirect()->route('admin.blog.index')
            ->with('success', 'Xóa bài viết thành công');
    }
}
