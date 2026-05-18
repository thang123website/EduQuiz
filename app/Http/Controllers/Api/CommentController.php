<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Resources\CommentResource;
use App\Models\Setting;

class CommentController extends Controller
{
    /**
     * Lấy danh sách bình luận của bài viết
     */
    public function index($slug, Request $request)
    {
        $limit = $request->input('limit', 10);
        
        $blog = Blog::where('slug', $slug)
            ->where('status', 'publish')
            ->whereNotNull('title->' . app()->getLocale())
            ->firstOrFail();

        // Chỉ lấy bình luận đã duyệt và là bình luận gốc (parent_id = null)
        // Load kèm theo user của bình luận gốc và các phản hồi (kèm user của phản hồi)
        $comments = $blog->comments()
            ->with(['user', 'replies.user'])
            ->where('status', 'active')
            ->paginate($limit);

        return CommentResource::collection($comments)->additional([
            'status' => 'success',
        ]);
    }

    /**
     * Thêm bình luận cho bài viết
     */
    public function store($slug, Request $request)
    {
        $blog = Blog::where('slug', $slug)
            ->where('status', 'publish')
            ->whereNotNull('title->' . app()->getLocale())
            ->firstOrFail();

        if (!$blog->enable_comment) {
            return response()->json([
                'status' => 'error',
                'message' => 'Bài viết này đã tắt tính năng bình luận.'
            ], 403);
        }

        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id'
        ]);

        // Đọc cấu hình xem có cần duyệt bình luận không
        $requireApproval = Setting::get('comment_approval_required', 0) == '1';
        $status = $requireApproval ? 'pending' : 'active';

        $comment = new Comment([
            'user_id' => auth('sanctum')->id(),
            'content' => $validated['content'],
            'parent_id' => $validated['parent_id'] ?? null,
            'status' => $status,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $blog->comments()->save($comment);

        $message = $requireApproval 
            ? 'Bình luận của bạn đã được gửi và đang chờ kiểm duyệt.' 
            : 'Bình luận thành công.';

        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => new CommentResource($comment->load('user'))
        ], 201);
    }

    /**
     * Chỉnh sửa bình luận
     */
    public function update($id, Request $request)
    {
        $comment = Comment::findOrFail($id);

        if ($comment->user_id !== auth('sanctum')->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Bạn không có quyền chỉnh sửa bình luận này.'
            ], 403);
        }

        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        // Đọc cấu hình xem có cần duyệt lại bình luận sau khi sửa không
        $requireApproval = Setting::get('comment_approval_required', 0) == '1';
        if ($requireApproval) {
            $comment->status = 'pending';
        }

        $comment->content = $validated['content'];
        $comment->save();

        $message = $requireApproval 
            ? 'Bình luận của bạn đã được cập nhật và đang chờ kiểm duyệt lại.' 
            : 'Cập nhật bình luận thành công.';

        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => new CommentResource($comment->load('user'))
        ]);
    }

    /**
     * Xóa bình luận
     */
    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);

        if ($comment->user_id !== auth('sanctum')->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Bạn không có quyền xóa bình luận này.'
            ], 403);
        }

        // Xóa các phản hồi (replies) của bình luận này
        $comment->allReplies()->delete();
        $comment->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Đã xóa bình luận thành công.'
        ]);
    }
}
