<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CommentController extends Controller
{
    /**
     * Display a listing of the comments for a given post.
     * Hiển thị danh sách comment cho 1 post
     */
    public function index($postId) // Thay đổi để nhận postId thay vì Post model
    {
        // Bắt đầu try block để xử lý lỗi ModelNotFoundException khi tìm post thủ công
        try {
            // Tự tay tìm bài viết. Nếu không tìm thấy, ModelNotFoundException sẽ được ném ra tại đây
            $post = Post::findOrFail($postId);

            $comments = $post->comments()
                ->whereNull('parent_id')
                ->with(['user', 'replies.user' => function($query) {
                    $query->orderBy('created_at', 'asc');
                }])
                ->orderBy('created_at', 'desc')
                ->get();

            // Trả về view hiển thị bình luận nếu tìm thấy bài viết
            return view('posts.comment', compact('post', 'comments'));

        } catch (ModelNotFoundException $e) {
            // Bắt ngoại lệ khi không tìm thấy bài viết
            return redirect()
                ->route('posts.index') // Chuyển hướng về trang danh sách bài viết
                ->with('error', 'Bài viết bạn đang tìm kiếm đã bị xóa hoặc không tồn tại.');
        }
    }

    public function store(Request $request, Post $post)
    {
        $request->validate([
            'content' => 'required|string|max:1000'
        ], [
            'content.required' => 'Nội dung bình luận không được để trống.',
            'content.string' => 'Nội dung bình luận phải là văn bản.',
            'content.max' => 'Nội dung bình luận không được vượt quá :max ký tự.'
        ]);

        try {
            $comment = $post->comments()->create([
                'user_id' => auth()->id(),
                'content' => $request->content
            ]);

            // Gửi thông báo cho chủ bài viết nếu người bình luận không phải là chủ bài viết
            if ($post->user_id !== auth()->id()) {
                $post->user->notify(new \App\Notifications\CommentNotification(auth()->user(), $comment));
            }

            return redirect()
                ->route('posts.show', $post->id)
                ->with('success', 'Bình luận đã được thêm thành công.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi thêm bình luận. Vui lòng thử lại.');
        }
    }

    public function reply(Request $request, Comment $comment)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000'
        ], [
            'content.required' => 'Nội dung trả lời không được để trống.',
            'content.string' => 'Nội dung trả lời phải là văn bản.',
            'content.max' => 'Nội dung trả lời không được vượt quá :max ký tự.'
        ]);

        try {
            $reply = Comment::create([
                'content' => $validated['content'],
                'user_id' => auth()->id(),
                'post_id' => $comment->post_id,
                'parent_id' => $comment->id
            ]);

            // Lấy lại danh sách bình luận mới sau khi thêm trả lời
            $post = Post::findOrFail($comment->post_id);
            $comments = $post->comments()
                ->whereNull('parent_id')
                ->with(['user', 'replies.user' => function($query) {
                    $query->orderBy('created_at', 'asc');
                }])
                ->orderBy('created_at', 'desc')
                ->get();

            return view('posts.comment', compact('post', 'comments'))
                ->with('success', 'Trả lời đã được thêm thành công.');

        } catch (\Exception $e) {
            return redirect()
                ->route('posts.show', $comment->post_id) // Redirect về trang comments với lỗi
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi thêm trả lời. Vui lòng thử lại.');
        }
    }

    public function destroy(Comment $comment)
    {
        if (auth()->id() !== $comment->user_id) {
            return redirect()
                ->route('posts.show', $comment->post_id)
                ->with('error', 'Bạn không có quyền xóa bình luận này.');
        }

        try {
            $post_id = $comment->post_id; // Lưu post_id trước khi xóa
            $comment->delete();

            // Lấy lại danh sách bình luận mới sau khi xóa
            $post = Post::findOrFail($post_id);
            $comments = $post->comments()
                ->whereNull('parent_id')
                ->with(['user', 'replies.user' => function($query) {
                    $query->orderBy('created_at', 'asc');
                }])
                ->orderBy('created_at', 'desc')
                ->get();

            return view('posts.comment', compact('post', 'comments'))
                ->with('success', 'Bình luận đã được xóa thành công.');

        } catch (\Exception $e) {
            return redirect()
                ->route('posts.show', $comment->post_id)
                ->with('error', 'Có lỗi xảy ra khi xóa bình luận. Vui lòng thử lại.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Comment $comment)
    {
        // Kiểm tra quyền chỉnh sửa
        if (Auth::id() !== $comment->user_id) {
            return redirect()
                ->route('comments.edit', $comment->id)
                ->with('error', 'Bạn không có quyền chỉnh sửa bình luận này.');
        }

        $request->validate([
            'content' => 'required|string|max:1000',
        ], [
            'content.required' => 'Nội dung bình luận không được để trống.',
            'content.string' => 'Nội dung bình luận phải là văn bản.',
            'content.max' => 'Nội dung bình luận không được vượt quá :max ký tự.',
        ]);

        try {
            $comment->content = $request->content;
            $comment->save();

            // Lấy lại danh sách bình luận mới sau khi cập nhật
            $post = Post::findOrFail($comment->post_id);
            $comments = $post->comments()
                ->whereNull('parent_id')
                ->with(['user', 'replies.user' => function($query) {
                    $query->orderBy('created_at', 'asc');
                }])
                ->orderBy('created_at', 'desc')
                ->get();

            return view('posts.comment', compact('post', 'comments'))
                ->with('success', 'Bình luận đã được cập nhật thành công.');

        } catch (\Exception $e) {
            return redirect()
                ->route('comments.edit', $comment->id)
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi cập nhật bình luận. Vui lòng thử lại.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Comment $comment)
    {
        // Kiểm tra quyền chỉnh sửa
        if (Auth::id() !== $comment->user_id) {
            return redirect()
                ->route('posts.show', $comment->post_id)
                ->with('error', 'Bạn không có quyền chỉnh sửa bình luận này.');
        }

        return view('comments.edit', compact('comment'));
    }
}