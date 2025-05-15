<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CommentController extends Controller
{
    use AuthorizesRequests;

    // Hiển thị danh sách comment cho 1 post
    public function index(Post $post)
    {
        $comments = $post->comments()->with('user')->latest()->paginate(10);
        return view('posts.comment', compact('post', 'comments'));
    }

    public function store(Request $request, Post $post)
    {
        $validated = $request->validate([
            'content' => 'required|string'
        ]);

        $comment = $post->comments()->create([
            'content' => $validated['content'],
            'user_id' => auth()->id()
        ]);

        return back()->with('success', 'Bình luận đã được thêm thành công.');
    }

    public function update(Request $request, Comment $comment)
    {
        // Kiểm tra xem người dùng hiện tại có phải là người tạo bình luận không
        if (auth()->id() !== $comment->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate(['content' => 'required|string']);
        $comment->update($validated);
        return back()->with('success', 'Đã cập nhật bình luận');
    }

    public function destroy(Comment $comment)
    {
        // Kiểm tra xem người dùng hiện tại có phải là người tạo bình luận không
        if (auth()->id() !== $comment->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $comment->delete();
        return back()->with('success', 'Đã xóa bình luận');
    }

    public function storeReply(Request $request, Comment $comment)
    {
        $validated = $request->validate([
            'content' => 'required|string'
        ]);

        $reply = new Comment([
            'content' => $validated['content'],
            'user_id' => auth()->id(),
            'parent_id' => $comment->id,
            'post_id' => $comment->post_id
        ]);

        $reply->save();

        return back()->with('success', 'Đã thêm trả lời');
    }
}