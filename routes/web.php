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
use App\Http\Controllers\CommentController;
use App\Http\Controllers\GroupPostFavoriteController;
use App\Http\Controllers\GroupCommentController;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\UserReportController;
use App\Http\Controllers\ReadPostController;

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
Route::middleware(['web'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login'); // Hiển thị form đăng nhập
    Route::post('/login', [AuthController::class, 'login']); // Xử lý dữ liệu đăng nhập
});

// Routes xử lý quên mật khẩu
Route::get('/forgot-password', [PasswordController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password', [PasswordController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [PasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [PasswordController::class, 'reset'])->name('password.update');

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
    Route::get('/profile/friends', [ProfileController::class, 'friends'])->name('profile.friends'); // Hiển thị danh sách bạn bè
    
    // Routes xóa tài khoản
    Route::get('/profile/delete', [ProfileController::class, 'showDeleteAccount'])->name('profile.delete');
    Route::post('/profile/delete', [ProfileController::class, 'deleteAccount'])->name('profile.delete.confirm');
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
    Route::get('/api/users/search', [App\Http\Controllers\Api\UserController::class, 'search'])->name('api.users.search');
    Route::get('/api/groups/search', [GroupController::class, 'searchAjax'])->name('groups.searchAjax');

    // Routes quản lý like/unlike group post
    Route::post('groups/{group}/posts/{groupPost}/like', [GroupPostLikeController::class, 'toggleLike'])->name('groups.posts.like');

    // Routes quản lý like post
    Route::post('/posts/{post}/like', [PostController::class, 'like'])->name('posts.like');

    // Routes quản lý yêu thích bài viết
    Route::post('/posts/{post}/favorites/toggle', [PostController::class, 'toggleFavorite'])->name('posts.favorites.toggle');

    // Routes quản lý bình luận
    Route::get('/posts/{postId}/comments', [CommentController::class, 'index'])->name('comments.index');
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::post('/comments/{comment}/reply', [CommentController::class, 'reply'])->name('comments.reply');
    Route::get('/comments/{comment}/edit', [CommentController::class, 'edit'])->name('comments.edit');
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');

    // Routes quản lý báo cáo bài viết
    Route::post('/posts/{post}/report', [UserReportController::class, 'store'])->name('posts.report');

    // Routes quản lý kết bạn
    Route::post('/users/{user}/add-friend', [UserController::class, 'addFriend'])->name('users.add-friend');
    Route::delete('/users/{user}/remove-friend', [UserController::class, 'removeFriend'])->name('users.remove-friend');
    Route::post('/users/{user}/accept-friend-request', [UserController::class, 'acceptFriendRequest'])->name('users.accept-friend-request');
    Route::post('/users/{user}/reject-friend-request', [UserController::class, 'rejectFriendRequest'])->name('users.reject-friend-request');
    Route::get('/users/{user}/friends', [UserController::class, 'friends'])->name('users.friends');

    // Routes quản lý chặn người dùng
    Route::post('/users/{user}/block', [UserController::class, 'block'])->name('users.block');
    Route::delete('/users/{user}/unblock', [UserController::class, 'unblock'])->name('users.unblock');
    Route::get('/users/blocked', [UserController::class, 'blocked'])->name('users.blocked');

    // Routes quản lý báo cáo người dùng
    Route::post('/users/{user}/report', [UserController::class, 'report'])->name('users.report');
    Route::delete('/users/{user}/unreport', [UserController::class, 'unreport'])->name('users.unreport');

    // Routes cho yêu thích bài viết trong nhóm
    Route::post('group-posts/{post}/favorite', [GroupPostFavoriteController::class, 'toggle'])->name('group-posts.favorites.toggle');

    // Routes quản lý theo dõi người dùng
    Route::post('/users/{user}/follow', [FollowController::class, 'follow'])->name('users.follow');
    Route::post('/users/{user}/unfollow', [FollowController::class, 'unfollow'])->name('users.unfollow');
    Route::get('/users/{user}/followers', [FollowController::class, 'getFollowers'])->name('users.followers');
    Route::get('/users/{user}/following', [FollowController::class, 'getFollowing'])->name('users.following');
    Route::get('/users/{user}/check-follow', [FollowController::class, 'checkFollowStatus'])->name('users.check-follow');
    Route::get('/users/{user}/following-list', [FollowController::class, 'followingList'])->name('users.following-list');

    // Routes quản lý bình luận trong nhóm
    Route::post('/groups/posts/{post}/comments', [GroupCommentController::class, 'store'])->name('groups.posts.comments.store');

    // Routes quản lý bài viết yêu thích của người dùng
    Route::get('/my-favorited-posts', [PostController::class, 'myFavoritedPosts'])->name('posts.my_favorited');

    // Routes quản lý story
    Route::get('/stories', [StoryController::class, 'index'])->name('stories.index');
    Route::get('/stories/create', [StoryController::class, 'create'])->name('stories.create');
    Route::post('/stories', [StoryController::class, 'store'])->name('stories.store');
    Route::get('/stories/{story}', [StoryController::class, 'show'])->name('stories.show');
    Route::delete('/stories/{story}', [StoryController::class, 'destroy'])->name('stories.destroy');

    // Routes quản lý sự kiện
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
    Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
    
    // Event participation routes
    Route::post('/events/{event}/join', [EventController::class, 'join'])->name('events.join');
    Route::post('/events/{event}/leave', [EventController::class, 'leave'])->name('events.leave');

    // Routes quản lý danh sách người đã like bài viết (AJAX)
    Route::get('/posts/{post}/likes-list', [PostController::class, 'likesList'])->name('posts.likesList');

    // Routes quản lý đánh dấu bài viết là đã đọc
    Route::post('/posts/{post}/read', [\App\Http\Controllers\ReadPostController::class, 'markAsRead'])->name('posts.markAsRead');
});

/**
 * PHẦN 4: ROUTES XỬ LÝ ĐỔI MẬT KHẨU
 * Yêu cầu user đã đăng nhập
 */
Route::middleware('auth')->group(function () {
    Route::get('/password/change', [PasswordController::class, 'showChangePasswordForm'])->name('password.change'); // Form đổi mật khẩu
    Route::post('/password/change', [PasswordController::class, 'updatePassword'])->name('password.change'); // Xử lý đổi mật khẩu
});

// Routes cho thông báo
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::delete('/notifications-all', [NotificationController::class, 'clearAll'])->name('notifications.clearAll');
    
    // Routes xử lý friend request
    Route::post('/friends/accept/{user}', [UserController::class, 'acceptFriendRequest'])->name('friends.accept');
    Route::post('/friends/reject/{user}', [UserController::class, 'rejectFriendRequest'])->name('friends.reject');
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

    // Routes quản lý báo cáo
    // Route hiện tại cho báo cáo bài viết
    Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports'); // Danh sách báo cáo bài viết
    Route::delete('/reports/{id}', [AdminController::class, 'deleteReport'])->name('admin.reports.delete'); // Xóa báo cáo bài viết

    // Route mới cho báo cáo người dùng
    Route::get('/user-reports', [AdminController::class, 'userReports'])->name('admin.user-reports'); // Danh sách báo cáo người dùng

    // Routes xử lý hành động trên báo cáo người dùng
    Route::put('/user-reports/{userReport}/mark-resolved', [AdminController::class, 'markUserReportResolved'])->name('admin.user-reports.mark-resolved'); // Đánh dấu báo cáo đã xử lý
    Route::delete('/user-reports/{userReport}', [AdminController::class, 'deleteUserReport'])->name('admin.user-reports.delete'); // Xóa báo cáo người dùng
});
