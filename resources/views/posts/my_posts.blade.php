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
                    <img src="{{ auth()->user()->avatar ? asset('images/' . auth()->user()->avatar) : asset('images/default-avatar.jpg') }}"
                         onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.jpg') }}';"
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

    {{-- Tabs chuyển đổi --}}
    <ul class="nav nav-tabs mb-4" id="profileTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="posts-tab" data-bs-toggle="tab" data-bs-target="#posts-pane" type="button" role="tab" aria-controls="posts-pane" aria-selected="true">
                <i class="fas fa-newspaper"></i> Bài viết
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="groups-tab" data-bs-toggle="tab" data-bs-target="#groups-pane" type="button" role="tab" aria-controls="groups-pane" aria-selected="false">
                <i class="fas fa-users"></i> Nhóm
            </button>
        </li>
    </ul>
    <div class="tab-content" id="profileTabContent">
        {{-- Tab Bài viết --}}
        <div class="tab-pane fade show active" id="posts-pane" role="tabpanel" aria-labelledby="posts-tab">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>Tất cả bài viết</h4>
                <div>
                    <a href="{{ route('groups.create') }}" class="btn btn-success me-2">
                        <i class="fas fa-users"></i> Tạo nhóm
                    </a>
                    <a href="{{ route('posts.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tạo bài viết mới
                    </a>
                </div>
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
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0">{{ $post->title }}</h5>
                                        <button class="btn btn-sm btn-outline-danger like-button {{ $post->isLikedBy(auth()->id()) ? 'active' : '' }}"
                                            data-post-id="{{ $post->id }}">
                                            <i class="fas fa-heart {{ $post->isLikedBy(auth()->id()) ? 'text-danger' : 'text-muted' }}"></i>
                                            <span class="like-count ms-1">{{ $post->getLikesCount() }}</span>
                                        </button>
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
                                        <form action="{{ route('posts.destroy', $post) }}" method="POST" class="d-inline"
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
                <div class="d-flex justify-content-center mt-4">
                    {{ $posts->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    Bạn chưa có bài viết nào. <a href="{{ route('posts.create') }}">Tạo bài viết đầu tiên</a>
                </div>
            @endif
        </div>
        {{-- Tab Nhóm --}}
        <div class="tab-pane fade" id="groups-pane" role="tabpanel" aria-labelledby="groups-tab">
            <form method="GET" action="{{ route('groups.search') }}" class="mb-4">
                <div class="input-group">
                    <input type="text" name="q" class="form-control" placeholder="Tìm kiếm nhóm..." value="{{ request('q') }}">
                    <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i> Tìm kiếm</button>
                </div>
            </form>
            @php
                $q = request('q');
                $groups = \App\Models\Group::withCount('members')
                    ->with(['members'])
                    ->when($q, function($query) use ($q) {
                        $query->where(function($sub) use ($q) {
                            $sub->where('name', 'like', "%$q%")
                                ->orWhere('description', 'like', "%$q%");
                        });
                    })
                    ->latest()
                    ->paginate(9);
                $joinedGroupIds = auth()->user()->joinedGroups()->pluck('groups.id')->toArray();
            @endphp
            @if($groups->count() > 0)
                <h5 class="mb-3">Kết quả nhóm</h5>
                <div class="row mb-4">
                    @foreach($groups as $group)
                        <div class="col-md-4">
                            <div class="group-card">
                                <img src="{{ $group->cover_image ? asset('storage/' . $group->cover_image) : asset('images/default-cover.jpg') }}"
                                     onerror="this.onerror=null;this.src='{{ asset('images/default-cover.jpg') }}';"
                                     class="group-cover" alt="Cover">
                                <img src="{{ $group->avatar ? asset('storage/' . $group->avatar) : asset('images/default-avatar.jpg') }}"
                                     onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.jpg') }}';"
                                     class="group-avatar" alt="Avatar">
                                <div class="group-info">
                                    <h5 class="group-title">{{ $group->name }}</h5>
                                    <p class="group-description">{{ Str::limit($group->description, 100) }}</p>
                                    <div class="group-meta">
                                        <span>
                                            <i class="fas fa-users"></i> {{ $group->members_count }} thành viên
                                        </span>
                                        <span class="badge {{ $group->is_private ? 'bg-secondary' : 'bg-success' }} group-badge">
                                            {{ $group->is_private ? 'Riêng tư' : 'Công khai' }}
                                        </span>
                                    </div>
                                    <div class="d-grid mt-3">
                                        <a href="{{ route('groups.show', $group) }}" class="btn btn-outline-primary mb-2">
                                            Xem chi tiết
                                        </a>
                                        @if(!in_array($group->id, $joinedGroupIds))
                                            <form method="POST" action="{{ route('groups.join', $group->id) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-success">Tham gia nhóm</button>
                                            </form>
                                        @else
                                            <button class="btn btn-secondary" disabled>Đã tham gia</button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-info">
                    Không tìm thấy nhóm nào phù hợp. <a href="{{ route('groups.create') }}">Tạo nhóm mới</a>
                </div>
            @endif
        </div>
    </div>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Like button functionality
    document.querySelectorAll('.like-button').forEach(button => {
        let isProcessing = false; // Flag to prevent double clicks
        
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Prevent double clicks
            if (isProcessing) {
                console.log('Like action is already in progress');
                return;
            }
            
            isProcessing = true;
            const postId = this.dataset.postId;
            const icon = this.querySelector('i');
            const countSpan = this.querySelector('.like-count');
            
            console.log('Like button clicked for post:', postId);
            
            // Disable button while processing
            this.disabled = true;
            
            fetch(`/posts/${postId}/like`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.liked) {
                    icon.classList.remove('text-muted');
                    icon.classList.add('text-danger');
                    this.classList.add('active');
                } else {
                    icon.classList.remove('text-danger');
                    icon.classList.add('text-muted');
                    this.classList.remove('active');
                }
                countSpan.textContent = data.likesCount;
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi thích bài viết. Vui lòng thử lại sau.');
            })
            .finally(() => {
                // Re-enable button and reset processing flag
                this.disabled = false;
                isProcessing = false;
            });
        });
    });
});
</script>
@endpush
@endsection