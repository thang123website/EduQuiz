<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Blog;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $query = Comment::with(['user', 'commentable'])->whereNull('parent_id');

        if ($status === 'pending') {
            $query->pending();
        } elseif ($status === 'active') {
            $query->active();
        }

        if ($request->filled('search')) {
            $query->where('content', 'like', '%' . $request->search . '%');
        }

        $comments = $query->orderBy('created_at', 'desc')->paginate(15);
        $blogs = Blog::where('status', 'publish')->orderBy('title')->get();
        
        return view('admin.comments.index', compact('comments', 'status', 'blogs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'commentable_id' => 'required',
            'commentable_type' => 'required',
        ]);

        Comment::create([
            'user_id' => auth()->id(),
            'commentable_id' => $request->commentable_id,
            'commentable_type' => $request->commentable_type,
            'content' => $request->content,
            'status' => 'active', // Admin thêm thì active luôn
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'Đã thêm bình luận mới.');
    }

    public function toggleStatus($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->status = ($comment->status === 'active') ? 'pending' : 'active';
        $comment->save();

        return response()->json([
            'success' => true, 
            'status' => $comment->status,
            'message' => $comment->status === 'active' ? 'Đã phê duyệt bình luận' : 'Đã tạm ẩn bình luận'
        ]);
    }

    public function reply(Request $request, $id)
    {
        $request->validate(['content' => 'required|string']);
        $parent = Comment::findOrFail($id);

        Comment::create([
            'user_id' => auth()->id(),
            'parent_id' => $parent->id,
            'commentable_id' => $parent->commentable_id,
            'commentable_type' => $parent->commentable_type,
            'content' => $request->content,
            'status' => 'active',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'Đã gửi phản hồi.');
    }

    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();
        return back()->with('success', 'Đã xóa bình luận.');
    }
}
