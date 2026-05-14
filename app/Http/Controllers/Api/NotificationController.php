<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\NotificationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Lấy danh sách thông báo của người dùng (có phân trang)
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 15);
        $notifications = $request->user()->notifications()->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'message' => 'Lấy danh sách thông báo thành công.',
            'data' => [
                'items' => NotificationResource::collection($notifications),
                'pagination' => [
                    'total' => $notifications->total(),
                    'per_page' => $notifications->perPage(),
                    'current_page' => $notifications->currentPage(),
                    'last_page' => $notifications->lastPage(),
                    'from' => $notifications->firstItem(),
                    'to' => $notifications->lastItem(),
                ],
            ]
        ]);
    }

    /**
     * Lấy số lượng thông báo chưa đọc
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $count = $request->user()->unreadNotifications()->count();

        return response()->json([
            'status' => 'success',
            'message' => 'Lấy số lượng thông báo chưa đọc thành công.',
            'data' => [
                'unread_count' => $count,
            ]
        ]);
    }

    /**
     * Đánh dấu một thông báo là đã đọc
     */
    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $notification = $request->user()->notifications()->where('id', $id)->first();

        if (!$notification) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy thông báo.',
            ], 404);
        }

        if (!$notification->read_at) {
            $notification->markAsRead();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Đã đánh dấu thông báo là đã đọc.',
        ]);
    }

    /**
     * Đánh dấu tất cả thông báo là đã đọc
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json([
            'status' => 'success',
            'message' => 'Đã đánh dấu tất cả thông báo là đã đọc.',
        ]);
    }

    /**
     * Xóa một thông báo
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $notification = $request->user()->notifications()->where('id', $id)->first();

        if (!$notification) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy thông báo.',
            ], 404);
        }

        $notification->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Đã xóa thông báo.',
        ]);
    }
}
