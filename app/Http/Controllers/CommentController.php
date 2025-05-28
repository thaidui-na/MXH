<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use App\Models\Notification;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    // Hiển thị danh sách comment cho 1 post
    public function index(Post $post)
    {
        $comments = $post->comments()
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('posts.comment', compact('post', 'comments'));
    }

    public function store(Request $request, Post $post)
    {
        $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        $comment = $post->comments()->create([
            'user_id' => auth()->id(),
            'content' => $request->content
        ]);

        // Tạo thông báo cho chủ bài viết
        if ($post->user_id !== auth()->id()) {
            \App\Models\Notification::create([
                'user_id' => $post->user_id,
                'sender_id' => auth()->id(),
                'type' => 'comment',
                'notifiable_type' => 'App\\Models\\Comment',
                'notifiable_id' => $comment->id,
                'data' => [
                    'post_id' => $post->id,
                    'post_title' => $post->title,
                    'comment_id' => $comment->id
                ]
            ]);
            
            \Log::info('Notification created for comment', [
                'user_id' => $post->user_id,
                'sender_id' => auth()->id(),
                'type' => 'comment',
                'notifiable_type' => 'App\\Models\\Comment',
                'notifiable_id' => $comment->id,
            ]);
        }

        return response()->json([
            'success' => true,
            'comment' => $comment->load('user')
        ]);
    }

    public function reply(Request $request, Comment $comment)
    {
        $validated = $request->validate([
            'content' => 'required|string'
        ]);

        $reply = Comment::create([
            'content' => $validated['content'],
            'user_id' => auth()->id(),
            'post_id' => $comment->post_id,
            'parent_id' => $comment->id
        ]);

        return back()->with('success', 'Đã thêm trả lời');
    }

    public function destroy(Comment $comment)
    {
        if (auth()->id() !== $comment->user_id) {
            abort(403);
        }

        $comment->delete();
        return back()->with('success', 'Đã xóa bình luận');
    }
}