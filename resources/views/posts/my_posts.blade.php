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
                                        <a href="{{ route('posts.edit', $post) }}" class="btn btn-sm btn-warning ms-2">
                                            <i class="fas fa-edit"></i> Sửa
                                        </a>
                                        <form action="{{ route('posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài viết này?');">
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
            <div class="search-container position-relative mb-4">
                <div class="input-group">
                    <input type="text" 
                           name="q" 
                           class="form-control" 
                           id="groupSearchInput"
                           placeholder="Tìm kiếm nhóm..." 
                           value="{{ request('q') }}"
                           autocomplete="off">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="fas fa-search"></i> Tìm kiếm
                    </button>
                </div>
                <div id="groupSearchResults" class="autocomplete-results position-absolute w-100 mt-1 d-none"></div>
            </div>
            @php
                $q = request('q');
                $groups = \App\Models\Group::withCount('members')->with(['members'])
                    ->when($q, function($query) use ($q) {
                        $query->where(function($sub) use ($q) {
                            $sub->where('name', 'like', "%$q%")
                                 ->orWhere('description', 'like', "%$q%") ;
                        });
                    })
                    ->latest()->get();
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
    .search-container {
        z-index: 1000;
    }
    .autocomplete-results {
        background: white;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        max-height: 300px;
        overflow-y: auto;
    }
    .autocomplete-results .list-group-item {
        border-left: none;
        border-right: none;
        cursor: pointer;
    }
    .autocomplete-results .list-group-item:first-child {
        border-top: none;
    }
    .autocomplete-results .list-group-item:last-child {
        border-bottom: none;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('groupSearchInput');
    const searchResults = document.getElementById('groupSearchResults');
    let searchTimeout;

    if (searchInput && searchResults) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();

            if (query.length < 2) {
                searchResults.innerHTML = '';
                searchResults.classList.add('d-none');
                return;
            }

            searchTimeout = setTimeout(() => {
                fetch(`/api/groups/search?q=${encodeURIComponent(query)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    searchResults.innerHTML = '';
                    
                    if (data.length === 0) {
                        searchResults.innerHTML = `
                            <div class="list-group-item text-center text-muted">
                                Không tìm thấy nhóm nào
                            </div>`;
                    } else {
                        data.forEach(group => {
                            const item = document.createElement('a');
                            item.href = `/groups/${group.id}`;
                            item.className = 'list-group-item list-group-item-action';
                            item.innerHTML = `
                                <div class="d-flex align-items-center">
                                    <img src="${group.avatar || '/images/default-avatar.jpg'}" 
                                         class="rounded-circle me-2" 
                                         style="width: 32px; height: 32px; object-fit: cover;">
                                    <div>
                                        <div class="fw-bold">${group.name}</div>
                                        <small class="text-muted">${group.members_count || 0} thành viên</small>
                                    </div>
                                </div>`;
                            searchResults.appendChild(item);
                        });
                    }
                    searchResults.classList.remove('d-none');
                })
                .catch(error => {
                    console.error('Error:', error);
                    searchResults.innerHTML = `
                        <div class="list-group-item text-center text-danger">
                            Đã có lỗi xảy ra khi tìm kiếm
                        </div>`;
                    searchResults.classList.remove('d-none');
                });
            }, 300);
        });

        // Ẩn kết quả khi click ra ngoài
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.classList.add('d-none');
            }
        });

        // Hiện lại kết quả khi focus vào input
        searchInput.addEventListener('focus', function() {
            if (this.value.trim().length >= 2) {
                searchResults.classList.remove('d-none');
            }
        });
    }
});

document.addEventListener('DOMContentLoaded', function() {
    // Xử lý sự kiện like
    document.querySelectorAll('.like-button').forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.dataset.postId;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            fetch(`/posts/${postId}/like`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                const heartIcon = this.querySelector('i');
                const likeCount = this.querySelector('.like-count');
                
                if (data.liked) {
                    this.classList.add('active');
                    heartIcon.classList.add('text-danger');
                    heartIcon.classList.remove('text-muted');
                } else {
                    this.classList.remove('active');
                    heartIcon.classList.remove('text-danger');
                    heartIcon.classList.add('text-muted');
                }
                
                if (likeCount) {
                    likeCount.textContent = data.likesCount;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi thực hiện thao tác like');
            });
        });
    });
});
</script>
@endpush
@endsection