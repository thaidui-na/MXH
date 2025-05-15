@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- Thông tin người dùng -->
        <div class="col-md-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <!-- Avatar và thông tin cơ bản -->
                        <div class="col-md-6 d-flex align-items-center">
                            <img src="{{ $user->avatar_url }}" 
                                 alt="{{ $user->name }}" 
                                 class="rounded-circle me-3"
                                 style="width: 64px; height: 64px; object-fit: cover;">
                            <div>
                                <h4 class="mb-1">{{ $user->name }}</h4>
                                <p class="text-muted mb-0">{{ $user->email }}</p>
                                @if(auth()->id() !== $user->id)
                                    <div class="mt-2">
                                        <button class="btn btn-sm {{ auth()->user()->isFollowing($user) ? 'btn-primary' : 'btn-outline-primary' }} friend-button"
                                                data-user-id="{{ $user->id }}"
                                                onclick="toggleFriend({{ $user->id }}, this)">
                                            <i class="fas fa-{{ auth()->user()->isFollowing($user) ? 'user-friends' : 'user-plus' }}"></i>
                                            {{ auth()->user()->isFollowing($user) ? 'Bạn bè' : 'Kết bạn' }}
                                        </button>

                                        <a href="{{ route('messages.show', $user->id) }}" class="btn btn-sm btn-outline-info ms-2">
                                            <i class="fas fa-comment"></i> Nhắn tin
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Thống kê và menu actions -->
                        <div class="col-md-6">
                            <div class="row">
                                <!-- Số bài viết -->
                                <div class="col-md-6">
                                    <div class="stats-item text-center p-3">
                                        <h3 class="mb-0">{{ $user->posts->count() }}</h3>
                                        <p class="text-muted mb-0">Bài viết</p>
                                    </div>
                                </div>

                                <!-- Ngày tham gia và menu 3 chấm -->
                                <div class="col-md-6">
                                    <div class="stats-item text-center p-3 d-flex align-items-center justify-content-between">
                                        <div class="text-center flex-grow-1">
                                            <h3 class="mb-0">{{ $user->created_at->format('d/m/Y') }}</h3>
                                            <p class="text-muted mb-0">Ngày tham gia</p>
                                        </div>
                                        @include('components.user-actions-menu', ['user' => $user])
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Danh sách bài viết -->
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Bài viết của {{ $user->name }}</h5>
                </div>
                <div class="card-body">
                    @if($posts->count() > 0)
                        @foreach($posts as $post)
                            <div class="post-item mb-4">
                                <h5>{{ $post->title }}</h5>
                                <p>{{ Str::limit($post->content, 200) }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">{{ $post->created_at->format('d/m/Y H:i') }}</small>
                                    <a href="{{ route('posts.show', $post) }}" class="btn btn-sm btn-primary">Xem chi tiết</a>
                                </div>
                            </div>
                            @if(!$loop->last)
                                <hr>
                            @endif
                        @endforeach

                        <div class="mt-4">
                            {{ $posts->links() }}
                        </div>
                    @else
                        <p class="text-center text-muted my-4">Người dùng chưa có bài viết nào.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleFriend(userId, button) {
    const isFollowing = button.classList.contains('btn-primary');
    const url = isFollowing ? `/users/${userId}/remove-friend` : `/users/${userId}/add-friend`;
    const method = isFollowing ? 'DELETE' : 'POST';

    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (isFollowing) {
                button.classList.remove('btn-primary');
                button.classList.add('btn-outline-primary');
                button.innerHTML = '<i class="fas fa-user-plus"></i> Kết bạn';
            } else {
                button.classList.remove('btn-outline-primary');
                button.classList.add('btn-primary');
                button.innerHTML = '<i class="fas fa-user-friends"></i> Bạn bè';
            }
        } else {
            alert(data.error || 'Có lỗi xảy ra');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra');
    });
}
</script>
@endpush
@endsection