<?php

// Import các class Controller cần thiết
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ChatGroupController;
use App\Http\Controllers\GroupMessageController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GroupPostLikeController;
use App\Http\Controllers\GroupPostController;

// Route mặc định, hiển thị trang chào mừng
Route::get('/', function () {
    return view('welcome');
});

/**
 * PHẦN 1: ROUTES XỬ LÝ AUTHENTICATION
 */

// Routes xử lý đăng ký tài khoản
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register'); // Hiển thị form đăng ký
Route::post('/register', [AuthController::class, 'register']); // Xử lý dữ liệu đăng ký

// Routes xử lý đăng nhập
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login'); // Hiển thị form đăng nhập
Route::post('/login', [AuthController::class, 'login']); // Xử lý dữ liệu đăng nhập

// Route xử lý đăng xuất - yêu cầu phương thức POST để tránh CSRF
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');



/**
 * PHẦN 2: ROUTES XỬ LÝ PROFILE
 * Tất cả routes trong group này đều yêu cầu user đã đăng nhập (middleware 'auth')
 */
Route::middleware('auth')->group(function () {
    // Routes quản lý thông tin cá nhân
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit'); // Hiển thị form chỉnh sửa profile
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update'); // Cập nhật thông tin profile
});

/**
 * PHẦN 3: ROUTES XỬ LÝ POSTS VÀ MESSAGES
 * Tất cả routes trong group này đều yêu cầu user đã đăng nhập
 */
Route::middleware('auth')->group(function () {
    // Routes quản lý bài viết
    Route::get('/posts', [PostController::class, 'index'])->name('posts.index'); // Hiển thị tất cả bài viết
    Route::get('/my-posts', [PostController::class, 'myPosts'])->name('posts.my_posts'); // Hiển thị bài viết của user hiện tại
    Route::get('/posts/my_posts/{user}', [PostController::class, 'userPosts'])->name('posts.user_posts'); // Thêm route mới

    // Tạo các routes CRUD cho posts (trừ index đã định nghĩa ở trên)
    // Tự động tạo các routes: show, create, store, edit, update, destroy
    Route::resource('posts', PostController::class)->except(['index']);

    // Routes quản lý tin nhắn cá nhân
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index'); // Danh sách chat
    Route::get('/messages/{user}', [MessageController::class, 'show'])->name('messages.show'); // Xem cuộc trò chuyện với một user
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store'); // Gửi tin nhắn mới
    Route::get('/messages/{user}/new', [MessageController::class, 'getNewMessages'])->name('messages.new'); // Kiểm tra tin nhắn mới
    Route::get('/messages/users/status', [MessageController::class, 'getUsersStatus'])->name('messages.users.status'); // Kiểm tra trạng thái online

    // Routes quản lý nhóm chat
    Route::resource('chat-groups', ChatGroupController::class); // Tạo đầy đủ CRUD routes cho nhóm chat
    // Routes xử lý tin nhắn trong nhóm
    Route::post('chat-groups/{group}/messages', [GroupMessageController::class, 'store'])
        ->name('group.messages.store'); // Gửi tin nhắn vào nhóm
    Route::get('chat-groups/{group}/messages/check', [GroupMessageController::class, 'checkNewMessages'])
        ->name('group.messages.check'); // Kiểm tra tin nhắn mới trong nhóm

    // Routes quản lý nhóm
    Route::resource('groups', GroupController::class);
    Route::resource('groups.posts', GroupPostController::class);
    Route::post('groups/{group}/join', [GroupController::class, 'join'])->name('groups.join');
    Route::post('groups/{group}/leave', [GroupController::class, 'leave'])->name('groups.leave');
    Route::post('groups/{group}/post', [GroupController::class, 'post'])->name('groups.post');
   
    // Routes quản lý thành viên nhóm
    Route::get('groups/{group}/members', [GroupController::class, 'members'])->name('groups.members');
    Route::put('groups/{group}/members/{member}', [GroupController::class, 'updateMember'])->name('groups.update-member');
    Route::delete('groups/{group}/members/{member}', [GroupController::class, 'removeMember'])->name('groups.remove-member');
    Route::post('groups/{group}/add-members', [GroupController::class, 'addMembers'])->name('groups.add-members');

    // API tìm kiếm
    Route::get('/users/search', [UserController::class, 'searchAjax'])->name('users.search');
    Route::get('/api/groups/search', [GroupController::class, 'searchAjax'])->name('groups.searchAjax');

    // Routes quản lý like/unlike group post
    Route::post('groups/{group}/posts/{groupPost}/like', [GroupPostLikeController::class, 'toggleLike'])->name('groups.posts.like');

    // Routes quản lý like post
    Route::post('/posts/{post}/like', [PostController::class, 'like'])->name('posts.like');
});

/**
 * PHẦN 4: ROUTES XỬ LÝ ĐỔI MẬT KHẨU
 * Yêu cầu user đã đăng nhập
 */
Route::middleware('auth')->group(function () {
    Route::get('/password/change', [PasswordController::class, 'showChangePasswordForm'])->name('password.change'); // Form đổi mật khẩu
    Route::put('/password/change', [PasswordController::class, 'updatePassword'])->name('password.update'); // Xử lý đổi mật khẩu
});




// Route dashboard - Chuyển hướng đến trang danh sách bài viết
Route::get('/dashboard', function () {
    return redirect()->route('posts.index');
})->middleware(['auth'])->name('dashboard');

/**
 * PHẦN 5: ROUTES DÀNH CHO ADMIN
 * Yêu cầu:
 * - User đã đăng nhập (auth)
 * - User có quyền admin (AdminMiddleware)
 * - Prefix tất cả routes với 'admin'
 */
Route::middleware(['web', 'auth', \App\Http\Middleware\AdminMiddleware::class])->prefix('admin')->group(function () {
    // Trang dashboard của admin
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');

    // Routes quản lý users (CRUD)
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users'); // Danh sách users
    Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit'); // Form sửa user
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update'); // Cập nhật user
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete'); // Xóa user

    // Routes quản lý posts (CRUD)
    Route::get('/posts', [AdminController::class, 'posts'])->name('admin.posts'); // Danh sách posts
    Route::get('/posts/{id}/edit', [AdminController::class, 'editPost'])->name('admin.posts.edit'); // Form sửa post
    Route::put('/posts/{id}', [AdminController::class, 'updatePost'])->name('admin.posts.update'); // Cập nhật post
    Route::delete('/posts/{id}', [AdminController::class, 'deletePost'])->name('admin.posts.delete'); // Xóa post
});
