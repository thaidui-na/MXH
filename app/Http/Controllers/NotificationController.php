<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Hiển thị trang thông báo
     */
    public function index()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->paginate(20);
        
        // Đánh dấu tất cả thông báo là đã đọc
        $user->unreadNotifications->markAsRead();
        
        return view('notifications.index', compact('notifications'));
    }

    /**
     * Đánh dấu một thông báo là đã đọc
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($id);
        $notification->markAsRead();
        
        return response()->json(['success' => true]);
    }

    /**
     * Xóa một thông báo
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $user->notifications()->findOrFail($id)->delete();
        
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
} 