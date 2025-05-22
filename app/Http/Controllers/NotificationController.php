<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->paginate(20);
        // Đánh dấu tất cả là đã đọc
        $user->unreadNotifications->markAsRead();
        return view('notifications.index', compact('notifications'));
    }
}
