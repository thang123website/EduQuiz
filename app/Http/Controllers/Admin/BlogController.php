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
        $categories = BlogCategory::orderBy('title')->get();
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
            'image'          => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'enable_comment' => 'nullable|boolean',
            'status'         => 'required|in:pending,publish',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('blog', 'public');
        }

        Blog::create([
            'title'          => $request->title,
            'slug'           => $request->slug,
            'category_id'    => $request->category_id,
            'author_id'      => auth()->id(),
            'description'    => $request->description,
            'content'        => $request->content,
            'image'          => $imagePath,
            'enable_comment' => $request->boolean('enable_comment', true),
            'status'         => $request->status,
        ]);

        return redirect()->route('admin.blog.index')
            ->with('success', 'Thêm bài viết thành công');
    }

    public function edit(Blog $blog)
    {
        Gate::authorize('blog.update');
        $categories = BlogCategory::orderBy('title')->get();
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
            'image'          => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'enable_comment' => 'nullable|boolean',
            'status'         => 'required|in:pending,publish',
        ]);

        $data = [
            'title'          => $request->title,
            'slug'           => $request->slug,
            'category_id'    => $request->category_id,
            'description'    => $request->description,
            'content'        => $request->content,
            'enable_comment' => $request->boolean('enable_comment', true),
            'status'         => $request->status,
        ];

        if ($request->hasFile('image')) {
            // Xóa ảnh cũ trước khi upload ảnh mới
            if ($blog->image) {
                Storage::disk('public')->delete($blog->image);
            }
            $data['image'] = $request->file('image')->store('blog', 'public');
        }

        $blog->update($data);

        return redirect()->route('admin.blog.index')
            ->with('success', 'Cập nhật bài viết thành công');
    }

    public function destroy(Blog $blog)
    {
        Gate::authorize('blog.delete');

        // Xóa file ảnh trước khi xóa bản ghi
        if ($blog->image) {
            Storage::disk('public')->delete($blog->image);
        }

        $blog->delete();

        return redirect()->route('admin.blog.index')
            ->with('success', 'Xóa bài viết thành công');
    }
}
