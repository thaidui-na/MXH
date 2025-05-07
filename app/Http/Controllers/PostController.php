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
     * Hiển thị form để tạo một bài viết mới.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Chỉ đơn giản trả về view 'posts.create' chứa form nhập liệu
        return view('posts.create');
    }
} 