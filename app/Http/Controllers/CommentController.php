<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    // Hiển thị danh sách comment cho 1 post
    public function index($postId)
    {
        $comments = Comment::where('post_id', $postId)->with('user')->latest()->get();
        $post = Post::findOrFail($postId);
        return view('posts.comment', compact('comments', 'post'));
    }

    public function store(Request $request, $postId)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        Comment::create([
            'user_id' => auth()->id(),
            'post_id' => $postId,
            'content' => $request->content,
        ]);

        return redirect()->route('comments.index', ['post' => $postId])
                         ->with('success', 'Bình luận đã được gửi!');
    }
}