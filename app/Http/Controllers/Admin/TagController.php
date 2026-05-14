<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('quiz_category.view')) {
            // Reusing quiz_category permission or you can add tag specific ones
            abort(403, 'Bạn không có quyền xem quản lý thẻ.');
        }

        $query = Tag::query();

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $tags = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.tags.index', compact('tags'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('quiz_category.create')) {
            abort(403, 'Bạn không có quyền tạo thẻ.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $validated['slug'] = Str::slug($validated['name']) . '-' . rand(1000, 9999);

        Tag::create($validated);

        return redirect()->route('admin.tags.index')->with('success', 'Tạo thẻ thành công');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('quiz_category.update')) {
            abort(403, 'Bạn không có quyền chỉnh sửa thẻ.');
        }

        $tag = Tag::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        if ($tag->name !== $validated['name']) {
            $validated['slug'] = Str::slug($validated['name']) . '-' . rand(1000, 9999);
        }

        $tag->update($validated);

        return redirect()->route('admin.tags.index')->with('success', 'Cập nhật thẻ thành công');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('quiz_category.delete')) {
            abort(403, 'Bạn không có quyền xóa thẻ.');
        }

        $tag = Tag::findOrFail($id);
        $tag->delete();

        return redirect()->route('admin.tags.index')->with('success', 'Xóa thẻ thành công');
    }
}
