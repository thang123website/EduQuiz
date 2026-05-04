<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                "content" => "required|string|max:1000",
                "commentable_id" => "required",
                "commentable_type" => "required",
            ]);

            $comment = Comment::create([
                "user_id" => auth()->id(),
                "commentable_id" => $request->commentable_id,
                "commentable_type" => $request->commentable_type,
                "parent_id" => $request->parent_id,
                "content" => $request->content,
                "status" => "pending", 
                "ip_address" => $request->ip(),
                "user_agent" => $request->userAgent(),
            ]);

            return response()->json([
                "success" => true,
                "message" => "Bình luận của bạn đã được gửi và đang chờ phê duyệt.",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Lỗi: " . $e->getMessage(),
            ], 500);
        }
    }
}
