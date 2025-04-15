<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ChatGroupController;
use App\Http\Controllers\GroupMessageController;
use App\Http\Controllers\Admin\AdminController;

Route::get('/', function () {
    return view('welcome');
});

// Routes cho đăng ký
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Routes cho đăng nhập
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Route cho đăng xuất
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Routes cho profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

// Routes cho bài viết
Route::middleware('auth')->group(function () {
    // Danh sách tất cả bài viết
    Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
    
    // Danh sách bài viết của tôi
    Route::get('/my-posts', [PostController::class, 'myPosts'])->name('posts.my_posts');
    
    // CRUD cho posts
    Route::resource('posts', PostController::class)->except(['index']);

    // Routes cho tin nhắn
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{user}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/{user}/new', [MessageController::class, 'getNewMessages'])->name('messages.new');
    Route::get('/messages/users/status', [MessageController::class, 'getUsersStatus'])->name('messages.users.status');
});

// Routes cho nhóm chat
Route::middleware(['web', 'auth'])->group(function () {
    // Chat groups
    Route::resource('chat-groups', ChatGroupController::class);
    Route::post('chat-groups/{group}/messages', [GroupMessageController::class, 'store'])
         ->name('group.messages.store');
    Route::get('chat-groups/{group}/messages/check', [GroupMessageController::class, 'checkNewMessages'])
         ->name('group.messages.check');
});

// Cập nhật route dashboard
Route::get('/dashboard', function () {
    return redirect()->route('posts.index');
})->middleware(['auth'])->name('dashboard');

// Routes cho trang admin
Route::middleware(['web', 'auth', \App\Http\Middleware\AdminMiddleware::class])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');
    
    // Users routes
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    
    // Posts routes
    Route::get('/posts', [AdminController::class, 'posts'])->name('admin.posts');
    Route::get('/posts/{id}/edit', [AdminController::class, 'editPost'])->name('admin.posts.edit');
    Route::put('/posts/{id}', [AdminController::class, 'updatePost'])->name('admin.posts.update');
    Route::delete('/posts/{id}', [AdminController::class, 'deletePost'])->name('admin.posts.delete');
});
