<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PostController extends Controller
{
    /**
     * Hiển thị danh sách bài viết
     */
    public function index()
    {
        // Lấy tất cả bài viết công khai, mới nhất đầu tiên
        $posts = Post::with('user') // Eager loading để tối ưu truy vấn
                    ->where('is_public', true) // Chỉ lấy bài viết công khai
                    ->latest()
                    ->paginate(10); // Phân trang mỗi trang 10 bài

        return view('posts.index', compact('posts'));
    }

    /**
     * Hiển thị danh sách bài viết của user hiện tại
     */
    public function myPosts()
    {
        // Lấy bài viết của user đăng nhập
        $posts = auth()->user()->posts()
                    ->latest()
                    ->paginate(10);

        return view('posts.my_posts', compact('posts'));
    }

    /**
     * Hiển thị form tạo bài viết mới
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Lưu bài viết mới vào database
     */
    public function store(Request $request)
    {
        // Validate dữ liệu đầu vào
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        // Thêm user_id vào dữ liệu
        $validated['user_id'] = auth()->id();
        
        // Tất cả bài viết đều công khai
        $validated['is_public'] = true;

        // Tạo bài viết mới
        Post::create($validated);

        return redirect()
            ->route('posts.my_posts')
            ->with('success', 'Bài viết đã được đăng thành công!');
    }

    /**
     * Hiển thị chi tiết một bài viết
     */
    public function show(Post $post)
    {
        // Chỉ cho phép xem bài viết của mình hoặc bài viết công khai
        if (!$post->is_public && $post->user_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền xem bài viết này.');
        }

        return view('posts.show', compact('post'));
    }

    /**
     * Hiển thị form chỉnh sửa bài viết
     */
    public function edit(Post $post)
    {
        // Kiểm tra nếu người dùng không phải tác giả
        if ($post->user_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền chỉnh sửa bài viết này.');
        }

        return view('posts.edit', compact('post'));
    }

    /**
     * Cập nhật bài viết
     */
    public function update(Request $request, Post $post)
    {
        // Kiểm tra nếu người dùng không phải tác giả
        if ($post->user_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền chỉnh sửa bài viết này.');
        }

        // Validate dữ liệu đầu vào
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        // Tất cả bài viết đều công khai
        $validated['is_public'] = true;

        // Cập nhật bài viết
        $post->update($validated);

        return redirect()
            ->route('posts.show', $post)
            ->with('success', 'Bài viết đã được cập nhật thành công!');
    }

    /**
     * Xóa bài viết
     */
    public function destroy(Post $post)
    {
        // Kiểm tra nếu người dùng không phải tác giả
        if ($post->user_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền xóa bài viết này.');
        }

        $post->delete();

        return redirect()
            ->route('posts.my_posts')
            ->with('success', 'Bài viết đã được xóa thành công!');
    }
} 