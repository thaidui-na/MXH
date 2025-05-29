<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Report;
use App\Models\Story;
use App\Models\ReadPost;

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
        $posts = Post::with(['user', 'likes'])
            ->where('is_public', true)
            ->orderByFavorites()
            ->paginate(10);

        $groups = auth()->user()->joinedGroups()
            ->withCount('members')
            ->get();

        // Lấy stories của tất cả người dùng
        $stories = Story::with('user')
            ->active()
            ->latest()
            ->get()
            ->groupBy('user_id');

        // Lấy danh sách id các bài viết đã đọc của user hiện tại
        $readPostIds = ReadPost::where('user_id', auth()->id())->pluck('post_id')->toArray();

        return view('posts.index', compact('posts', 'groups', 'stories', 'readPostIds'));
    }

    /**
     * Hiển thị danh sách các bài viết của người dùng đang đăng nhập.
     * Phân trang kết quả.
     *
     * @return \Illuminate\View\View
     */
    public function myPosts()
    {
        // Lấy người dùng đang đăng nhập
        $user = auth()->user();
        
        // Lấy các bài viết thuộc về người dùng đang đăng nhập
        $posts = $user->posts() // Truy cập relationship 'posts' đã định nghĩa trong User model
                    ->latest() // Sắp xếp theo thứ tự mới nhất lên đầu
                    ->paginate(10); // Phân trang kết quả, mỗi trang 10 bài viết

        // Trả về view 'posts.my_posts' và truyền biến 'posts' và 'user' vào view
        return view('posts.my_posts', compact('posts', 'user'));
    }

    /**
     * Hiển thị danh sách các bài viết của một người dùng khác.
     * Phân trang kết quả.
     *
     * @param  \App\Models\User  $user Đối tượng User cần xem bài viết
     * @return \Illuminate\View\View
     */
    public function userPosts(User $user)
    {
        // Lấy các bài viết công khai của người dùng được chỉ định
        $posts = $user->posts()
                    ->where('is_public', true) // Chỉ lấy bài viết công khai
                    ->latest()
                    ->paginate(10);

        // Trả về view 'posts.my_posts' và truyền biến 'posts' và 'user' vào view
        return view('posts.my_posts', compact('posts', 'user'));
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
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề bài viết',
            'title.string' => 'Tiêu đề phải là chuỗi ký tự',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự',
            'content.required' => 'Vui lòng nhập nội dung bài viết',
            'content.string' => 'Nội dung phải là chuỗi ký tự',
        ]);

        // Thêm user_id và is_public
        $validated['user_id'] = auth()->id();
        $validated['is_public'] = true;

        // Tạo bài viết mới
        Post::create($validated);

        return redirect()
            ->route('posts.my_posts')
            ->with('success', 'Bài viết đã được đăng thành công!');
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
        // Kiểm tra quyền chỉnh sửa
        if ($post->user_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền chỉnh sửa bài viết này.');
        }

        // Validate dữ liệu gửi lên từ form
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
                'regex:/^[^\s　]*[^\s　]+[^\s　]*$/',
            ],
            'content' => [
                'required',
                'string',
                'max:10000',
                'regex:/^\S.*\S$|^[\S]$/',
            ],
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề bài viết',
            'title.string' => 'Tiêu đề phải là chuỗi ký tự',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự',
            'title.regex' => 'Tiêu đề không được chứa toàn khoảng trắng',
            'content.required' => 'Vui lòng nhập nội dung bài viết',
            'content.string' => 'Nội dung phải là chuỗi ký tự',
            'content.max' => 'Nội dung không được vượt quá 10000 ký tự',
            'content.regex' => 'Nội dung không được bắt đầu hoặc kết thúc bằng khoảng trắng.',
        ]);

        // Gán lại giá trị is_public
        $validated['is_public'] = true;

        // Cập nhật bản ghi bài viết
        $post->update($validated);

        return redirect()
            ->route('posts.show', $post)
            ->with('success', 'Bài viết đã được cập nhật thành công!');
    }

    /**
     * Xóa một bài viết khỏi database.
     * Sử dụng Route Model Binding.
     * Kiểm tra quyền xóa (chỉ tác giả mới được xóa).
     *
     * @param  \App\Models\Post  $post Đối tượng Post cần xóa
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Post $post)
    {
        try {
            // Kiểm tra quyền xóa
            if ($post->user_id !== auth()->id()) {
                abort(403, 'Bạn không có quyền xóa bài viết này.');
            }

            // Thực hiện xóa bản ghi bài viết
            $post->delete();

            return redirect()
                ->route('posts.my_posts')
                ->with('success', 'Bài viết đã được xóa thành công!');
        } catch (\Exception $e) {
            return redirect()
                ->route('posts.my_posts')
                ->with('error', 'Không thể xóa bài viết. Bài viết có thể đã bị xóa hoặc không tồn tại.');
        }
    }

    /**
     * Toggle like status for a post
     */
    public function like(Post $post)
    {
        $user = auth()->user();
        if ($post->isLikedBy($user->id)) {
            $post->likes()->detach($user->id);
            $liked = false;
        } else {
            $post->likes()->attach($user->id);
            $liked = true;
            
            // Gửi thông báo cho chủ bài viết nếu người like không phải là chủ bài viết
            if ($post->user_id !== $user->id) {
                $post->user->notify(new \App\Notifications\PostLikeNotification($user, $post));
            }
        }
        return response()->json([
            'liked' => $liked,
            'likesCount' => $post->getLikesCount()
        ]);
    }

    public function report(Request $request, Post $post)
    {
        $request->validate([
            'reason' => 'required|string',
            'other_reason' => 'required_if:reason,other|string|max:500'
        ]);

        $reason = $request->input('reason');
        if ($reason === 'other') {
            $reason = $request->input('other_reason');
        }
        
        // Check if user has already reported this post
        $existingReport = Report::where('post_id', $post->id)
            ->where('user_id', auth()->id())
            ->first();

        if ($existingReport) {
            return redirect()->back()->with('error', 'Bạn đã báo cáo bài viết này trước đó.');
        }
        
        // Create new report
        Report::create([
            'post_id' => $post->id,
            'user_id' => auth()->id(),
            'reason' => $reason
        ]);
        
        return redirect()->back()->with('success', 'Cảm ơn bạn đã báo cáo. Chúng tôi sẽ xem xét nội dung này trong thời gian sớm nhất.');
    }

    public function toggleFavorite(Post $post)
    {
        $user = auth()->user();
        
        if ($post->isFavoritedBy($user->id)) {
            // Nếu đã favorite thì xóa
            $post->favorites()->where('user_id', $user->id)->delete();
            $message = 'Đã xóa khỏi danh sách yêu thích';
        } else {
            // Nếu chưa favorite thì thêm mới
            $post->favorites()->create([
                'user_id' => $user->id
            ]);
            $message = 'Đã thêm vào danh sách yêu thích';
        }

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'isFavorited' => !$post->isFavoritedBy($user->id)
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Hiển thị danh sách các bài viết mà người dùng hiện tại đã yêu thích.
     *
     * @return \Illuminate\View\View
     */
    public function myFavoritedPosts()
    {
        $user = auth()->user();

        // Lấy các bài viết yêu thích của người dùng hiện tại, kèm thông tin người đăng
        $favoritedPosts = $user->favoritedPosts()->with('user')->latest()->paginate(10);

        // Trả về view, truyền kèm danh sách bài viết yêu thích và đối tượng user
        return view('posts.my_favorited_posts', compact('favoritedPosts', 'user'));
    }

    /**
     * Trả về HTML danh sách người đã like bài viết (AJAX)
     */
    public function likesList(Post $post)
    {
        return view('posts.partials.likes_list', ['users' => $post->likes]);
    }
} 
