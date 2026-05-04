<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\GeneralNotification;
use App\Services\NotificationAudienceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Gate;

class NotificationController extends Controller
{
    protected $audienceService;

    public function __construct(NotificationAudienceService $audienceService)
    {
        $this->audienceService = $audienceService;
    }

    public function index(Request $request)
    {
        Gate::authorize('notifications.history');

        $query = \App\Models\NotificationHistory::with('sender');

        // Tìm kiếm tiêu đề/nội dung
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('body', 'like', '%' . $request->search . '%');
        }

        // Lọc theo kênh gửi
        if ($request->filled('channel')) {
            $query->whereJsonContains('channels', $request->channel);
        }

        // Lọc theo người gửi
        if ($request->filled('sender_id')) {
            $query->where('sender_id', $request->sender_id);
        }

        $histories = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();
            
        $audienceTypes = $this->audienceService->getAvailableTypes();
        
        // Lấy danh sách những người đã từng gửi thông báo để lọc
        $senders = User::whereHas('role', function($q) {
            $q->where('is_admin', true);
        })->get();

        $channels = [
            'database' => 'Web (In-app)',
            'mail'     => 'Email',
            'fcm'      => 'Firebase (Push)'
        ];

        return view('admin.notifications.index', compact('histories', 'audienceTypes', 'senders', 'channels'));
    }

    public function create(Request $request)
    {
        Gate::authorize('notifications.create');

        $audienceTypes = $this->audienceService->getAvailableTypes();
        $users = User::orderBy('name')->get(); 
        
        $template = null;
        if ($request->has('from_history')) {
            $template = \App\Models\NotificationHistory::find($request->from_history);
        }

        return view('admin.notifications.create', compact('audienceTypes', 'users', 'template'));
    }

    public function store(Request $request)
    {
        Gate::authorize('notifications.create');

        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'body' => 'required|string',
                'audience_type' => 'required|string',
                'channels' => 'required|array',
                'target_id' => 'required_if:audience_type,single',
                'url' => 'nullable|url',
                'image' => 'nullable|string',
            ]);

            $users = $this->audienceService->getUsersByType(
                $request->audience_type, 
                $request->target_id
            );

            if ($users->isEmpty()) {
                return back()->with('error', 'Không tìm thấy người nhận phù hợp.');
            }

            // Sử dụng helper toàn cục để gửi thông báo
            $sent = sendNotification(
                [
                    'title' => $request->title,
                    'body'  => $request->body,
                    'url'   => $request->url,
                    'image' => $request->image,
                ],
                [
                    'channels'      => $request->channels,
                    'audience_type' => $request->audience_type,
                    'target_id'     => $request->target_id,
                ]
            );

            if (!$sent) {
                return back()->with('error', 'Có lỗi xảy ra khi gửi thông báo hoặc không tìm thấy người nhận.');
            }

            return redirect()->route('admin.notifications.index')
                ->with('success', 'Thông báo đã được đẩy vào hàng đợi (' . implode(', ', $request->channels) . ') để gửi đến ' . $users->count() . ' người dùng.');

        } catch (\Exception $e) {
            \Log::error('Notification Error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        Gate::authorize('notifications.history');

        try {
            $history = \App\Models\NotificationHistory::findOrFail($id);
            
            // Xóa tất cả thông báo tương ứng ở cái chuông của User
            \Illuminate\Support\Facades\DB::table('notifications')
                ->where('data->history_id', $id)
                ->delete();

            $history->delete();
            return back()->with('success', 'Đã xóa lịch sử và tất cả thông báo liên quan.');
        } catch (\Exception $e) {
            \Log::error('Delete Notification History Error: ' . $e->getMessage());
            return back()->with('error', 'Lỗi khi xóa: ' . $e->getMessage());
        }
    }

    /**
     * User tự xóa thông báo của mình
     */
    public function deletePersonal($id)
    {
        try {
            $notification = auth()->user()->notifications()->where('id', $id)->first();

            if ($notification) {
                $notification->delete();
                return response()->json([
                    'success' => true,
                    'unread_count' => auth()->user()->unreadNotifications()->count()
                ]);
            }
            
            return response()->json(['success' => false, 'message' => 'Không tìm thấy thông báo.'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Bulk delete personal notifications
     */
    public function bulkDeletePersonal(Request $request)
    {
        try {
            $ids = $request->ids;
            if (empty($ids)) {
                return response()->json(['success' => false, 'message' => 'Danh sách trống.']);
            }

            auth()->user()->notifications()->whereIn('id', $ids)->delete();

            return response()->json([
                'success' => true,
                'unread_count' => auth()->user()->unreadNotifications()->count()
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Trang danh sách thông báo của User (Mailbox view)
     */
    public function userList(Request $request)
    {
        $type = $request->query('type');
        $query = auth()->user()->notifications();
        
        if ($type === 'unread') {
            $query = auth()->user()->unreadNotifications();
        }
        
        $notifications = $query->paginate(20)->withQueryString();
        $unreadCount = auth()->user()->unreadNotifications()->count();
        
        return view('admin.notifications.user_list', compact('notifications', 'unreadCount', 'type'));
    }

    /**
     * Xem chi tiết 1 thông báo trong giao diện Mailbox
     */
    public function show(Request $request, $id)
    {
        $type = $request->query('type');
        
        // Đánh dấu là đã đọc nếu chưa đọc
        $notification = auth()->user()->notifications()->where('id', $id)->firstOrFail();
        if (!$notification->read_at) {
            $notification->markAsRead();
        }

        $query = auth()->user()->notifications();
        if ($type === 'unread') {
            $query = auth()->user()->unreadNotifications();
        }
        
        $notifications = $query->paginate(20)->withQueryString();
        $unreadCount = auth()->user()->unreadNotifications()->count();
        $selectedNotification = $notification;
        
        return view('admin.notifications.user_list', compact('notifications', 'unreadCount', 'selectedNotification', 'type'));
    }

    /**
     * Đánh dấu tất cả thông báo là đã đọc
     */
    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    }
}
