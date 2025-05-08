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

    public function destroy($commentId)
    {
        $comment = \App\Models\Comment::findOrFail($commentId);

        // Admin xóa được tất cả, user chỉ xóa được bình luận của mình
        if (!auth()->user()->is_admin && $comment->user_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền xóa bình luận này.');
        }

        $comment->delete();

        return back()->with('success', 'Đã xóa bình luận!');
    }

    public function edit($commentId)
    {
        $comment = \App\Models\Comment::findOrFail($commentId);

        // Admin sửa được tất cả, user chỉ sửa được bình luận của mình
        if (!auth()->user()->is_admin && $comment->user_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền sửa bình luận này.');
        }

        return view('posts.edit_comment', compact('comment'));
    }

    public function update(Request $request, $commentId)
    {
        $comment = \App\Models\Comment::findOrFail($commentId);

        // Admin sửa được tất cả, user chỉ sửa được bình luận của mình
        if (!auth()->user()->is_admin && $comment->user_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền sửa bình luận này.');
        }

        $request->validate([
            'content' => 'required|string',
        ]);

        $comment->content = $request->content;
        $comment->save();

        // Chuyển hướng về trang danh sách bình luận của bài viết
        return redirect()->route('comments.index', ['post' => $comment->post_id])
                         ->with('success', 'Bình luận đã được cập nhật!');
    }
}