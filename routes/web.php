<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\MessageController;

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

// Cập nhật route dashboard
Route::get('/dashboard', function () {
    return redirect()->route('posts.index');
})->middleware(['auth'])->name('dashboard');
