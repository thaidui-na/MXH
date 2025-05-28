<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostShare;
use Illuminate\Http\Request;

class PostShareController extends Controller
{
    public function share(Request $request, Post $post)
    {
        $request->validate([
            'caption' => 'nullable|string|max:500'
        ]);

        try {
            // Tạo bài viết mới dựa trên bài viết được chia sẻ
            $sharedPost = Post::create([
                'user_id' => auth()->id(),
                'title' => $post->title,
                'content' => $post->content,
                'image' => $post->image,
                'is_public' => true,
                'is_shared' => true
            ]);

            // Lưu thông tin chia sẻ
            PostShare::create([
                'post_id' => $sharedPost->id,
                'user_id' => auth()->id(),
                'shared_by' => $post->user_id,
                'caption' => $request->caption
            ]);

            // Gửi thông báo cho chủ bài viết gốc
            if ($post->user_id !== auth()->id()) {
                $post->user->notify(new \App\Notifications\PostShareNotification(auth()->user(), $post));
            }

            return response()->json([
                'success' => true,
                'message' => 'Chia sẻ bài viết thành công!',
                'post' => $sharedPost->load('user')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi chia sẻ bài viết: ' . $e->getMessage()
            ], 500);
        }
    }

    public function shareToMessage(Request $request, Post $post)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        try {
            // Tạo tin nhắn mới với nội dung bài viết
            $message = \App\Models\Message::create([
                'sender_id' => auth()->id(),
                'receiver_id' => $request->user_id,
                'content' => "Tôi đã chia sẻ bài viết: {$post->title}\nLink: " . route('posts.show', $post->id)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Đã gửi bài viết qua tin nhắn!',
                'data' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi gửi bài viết qua tin nhắn: ' . $e->getMessage()
            ], 500);
        }
    }
} 