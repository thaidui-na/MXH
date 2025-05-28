<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Notification;

class NotificationController extends Controller
{
    /**
     * Hiển thị trang thông báo
     */
    public function index()
    {
        $notifications = auth()->user()->notifications()
            ->with(['sender', 'notifiable'])
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Đánh dấu một thông báo là đã đọc
     */
    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->update(['read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Xóa một thông báo
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($id);
        // Nếu là thông báo kết bạn thì xóa luôn bản ghi pending trong bảng friends
        if ($notification->type === 'App\\Notifications\\FriendRequestNotification') {
            $fromUserId = $notification->data['user_id'] ?? null;
            if ($fromUserId) {
                DB::table('friends')
                    ->where('user_id', $fromUserId)
                    ->where('friend_id', $user->id)
                    ->where('status', 'pending')
                    ->delete();
            }
        }
        $notification->delete();
        return response()->json(['success' => true]);
    }

    /**
     * Xóa tất cả thông báo
     */
    public function clearAll()
    {
        $user = Auth::user();
        $user->notifications()->delete();
        
        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        auth()->user()->notifications()->update(['read' => true]);

        return response()->json(['success' => true]);
    }

    public function getUnreadCount()
    {
        $count = auth()->user()->notifications()->where('read', false)->count();
        
        return response()->json(['count' => $count]);
    }
} 