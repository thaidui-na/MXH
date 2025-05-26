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
                    <img src="{{ $user->avatar_url }}" 
                         alt="{{ $user->name }}" 
                         class="rounded-circle mb-3"
                         style="width: 120px; height: 120px; object-fit: cover;">
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
                    @if(auth()->id() !== $user->id)
                        <div class="mt-3 d-flex gap-2">
                            <button class="btn {{ auth()->user()->isFollowing($user) ? 'btn-primary' : 'btn-outline-primary' }} follow-button" 
                                    data-user-id="{{ $user->id }}">
                                <i class="fas fa-user-plus"></i> 
                                <span class="follow-text">{{ auth()->user()->isFollowing($user) ? 'Đã theo dõi' : 'Theo dõi' }}</span>
                            </button>

                            <button class="btn {{ auth()->user()->isFollowing($user) ? 'btn-primary' : 'btn-outline-primary' }} friend-button" 
                                    onclick="toggleFriend({{ $user->id }}, this)">
                                <i class="fas fa-{{ auth()->user()->isFollowing($user) ? 'user-friends' : 'user-plus' }}"></i>
                                {{ auth()->user()->isFollowing($user) ? 'Bạn bè' : 'Kết bạn' }}
                            </button>
                            <a href="{{ route('messages.show', $user->id) }}" class="btn btn-outline-success">
                                <i class="fas fa-comment"></i> Nhắn tin
                            </a>
                        </div>
                    @endif
                </div>
                
                {{-- Cột bên phải: Ngày tham gia + 3 chấm + giới thiệu + thống kê --}}
                <div class="col-md-9">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h3 class="mb-0">{{ $user->created_at->format('d/m/Y') }}</h3>
                            <p class="text-muted mb-0">Ngày tham gia</p>
                        </div>
                        @include('components.user-actions-menu', ['user' => $user])
                    </div>
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
                        <div class="col-md-4">
                            <div class="stats-item text-center p-3">
                                <h3 class="mb-0">{{ $user->posts()->count() }}</h3>
                                <p class="text-muted mb-0">Tổng bài viết</p>
                            </div>
                        </div>
                        {{-- Số người theo dõi --}}
                        <div class="col-md-4">
                            <div class="stats-item text-center p-3">
                                <h3 class="mb-0">{{ $user->followers()->count() }}</h3>
                                <p class="text-muted mb-0">Người theo dõi</p>
                            </div>
                        </div>
                        {{-- Số người đang theo dõi --}}
                        <div class="col-md-4">
                            <div class="stats-item text-center p-3">
                                <h3 class="mb-0 following-count">{{ $user->following()->count() }}</h3>
                                <p class="text-muted mb-0">Đang theo dõi</p>
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
            <button class="nav-link" id="friends-tab" data-bs-toggle="tab" data-bs-target="#friends-pane" type="button" role="tab" aria-controls="friends-pane" aria-selected="false">
                <i class="fas fa-user-friends"></i> Bạn bè
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="followers-tab" data-bs-toggle="tab" data-bs-target="#followers-pane" type="button" role="tab" aria-controls="followers-pane" aria-selected="false">
                <i class="fas fa-users"></i> Người theo dõi
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="following-tab" data-bs-toggle="tab" data-bs-target="#following-pane" type="button" role="tab" aria-controls="following-pane" aria-selected="false">
                <i class="fas fa-user-friends"></i> Đang theo dõi
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="groups-tab" data-bs-toggle="tab" data-bs-target="#groups-pane" type="button" role="tab" aria-controls="groups-pane" aria-selected="false">
                <i class="fas fa-users"></i> Nhóm
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="events-tab" data-bs-toggle="tab" data-bs-target="#events-pane" type="button" role="tab" aria-controls="events-pane" aria-selected="false">
                <i class="fas fa-calendar-alt"></i> Sự kiện
            </button>
        </li>
        <li class="nav-item" role="presentation">
            {{-- Thêm tab Bài viết yêu thích --}}
            <a class="nav-link" href="{{ route('posts.my_favorited') }}" role="tab">
                <i class="fas fa-heart"></i> Bài viết yêu thích
            </a>
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
                    <a href="{{ route('stories.create') }}" class="btn btn-info me-2">
                        <i class="fas fa-camera"></i> Đăng story
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
                                    <div class="d-flex justify-content-between align-items-start">
                                        <h5 class="card-title mb-0">{{ $post->title }}</h5>
                                        <div class="dropdown">
                                            <button class="btn btn-link text-dark p-0" type="button" id="dropdownMenuButton{{ $post->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v fa-lg"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton{{ $post->id }}">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('posts.show', $post) }}">
                                                        <i class="fas fa-eye me-2"></i> Xem
                                                    </a>
                                                </li>
                                                @if($post->user_id === auth()->id())
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('posts.edit', $post) }}">
                                                            <i class="fas fa-edit me-2"></i> Chỉnh sửa
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài viết này?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="fas fa-trash me-2"></i> Xóa bài viết
                                                            </button>
                                                        </form>
                                                    </li>
                                                @else
                                                    <li>
                                                        <button class="dropdown-item text-danger report-post" 
                                                                data-post-id="{{ $post->id }}"
                                                                data-post-title="{{ $post->title }}">
                                                            <i class="fas fa-flag me-2"></i> Báo cáo bài viết
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <button class="dropdown-item {{ auth()->user()->hasBlocked($post->user_id) ? 'text-success unblock-user' : 'text-danger block-user' }}"
                                                                data-user-id="{{ $post->user_id }}"
                                                                data-user-name="{{ $post->user->name }}">
                                                            <i class="fas {{ auth()->user()->hasBlocked($post->user_id) ? 'fa-unlock me-2' : 'fa-ban me-2' }}"></i>
                                                            {{ auth()->user()->hasBlocked($post->user_id) ? 'Bỏ chặn người dùng' : 'Chặn người dùng' }}
                                                        </button>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
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
                                        <a href="{{ route('comments.index', $post->id) }}" class="btn btn-sm comment-button ms-2">
                                            <i class="fas fa-comment"></i>
                                            <span class="comment-count">{{ $post->comments()->count() }}</span>
                                        </a>
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

        {{-- Tab Bạn bè --}}
        <div class="tab-pane fade" id="friends-pane" role="tabpanel" aria-labelledby="friends-tab">
            <div class="row">
                @forelse($user->acceptedFriends as $friend)
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $friend->avatar ? asset('storage/' . $friend->avatar) : asset('images/default-avatar.jpg') }}"
                                         class="rounded-circle me-3"
                                         style="width: 50px; height: 50px; object-fit: cover;"
                                         alt="{{ $friend->name }}'s avatar">
                                    <div>
                                        <h5 class="mb-0">{{ $friend->name }}</h5>
                                        <p class="text-muted mb-0">{{ $friend->email }}</p>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <a href="{{ route('posts.user_posts', $friend) }}" class="btn btn-sm btn-info me-2">
                                        <i class="fas fa-user me-1"></i>Xem trang cá nhân
                                    </a>
                                    <a href="{{ route('messages.show', $friend) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-envelope me-1"></i>Nhắn tin
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Chưa có bạn bè nào.
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Tab Người theo dõi --}}
        <div class="tab-pane fade" id="followers-pane" role="tabpanel" aria-labelledby="followers-tab">
            <div class="row">
                @forelse($user->followers as $follower)
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $follower->avatar ? asset('storage/' . $follower->avatar) : asset('stickers/avatar_icon.png') }}"
                                         class="rounded-circle me-3"
                                         style="width: 50px; height: 50px; object-fit: cover;"
                                         alt="{{ $follower->name }}'s avatar">
                                    <div>
                                        <h5 class="mb-0">{{ $follower->name }}</h5>
                                        <p class="text-muted mb-0">{{ $follower->email }}</p>
                                    </div>
                                </div>
                                @if(auth()->id() !== $follower->id)
                                    <div class="mt-3">
                                        <button class="btn {{ auth()->user()->isFollowing($follower) ? 'btn-primary' : 'btn-outline-primary' }} btn-sm follow-button" 
                                                data-user-id="{{ $follower->id }}">
                                            <i class="fas fa-user-plus"></i> 
                                            <span class="follow-text">{{ auth()->user()->isFollowing($follower) ? 'Đã theo dõi' : 'Theo dõi' }}</span>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info">
                            Chưa có người theo dõi nào.
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Tab Đang theo dõi --}}
        <div class="tab-pane fade" id="following-pane" role="tabpanel" aria-labelledby="following-tab">
            <div class="row" id="following-list">
                @forelse($user->following as $following)
                    <div class="col-md-4 mb-3 following-item" data-user-id="{{ $following->id }}">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $following->avatar ? asset('storage/' . $following->avatar) : asset('stickers/avatar_icon.png') }}"
                                         class="rounded-circle me-3"
                                         style="width: 50px; height: 50px; object-fit: cover;"
                                         alt="{{ $following->name }}'s avatar">
                                    <div>
                                        <h5 class="mb-0">{{ $following->name }}</h5>
                                        <p class="text-muted mb-0">{{ $following->email }}</p>
                                    </div>
                                </div>
                                @if(auth()->id() !== $following->id)
                                    <div class="mt-3">
                                        <button class="btn {{ auth()->user()->isFollowing($following) ? 'btn-primary' : 'btn-outline-primary' }} btn-sm follow-button" 
                                                data-user-id="{{ $following->id }}">
                                            <i class="fas fa-user-plus"></i> 
                                            <span class="follow-text">{{ auth()->user()->isFollowing($following) ? 'Hủy theo dõi' : 'Theo dõi' }}</span>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info">
                            Chưa theo dõi ai.
                        </div>
                    </div>
                @endforelse
            </div>
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

        {{-- Tab Sự kiện --}}
        <div class="tab-pane fade" id="events-pane" role="tabpanel" aria-labelledby="events-tab">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>Sự kiện của tôi</h4>
                <a href="{{ route('events.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tạo sự kiện mới
                </a>
            </div>

            {{-- Sự kiện đang tham gia --}}
            <div class="mb-5">
                <h5 class="mb-3">Sự kiện đang tham gia</h5>
                <div class="row">
                    @forelse($user->joinedEvents as $event)
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                @if($event->image_path)
                                    <img src="{{ asset('storage/' . $event->image_path) }}" 
                                         class="card-img-top" 
                                         alt="{{ $event->title }}"
                                         style="height: 200px; object-fit: cover;">
                                @endif
                                <div class="card-body">
                                    <h5 class="card-title">{{ $event->title }}</h5>
                                    <p class="card-text text-muted">
                                        <i class="fas fa-clock me-2"></i>{{ $event->event_time->format('d/m/Y H:i') }}
                                    </p>
                                    <p class="card-text text-muted">
                                        <i class="fas fa-map-marker-alt me-2"></i>{{ $event->location }}
                                    </p>
                                    <p class="card-text">{{ Str::limit($event->description, 100) }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge bg-primary">
                                            <i class="fas fa-users me-1"></i>{{ $event->participants_count }} người tham gia
                                        </span>
                                        <div>
                                            <a href="{{ route('events.show', $event) }}" class="btn btn-sm btn-info me-2">
                                                <i class="fas fa-eye"></i> Xem chi tiết
                                            </a>
                                            <form action="{{ route('events.leave', $event) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-sign-out-alt"></i> Rời khỏi
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Bạn chưa tham gia sự kiện nào.
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Sự kiện đã tạo --}}
            <div>
                <h5 class="mb-3">Sự kiện đã tạo</h5>
                <div class="row">
                    @forelse($user->events as $event)
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                @if($event->image_path)
                                    <img src="{{ asset('storage/' . $event->image_path) }}" 
                                         class="card-img-top" 
                                         alt="{{ $event->title }}"
                                         style="height: 200px; object-fit: cover;">
                                @endif
                                <div class="card-body">
                                    <h5 class="card-title">{{ $event->title }}</h5>
                                    <p class="card-text text-muted">
                                        <i class="fas fa-clock me-2"></i>{{ $event->event_time->format('d/m/Y H:i') }}
                                    </p>
                                    <p class="card-text text-muted">
                                        <i class="fas fa-map-marker-alt me-2"></i>{{ $event->location }}
                                    </p>
                                    <p class="card-text">{{ Str::limit($event->description, 100) }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge bg-primary">
                                            <i class="fas fa-users me-1"></i>{{ $event->participants_count }} người tham gia
                                        </span>
                                        <div>
                                            <a href="{{ route('events.show', $event) }}" class="btn btn-sm btn-info me-2">
                                                <i class="fas fa-eye"></i> Xem chi tiết
                                            </a>
                                            <a href="{{ route('events.edit', $event) }}" class="btn btn-sm btn-warning me-2">
                                                <i class="fas fa-edit"></i> Chỉnh sửa
                                            </a>
                                            <form action="{{ route('events.destroy', $event) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sự kiện này?');">
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
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Bạn chưa tạo sự kiện nào.
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
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
    .like-button {
        transition: all 0.3s ease;
        padding: 8px 14px;
        font-size: 0.875rem;
        border-radius: 20px;
        border: 1px solid #dc3545;
        background-color: transparent;
        color: #dc3545;
        width: 70px;
        text-align: center;
        flex-shrink: 0;
    }

    .like-button i {
        font-size: 1.1rem;
        margin-right: 0.3rem;
        transition: all 0.3s ease;
    }

    .like-button.active {
        background-color: #ffebee;
        border-color: #ffcdd2;
    }

    .like-button.active i {
        color: #e53935 !important;
        transform: scale(1.1);
    }

    .like-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        background-color: #ffebee;
    }

    .like-button:hover i {
        transform: scale(1.1);
    }

    .like-count {
        font-weight: 500;
        margin-left: 0.3rem;
    }

    .comment-button {
        transition: all 0.3s ease;
        padding: 8px 14px;
        font-size: 0.875rem;
        border-radius: 20px;
        border: 1px solid #6c757d;
        background-color: transparent;
        color: #6c757d;
        width: 70px;
        text-align: center;
        flex-shrink: 0;
    }

    .comment-button i {
        font-size: 1.1rem;
        margin-right: 0.3rem;
        transition: all 0.3s ease;
    }

    .comment-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        background-color: #f8f9fa;
    }

    .comment-button:hover i {
        transform: scale(1.1);
    }

    .comment-count {
        font-weight: 500;
        margin-left: 0.3rem;
    }
</style>
@endpush

@push('scripts')
<script>
window.profileId = {{ $user->id }};

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

    // Xử lý sự kiện follow
    function followButtonHandler() {
        const userId = this.dataset.userId;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const followText = this.querySelector('.follow-text');
        const isFollowing = ['Đang theo dõi', 'Bỏ theo dõi', 'Hủy theo dõi'].includes(followText.textContent.trim());

        this.disabled = true;

        const url = isFollowing ? `/users/${userId}/unfollow` : `/users/${userId}/follow`;

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(async response => {
            this.disabled = false;
            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                let message = errorData.message || 'Có lỗi xảy ra khi thực hiện thao tác theo dõi';
                throw new Error(message);
            }
            return response.json();
        })
        .then(data => {
            if (isFollowing) {
                followText.textContent = 'Theo dõi';
                this.classList.remove('btn-primary');
                this.classList.add('btn-outline-primary');
            } else {
                followText.textContent = 'Đang theo dõi';
                this.classList.remove('btn-outline-primary');
                this.classList.add('btn-primary');
            }

            // Luôn fetch lại danh sách following để đảm bảo số lượng và danh sách đúng
            fetch(`/users/${window.profileId}/following-list`)
                .then(res => res.text())
                .then(html => {
                    const followingList = document.getElementById('following-list');
                    if (followingList) {
                        followingList.innerHTML = html;
                        // Gắn lại sự kiện cho nút follow/unfollow mới render
                        followingList.querySelectorAll('.follow-button').forEach(button => {
                            button.addEventListener('click', followButtonHandler);
                        });
                        // Cập nhật lại số lượng "Đang theo dõi" dựa trên số lượng item thực tế
                        const newCount = followingList.querySelectorAll('.following-item').length;
                        const followingCount = document.querySelector('.following-count');
                        if (followingCount) {
                            followingCount.textContent = newCount;
                        }
                    }
                });
        })
        .catch(error => {
            alert(error.message);
        });
    }

    // Gắn sự kiện cho tất cả nút follow
    document.querySelectorAll('.follow-button').forEach(button => {
        button.addEventListener('click', followButtonHandler);
    });

    // Xử lý sự kiện báo cáo bài viết
    document.querySelectorAll('.report-post').forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.dataset.postId;
            const postTitle = this.dataset.postTitle;
            
            // Tạo modal báo cáo động
            const modalHtml = `
                <div class="modal fade" id="reportModal-${postId}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Báo cáo bài viết</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                            </div>
                            <form action="/posts/${postId}/report" method="POST">
                                @csrf
                                <div class="modal-body">
                                    <p>Bạn đang báo cáo bài viết: <strong>${postTitle}</strong></p>
                                    <div class="mb-3">
                                        <label class="form-label">Lý do báo cáo:</label>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="reason" id="reason1-${postId}" value="Nội dung không phù hợp" required>
                                            <label class="form-check-label" for="reason1-${postId}">
                                                Nội dung không phù hợp
                                            </label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="reason" id="reason2-${postId}" value="Spam" required>
                                            <label class="form-check-label" for="reason2-${postId}">
                                                Spam
                                            </label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="reason" id="reason3-${postId}" value="Vi phạm bản quyền" required>
                                            <label class="form-check-label" for="reason3-${postId}">
                                                Vi phạm bản quyền
                                            </label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="reason" id="reason4-${postId}" value="Quấy rối" required>
                                            <label class="form-check-label" for="reason4-${postId}">
                                                Quấy rối
                                            </label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="reason" id="reason5-${postId}" value="Bạo lực" required>
                                            <label class="form-check-label" for="reason5-${postId}">
                                                Bạo lực
                                            </label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="reason" id="reason6-${postId}" value="other" required>
                                            <label class="form-check-label" for="reason6-${postId}">
                                                Lý do khác
                                            </label>
                                        </div>
                                        <div class="mt-2 d-none" id="otherReasonContainer-${postId}">
                                            <textarea class="form-control" name="other_reason" rows="2" placeholder="Vui lòng mô tả lý do báo cáo..."></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                    <button type="submit" class="btn btn-danger">Gửi báo cáo</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            `;
            
            // Thêm modal vào body
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            
            // Hiển thị modal
            const modal = new bootstrap.Modal(document.getElementById(`reportModal-${postId}`));
            modal.show();
            
            // Xử lý hiển thị/ẩn textarea lý do khác
            const otherRadio = document.getElementById(`reason6-${postId}`);
            const otherReasonContainer = document.getElementById(`otherReasonContainer-${postId}`);
            
            document.querySelectorAll(`#reportModal-${postId} input[name="reason"]`).forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'other') {
                        otherReasonContainer.classList.remove('d-none');
                    } else {
                        otherReasonContainer.classList.add('d-none');
                    }
                });
            });
            
            // Xóa modal khi đóng
            document.getElementById(`reportModal-${postId}`).addEventListener('hidden.bs.modal', function() {
                this.remove();
            });
        });
    });

    // Xử lý sự kiện chặn người dùng
    document.querySelectorAll('.block-user').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.userId;
            const userName = this.dataset.userName;
            
            if (confirm(`Bạn có chắc chắn muốn chặn người dùng ${userName}? Người dùng này sẽ không thể tương tác với bạn nữa.`)) {
                fetch(`/users/${userId}/block`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Đã chặn người dùng thành công');
                        location.reload();
                    } else {
                        alert(data.error || 'Có lỗi xảy ra khi chặn người dùng');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi chặn người dùng');
                });
            }
        });
    });

    // Xử lý sự kiện bỏ chặn người dùng
    document.querySelectorAll('.unblock-user').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.userId;
            const userName = this.dataset.userName;
            
            if (confirm(`Bạn có chắc chắn muốn bỏ chặn người dùng ${userName}?`)) {
                fetch(`/users/${userId}/unblock`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Đã bỏ chặn người dùng thành công');
                        location.reload();
                    } else {
                        alert(data.error || 'Có lỗi xảy ra khi bỏ chặn người dùng');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi bỏ chặn người dùng');
                });
            }
        });
    });

    // Xử lý sự kiện báo cáo
    document.querySelectorAll('.report-user').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.userId;
            const modal = new bootstrap.Modal(document.getElementById(`reportModal-${userId}`));
            modal.show();

            // Hiện/ẩn textarea khi chọn "Lý do khác"
            const otherRadio = document.getElementById(`reason5-${userId}`);
            const otherReasonContainer = document.getElementById(`otherReasonContainer-${userId}`);
            document.querySelectorAll(`input[name=\"reason-${userId}\"]`).forEach(radio => {
                radio.addEventListener('change', function() {
                    if (otherRadio.checked) {
                        otherReasonContainer.classList.remove('d-none');
                    } else {
                        otherReasonContainer.classList.add('d-none');
                    }
                });
            });
        });
    });

    // Xử lý sự kiện gửi báo cáo
    document.querySelectorAll('.submit-report').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.userId;
            const selectedReason = document.querySelector(`input[name=\"reason-${userId}\"]:checked`);
            let reason = '';
            if (selectedReason) {
                reason = selectedReason.value === 'other'
                    ? document.querySelector(`#otherReasonContainer-${userId} textarea`).value.trim()
                    : selectedReason.value;
            }
            if (!reason) {
                alert('Vui lòng chọn và nhập lý do báo cáo');
                return;
            }
            fetch(`/users/${userId}/report`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content
                },
                body: JSON.stringify({ reason: reason })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById(`reportModal-${userId}`)).hide();
                    alert('Đã gửi báo cáo thành công');
                } else {
                    alert(data.error || 'Có lỗi xảy ra');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra');
            });
        });
    });
});
</script>
@endpush
@endsection