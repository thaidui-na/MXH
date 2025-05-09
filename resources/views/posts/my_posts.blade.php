@extends('layouts.app')

{{-- Tiêu đề trang --}}
@section('title', 'Trang cá nhân của ' . auth()->user()->name)

@section('content')
<div class="container py-4">
    {{-- Card thông tin người dùng --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                {{-- Cột bên trái: Avatar và thông tin cơ bản --}}
                <div class="col-md-3 text-center">
                    <img src="{{ auth()->user()->avatar_url }}"
                        class="rounded-circle img-thumbnail mb-3"
                        style="width: 150px; height: 150px; object-fit: cover;"
                        alt="{{ auth()->user()->name }}'s avatar">
                    <h4 class="mb-0">{{ auth()->user()->name }}</h4>
                    @if(auth()->user()->email)
                    <p class="text-muted mb-2">
                        <i class="fas fa-envelope me-2"></i>{{ auth()->user()->email }}
                    </p>
                    @endif
                    @if(auth()->user()->phone)
                    <p class="text-muted mb-2">
                        <i class="fas fa-phone me-2"></i>{{ auth()->user()->phone }}
                    </p>
                    @endif
                    @if(auth()->user()->birthday)
                    <p class="text-muted mb-0">
                        <i class="fas fa-birthday-cake me-2"></i>
                        {{ auth()->user()->birthday->format('d/m/Y') }}
                    </p>
                    @endif
                </div>

                {{-- Cột bên phải: Giới thiệu và thống kê --}}
                <div class="col-md-9">
                    {{-- Phần giới thiệu nếu có --}}
                    @if(auth()->user()->bio)
                    <div class="mb-4">
                        <h5 class="text-muted mb-3">Giới thiệu</h5>
                        <p class="mb-0">{{ auth()->user()->bio }}</p>
                    </div>
                    @endif

                    {{-- Thống kê hoạt động --}}
                    <div class="row stats-container">
                        {{-- Tổng số bài viết --}}
                        <div class="col-md-6">
                            <div class="stats-item text-center p-3">
                                <h3 class="mb-0">{{ auth()->user()->posts()->count() }}</h3>
                                <p class="text-muted mb-0">Tổng bài viết</p>
                            </div>
                        </div>
                        {{-- Ngày tham gia --}}
                        <div class="col-md-6">
                            <div class="stats-item text-center p-3">
                                <h3 class="mb-0">{{ auth()->user()->created_at->format('d/m/Y') }}</h3>
                                <p class="text-muted mb-0">Ngày tham gia</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Phần danh sách bài viết --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Tất cả bài viết</h4>
        <a href="{{ route('posts.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tạo bài viết mới
        </a>
    </div>

    {{-- Hiển thị thông báo thành công nếu có --}}
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    {{-- Danh sách bài viết --}}
    @if($posts->count() > 0)
    <div class="row">
        @foreach($posts as $post)
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h5 class="card-title">{{ $post->title }}</h5>
                    </div>
                    <p class="card-text text-muted small">
                        Đăng ngày {{ $post->created_at->format('d/m/Y H:i') }}
                    </p>
                    <p class="card-text">{{ Str::limit($post->content, 200) }}</p>
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('posts.show', $post) }}" class="btn btn-sm btn-info me-2">
                            <i class="fas fa-eye"></i> Xem
                        </a>
                        <a href="{{ route('posts.edit', $post) }}" class="btn btn-sm btn-warning me-2">
                            <i class="fas fa-edit"></i> Sửa
                        </a>
                        <form action="{{ route('posts.destroy', $post) }}" method="POST"
                            onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài viết này?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i> Xóa
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Phân trang --}}
    <div class="d-flex justify-content-center mt-4">
        {{ $posts->links() }}
    </div>
    @else
    <div class="alert alert-info">
        Bạn chưa có bài viết nào. <a href="{{ route('posts.create') }}">Tạo bài viết đầu tiên</a>
    </div>
    @endif
</div>

{{-- CSS tùy chỉnh cho phần thống kê --}}
@push('styles')
<style>
    .stats-item {
        background-color: #f8f9fa;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .stats-item:hover {
        background-color: #e9ecef;
    }
</style>
@endpush
@endsection