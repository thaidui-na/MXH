<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    /**
     * Hiển thị trang tin nhắn
     */
    public function index()
    {
        // Lấy danh sách người dùng, trừ user hiện tại
        $users = User::where('id', '!=', auth()->id())->get();

        // Lấy tin nhắn với người dùng đầu tiên (nếu có)
        $selectedUser = $users->first();
        $messages = [];
        
        if ($selectedUser) {
            $messages = $this->getMessagesWith($selectedUser->id);
        }

        return view('messages.index', compact('users', 'selectedUser', 'messages'));
    }

    /**
     * Hiển thị cuộc trò chuyện với một người dùng cụ thể
     */
    public function show($userId)
    {
        $selectedUser = User::findOrFail($userId);
        $messages = $this->getMessagesWith($userId);

        // Đánh dấu tin nhắn là đã đọc
        auth()->user()->receivedMessages()
            ->where('sender_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        if (request()->ajax()) {
            return response()->json([
                'messages' => view('messages.partials.message-list', compact('messages', 'selectedUser'))->render(),
                'user' => $selectedUser
            ]);
        }

        $users = User::where('id', '!=', auth()->id())->get();
        return view('messages.index', compact('users', 'selectedUser', 'messages'));
    }

    /**
     * Gửi tin nhắn mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'content' => 'required|string'
        ]);

        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'content' => $request->content
        ]);

        if ($request->ajax()) {
            return response()->json([
                'message' => view('messages.partials.single-message', ['message' => $message])->render()
            ]);
        }

        return back();
    }

    /**
     * Lấy tin nhắn giữa người dùng hiện tại và một người dùng khác
     */
    private function getMessagesWith($userId)
    {
        return Message::where(function($query) use ($userId) {
            $query->where('sender_id', auth()->id())
                  ->where('receiver_id', $userId);
        })->orWhere(function($query) use ($userId) {
            $query->where('sender_id', $userId)
                  ->where('receiver_id', auth()->id());
        })->orderBy('created_at', 'asc')->get();
    }

    /**
     * Lấy tin nhắn mới (cho Ajax polling)
     */
    public function getNewMessages($userId)
    {
        $messages = Message::where('sender_id', $userId)
            ->where('receiver_id', auth()->id())
            ->where('is_read', false)
            ->get();

        // Đánh dấu là đã đọc
        foreach ($messages as $message) {
            $message->update(['is_read' => true]);
        }

        return response()->json([
            'messages' => view('messages.partials.message-list', ['messages' => $messages])->render()
        ]);
    }

    /**
     * Lấy trạng thái tin nhắn của tất cả người dùng
     */
    public function getUsersStatus()
    {
        $users = User::where('id', '!=', auth()->id())
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'unread_count' => auth()->user()->getUnreadMessagesFrom($user->id),
                    'last_message' => $user->getLastMessageWith(auth()->id())
                ];
            });

        return response()->json(['users' => $users]);
    }
} 