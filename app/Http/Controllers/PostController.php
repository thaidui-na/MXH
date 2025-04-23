<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * Controller quản lý các chức năng liên quan đến bài viết (Posts)
 * Bao gồm: hiển thị danh sách, xem chi tiết, tạo, sửa, xóa bài viết
 */
class PostController extends Controller
{
    /**
     * Hiển thị danh sách tất cả bài viết công khai (trang Bảng tin).
     * Sử dụng eager loading để tải thông tin người đăng.
     * Phân trang kết quả.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Lấy các bài viết từ database
        $posts = Post::with('user') // Tải kèm thông tin của tác giả (relationship 'user') để tránh N+1 query
                    ->where('is_public', true) // Chỉ lấy những bài viết được đánh dấu là công khai
                    ->latest() // Sắp xếp theo thứ tự mới nhất lên đầu (dựa vào cột created_at hoặc updated_at)
                    ->paginate(10); // Phân trang kết quả, mỗi trang hiển thị 10 bài viết

        // Trả về view 'posts.index' và truyền biến 'posts' chứa dữ liệu bài viết vào view
        return view('posts.index', compact('posts'));
    }

    /**
     * Hiển thị danh sách các bài viết của người dùng đang đăng nhập.
     * Phân trang kết quả.
     *
     * @return \Illuminate\View\View
     */
    public function myPosts()
    {
        // Lấy các bài viết thuộc về người dùng đang đăng nhập
        $posts = auth()->user() // Lấy đối tượng User đang đăng nhập
                    ->posts() // Truy cập relationship 'posts' đã định nghĩa trong User model
                    ->latest() // Sắp xếp theo thứ tự mới nhất lên đầu
                    ->paginate(10); // Phân trang kết quả, mỗi trang 10 bài viết

        // Trả về view 'posts.my_posts' và truyền biến 'posts' vào view
        return view('posts.my_posts', compact('posts'));
    }

    /**
     * Hiển thị form để tạo một bài viết mới.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Chỉ đơn giản trả về view 'posts.create' chứa form nhập liệu
        return view('posts.create');
    }

    /**
     * Lưu bài viết mới được tạo từ form vào database.
     * Validate dữ liệu đầu vào.
     * Tự động gán user_id và đánh dấu bài viết là công khai.
     *
     * @param  \Illuminate\Http\Request  $request Dữ liệu gửi lên từ form tạo bài viết
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate dữ liệu nhận được từ request
        $validated = $request->validate([
            'title' => 'required|string|max:255', // Tiêu đề là bắt buộc, dạng chuỗi, tối đa 255 ký tự
            'content' => 'required|string', // Nội dung là bắt buộc, dạng chuỗi
            // 'is_public' không cần validate ở đây vì sẽ được gán cố định
        ]);

        // Thêm user_id của người dùng đang đăng nhập vào mảng dữ liệu đã validate
        $validated['user_id'] = auth()->id();

        // Gán giá trị cố định cho trường is_public (mặc định mọi bài viết đều công khai)
        // Nếu bạn muốn có tùy chọn riêng tư/công khai, cần thêm trường này vào form và validate
        $validated['is_public'] = true;

        // Tạo bản ghi bài viết mới trong database với dữ liệu đã chuẩn bị
        Post::create($validated);

        // Chuyển hướng người dùng về trang "Bài viết của tôi"
        return redirect()
            ->route('posts.my_posts') // Route name của trang danh sách bài viết của tôi
            ->with('success', 'Bài viết đã được đăng thành công!'); // Gửi kèm thông báo thành công (flash message)
    }

    /**
     * Hiển thị chi tiết của một bài viết cụ thể.
     * Sử dụng Route Model Binding để tự động tìm Post dựa trên ID trong route.
     * Kiểm tra quyền xem (chỉ cho xem bài công khai hoặc bài của chính mình).
     *
     * @param  \App\Models\Post  $post Đối tượng Post được tự động inject bởi Laravel
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show(Post $post) // Laravel tự động tìm Post có ID tương ứng với tham số route
    {
        // Kiểm tra quyền truy cập:
        // Nếu bài viết không công khai (! $post->is_public)
        // VÀ người xem không phải là tác giả bài viết ($post->user_id !== auth()->id())
        if (!$post->is_public && $post->user_id !== auth()->id()) {
            // Thì dừng thực thi và trả về lỗi 403 (Forbidden)
            abort(403, 'Bạn không có quyền xem bài viết này.');
        }

        // Nếu có quyền xem, trả về view 'posts.show' và truyền đối tượng 'post' vào view
        return view('posts.show', compact('post'));
    }

    /**
     * Hiển thị form để chỉnh sửa một bài viết đã tồn tại.
     * Sử dụng Route Model Binding.
     * Kiểm tra quyền chỉnh sửa (chỉ tác giả mới được sửa).
     *
     * @param  \App\Models\Post  $post Đối tượng Post cần chỉnh sửa
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit(Post $post) // Inject Post model
    {
        // Kiểm tra xem người dùng đang đăng nhập có phải là tác giả của bài viết không
        if ($post->user_id !== auth()->id()) {
            // Nếu không phải, trả về lỗi 403
            abort(403, 'Bạn không có quyền chỉnh sửa bài viết này.');
        }

        // Nếu là tác giả, trả về view 'posts.edit' và truyền đối tượng 'post' vào view
        return view('posts.edit', compact('post'));
    }

    /**
     * Cập nhật thông tin bài viết trong database sau khi submit form chỉnh sửa.
     * Sử dụng Route Model Binding.
     * Kiểm tra quyền chỉnh sửa.
     * Validate dữ liệu đầu vào.
     *
     * @param  \Illuminate\Http\Request  $request Dữ liệu gửi lên từ form chỉnh sửa
     * @param  \App\Models\Post  $post Đối tượng Post cần cập nhật
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Post $post) // Inject Request và Post
    {
        // Kiểm tra quyền chỉnh sửa (giống như trong hàm edit)
        if ($post->user_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền chỉnh sửa bài viết này.');
        }

        // Validate dữ liệu gửi lên từ form
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
             // 'is_public' không cần validate nếu luôn là true
        ]);

        // Gán lại giá trị is_public (nếu logic ứng dụng yêu cầu mọi bài viết đều công khai)
        $validated['is_public'] = true;

        // Cập nhật bản ghi bài viết trong database với dữ liệu đã validate
        $post->update($validated);

        // Chuyển hướng người dùng về trang xem chi tiết bài viết vừa cập nhật
        return redirect()
            ->route('posts.show', $post) // Route name của trang xem chi tiết, truyền đối tượng post
            ->with('success', 'Bài viết đã được cập nhật thành công!'); // Gửi kèm thông báo thành công
    }

    /**
     * Xóa một bài viết khỏi database.
     * Sử dụng Route Model Binding.
     * Kiểm tra quyền xóa (chỉ tác giả mới được xóa).
     *
     * @param  \App\Models\Post  $post Đối tượng Post cần xóa
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Post $post) // Inject Post model
    {
        // Kiểm tra quyền xóa (giống như trong hàm edit và update)
        if ($post->user_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền xóa bài viết này.');
        }

        // Thực hiện xóa bản ghi bài viết khỏi database
        $post->delete();

        // Chuyển hướng người dùng về trang "Bài viết của tôi"
        return redirect()
            ->route('posts.my_posts') // Route name của trang danh sách bài viết của tôi
            ->with('success', 'Bài viết đã được xóa thành công!'); // Gửi kèm thông báo thành công
    }
    
} 