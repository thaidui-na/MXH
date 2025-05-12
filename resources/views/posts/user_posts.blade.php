@extends('layouts.app')

{{-- Tiêu đề trang --}}
@section('title', 'Trang cá nhân của ' . $user->name)

@section('content')
<div class="container py-4">
    {{-- Card thông tin người dùng --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                {{-- Cột bên trái: Avatar và thông tin cơ bản --}}
                <div class="col-md-3 text-center">
                    <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('images/default-avatar.jpg') }}"
                         onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.jpg') }}';"
                         class="rounded-circle img-thumbnail mb-3"
                         style="width: 150px; height: 150px; object-fit: cover;"
                         alt="{{ $user->name }}'s avatar">
                    <h4 class="mb-0">{{ $user->name }}</h4>
                    @if($user->email)
                        <p class="text-muted mb-2">
                            <i class="fas fa-envelope me-2"></i>{{ $user->email }}
                        </p>
                    @endif
                    @if($user->phone)
                        <p class="text-muted mb-2">
                            <i class="fas fa-phone me-2"></i>{{ $user->phone }}
                        </p>
                    @endif
                    @if($user->birthday)
                        <p class="text-muted mb-0">
                            <i class="fas fa-birthday-cake me-2"></i>
                            {{ $user->birthday->format('d/m/Y') }}
                        </p>
                    @endif
                </div>
                
                {{-- Cột bên phải: Giới thiệu và thống kê --}}
                <div class="col-md-9">
                    {{-- Phần giới thiệu nếu có --}}
                    @if($user->bio)
                        <div class="mb-4">
                            <h5 class="text-muted mb-3">Giới thiệu</h5>
                            <p class="mb-0">{{ $user->bio }}</p>
                        </div>
                    @endif
                    
                    {{-- Thống kê hoạt động --}}
                    <div class="row stats-container">
                        {{-- Tổng số bài viết --}}
                        <div class="col-md-6">
                            <div class="stats-item text-center p-3">
                                <h3 class="mb-0">{{ $user->posts()->count() }}</h3>
                                <p class="text-muted mb-0">Tổng bài viết</p>
                            </div>
                        </div>
                        {{-- Ngày tham gia --}}
                        <div class="col-md-6">
                            <div class="stats-item text-center p-3">
                                <h3 class="mb-0">{{ $user->created_at->format('d/m/Y') }}</h3>
                                <p class="text-muted mb-0">Ngày tham gia</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Danh sách bài viết --}}
    <div class="row">
        @if($posts->count() > 0)
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
                                <button class="btn btn-sm like-button {{ $post->isLikedBy(auth()->id()) ? 'active' : '' }}"
                                    data-post-id="{{ $post->id }}">
                                    <i class="fas fa-heart {{ $post->isLikedBy(auth()->id()) ? 'text-danger' : '' }}"></i>
                                    <span class="like-count">{{ $post->getLikesCount() }}</span>
                                </button>
                                <a href="{{ route('posts.show', $post) }}" class="btn btn-sm btn-info ms-2">
                                    <i class="fas fa-eye"></i> Xem
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            <div class="d-flex justify-content-center mt-4">
                {{ $posts->links() }}
            </div>
        @else
            <div class="col-12">
                <div class="alert alert-info">
                    @if($user->id === auth()->id())
                        Bạn chưa có bài viết nào. <a href="{{ route('posts.create') }}">Tạo bài viết đầu tiên</a>
                    @else
                        Người dùng này chưa có bài viết công khai nào.
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.like-button').forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.dataset.postId;
            fetch(`/posts/${postId}/like`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.liked) {
                    this.classList.add('active');
                    this.querySelector('i').classList.add('text-danger');
                    this.querySelector('i').classList.remove('text-muted');
                } else {
                    this.classList.remove('active');
                    this.querySelector('i').classList.remove('text-danger');
                    this.querySelector('i').classList.add('text-muted');
                }
                // Cập nhật số lượng like
                const likeCount = this.querySelector('.like-count');
                if (likeCount) {
                    likeCount.textContent = data.likesCount;
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    });
});
</script>
@endpush 