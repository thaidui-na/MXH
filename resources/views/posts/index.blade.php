@extends('layouts.app')

@section('title', 'Bảng tin')

{{-- Thêm CSS tùy chỉnh --}}
@push('styles')
<style>
    /* Style cho card bài viết */
    .post-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        margin-bottom: 1.5rem;
        background: #fff;
        overflow: hidden;
    }

    .post-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.12);
    }

    /* Style cho tiêu đề trang */
    .page-title {
        font-size: 2rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #eee;
    }

    /* Style cho card nhóm */
    .group-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        margin-bottom: 1.5rem;
        background: #fff;
        overflow: hidden;
    }

    .group-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.12);
    }

    .group-cover {
        height: 150px;
        object-fit: cover;
        width: 100%;
    }

    .group-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        border: 3px solid white;
        margin-top: -30px;
        margin-left: 20px;
        object-fit: cover;
    }

    .group-info {
        padding: 20px;
    }

    .group-title {
        color: #2c3e50;
        font-weight: 600;
        font-size: 1.25rem;
        margin-bottom: 0.5rem;
    }

    .group-description {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 1rem;
    }

    .group-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: #6c757d;
        font-size: 0.875rem;
    }

    .group-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
    }

    /* Style cho tiêu đề bài viết */
    .post-title {
        color: #2c3e50;
        font-weight: 600;
        font-size: 1.25rem;
        margin-bottom: 0.5rem;
        transition: color 0.2s ease;
    }

    .post-title:hover {
        color: #3498db;
        text-decoration: none;
    }

    /* Style cho meta info */
    .post-meta {
        font-size: 0.875rem;
        color: #6c757d;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .post-meta i {
        margin-right: 0.25rem;
    }

    /* Style cho nội dung bài viết */
    .post-excerpt {
        color: #505965;
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 1rem;
    }

    /* Style cho nút xem chi tiết */
    .btn-view-post {
        transition: all 0.3s ease;
        padding: 8px 14px;
        font-size: 0.875rem;
        border-radius: 20px;
        border: 1px solid #3498db;
        background-color: transparent;
        color: #3498db;
        min-width: 120px;
        text-align: center;
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.3rem;
        white-space: nowrap;
    }

    .btn-view-post i {
        font-size: 1.1rem;
        transition: all 0.3s ease;
        line-height: 1;
    }

    .btn-view-post span {
        line-height: 1;
    }

    .btn-view-post:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        background-color: #e3f2fd;
        color: #1976d2;
        border-color: #90caf9;
    }

    .btn-view-post:hover i {
        transform: scale(1.1);
    }

    /* Style cho phân trang */
    .pagination {
        margin-top: 2rem;
    }

    .page-link {
        border-radius: 5px;
        margin: 0 3px;
        color: #3498db;
        border: 1px solid #e9ecef;
    }

    .page-item.active .page-link {
        background-color: #3498db;
        border-color: #3498db;
    }

    /* Style cho alert */
    .custom-alert {
        border-radius: 10px;
        border-left: 4px solid #3498db;
        background-color: #f8f9fa;
        padding: 1rem;
    }

    .custom-alert a {
        color: #3498db;
        font-weight: 500;
        text-decoration: none;
    }

    .custom-alert a:hover {
        text-decoration: underline;
    }

    /* Thêm animation cho cards */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .post-card,
    .group-card {
        animation: fadeInUp 0.5s ease forwards;
    }

    .user-card {
        transition: all 0.3s ease;
        border: none;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
    }

    .user-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.12);
    }

    .user-card img {
        border: 3px solid #fff;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    #searchSuggestions {
        top: 100%;
        background: white;
        border-radius: 0 0 4px 4px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        max-height: 300px;
        overflow-y: auto;
    }

    #searchSuggestions .list-group-item {
        border-left: none;
        border-right: none;
        padding: 0.75rem 1rem;
    }

    #searchSuggestions .list-group-item:first-child {
        border-top: none;
    }

    #searchSuggestions .list-group-item:hover {
        background-color: #f8f9fa;
    }

    #searchSuggestions .list-group-item img {
        border: 2px solid #fff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* Style cho nút like */
    .like-button {
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
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.3rem;
    }

    .like-button i {
        font-size: 1.1rem;
        transition: all 0.3s ease;
        line-height: 1;
    }

    .like-button.active {
        background-color: #ffebee;
        border-color: #ffcdd2;
        color: #dc3545;
    }

    .like-button.active i {
        color: #e53935 !important;
        transform: scale(1.1);
    }

    .like-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        background-color: #f8f9fa;
    }

    .like-button:hover i {
        transform: scale(1.1);
    }

    .like-count {
        font-weight: 500;
        line-height: 1;
    }

    .btn-view-likes {
        transition: all 0.3s ease;
        padding: 8px 14px;
        font-size: 0.875rem;
        border-radius: 20px;
        border: 1px solid #6c757d;
        background-color: transparent;
        color: #6c757d;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.3rem;
    }

    .btn-view-likes i {
        font-size: 1.1rem;
        transition: all 0.3s ease;
        line-height: 1;
    }

    .btn-view-likes:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        background-color: #f8f9fa;
    }

    .btn-view-likes:hover i {
        transform: scale(1.1);
    }

    .stories-container {
        display: flex;
        gap: 1rem;
        overflow-x: auto;
        padding: 0.5rem 0;
    }

    .story-item {
        text-align: center;
        min-width: 80px;
    }

    .story-avatar {
        width: 64px;
        height: 64px;
        margin: 0 auto;
        position: relative;
    }

    .story-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border: 2px solid #ddd;
        transition: all 0.3s ease;
    }

    .story-avatar img.has-story {
        border: 2px solid #007bff;
    }

    .story-username {
        font-size: 0.8rem;
        margin-top: 0.5rem;
        color: #333;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 80px;
    }

    .add-story {
        position: relative;
        width: 100%;
        height: 100%;
    }

    .add-story-icon {
        position: absolute;
        bottom: 0;
        right: 0;
        background: #007bff;
        color: white;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        border: 2px solid white;
    }

    .favorite-button {
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
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .favorite-button i {
        font-size: 1.1rem;
        margin-right: 0.3rem;
        transition: all 0.3s ease;
        line-height: 1;
    }

    .favorite-button.active {
        background-color: #e3f2fd;
        border-color: #90caf9;
        color: #1976d2;
    }

    .favorite-button.active i {
        color: #1976d2;
        transform: scale(1.1);
    }

    .favorite-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        background-color: #f8f9fa;
    }

    .favorite-button:hover i {
        transform: scale(1.1);
    }

    .favorite-text {
        font-weight: 500;
        line-height: 1;
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    {{-- Display Session Flash Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Stories Section --}}
    @if($stories->isNotEmpty())
        <div class="card mb-4">
            <div class="card-body">
                <div class="stories-container">
                    {{-- Nút đăng story mới --}}
                    <div class="story-item">
                        <a href="{{ route('stories.create') }}" class="text-decoration-none">
                            <div class="story-avatar">
                                <div class="add-story">
                                    <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="rounded-circle">
                                    <div class="add-story-icon">
                                        <i class="fas fa-plus"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="story-username">Đăng story</div>
                        </a>
                    </div>

                    {{-- Danh sách stories --}}
                    @foreach($stories as $userId => $userStories)
                        @php
                            $user = $userStories->first()->user;
                            $latestStory = $userStories->first();
                        @endphp
                        <div class="story-item">
                            <a href="{{ route('stories.show', $latestStory) }}" class="text-decoration-none">
                                <div class="story-avatar">
                                    @if($latestStory->media_type === 'image')
                                        <img src="{{ $latestStory->media_url }}" alt="{{ $user->name }}'s Story"
                                             class="{{ $userStories->where('is_active', true)->count() > 0 ? 'has-story' : '' }}">
                                    @else
                                        {{-- Hiển thị thumbnail hoặc icon cho video --}}
                                        <img src="{{ $latestStory->media_url }}" alt="{{ $user->name }}'s Story"
                                             class="{{ $userStories->where('is_active', true)->count() > 0 ? 'has-story' : '' }}"
                                             onerror="this.onerror=null;this.src='{{ asset('images/video-placeholder.png') }}';">
                                        {{-- Có thể thêm icon play overlay ở đây --}}
                                    @endif
                                </div>
                                <div class="story-username">{{ $user->name }}</div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- Tabs chuyển đổi --}}
    <ul class="nav nav-tabs mb-4" id="feedTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="posts-tab" data-bs-toggle="tab" data-bs-target="#posts-pane" type="button" role="tab" aria-controls="posts-pane" aria-selected="true">
                <i class="fas fa-newspaper"></i> Bài viết
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="groups-tab" data-bs-toggle="tab" data-bs-target="#groups-pane" type="button" role="tab" aria-controls="groups-pane" aria-selected="false">
                <i class="fas fa-users"></i> Nhóm của tôi
            </button>
        </li>
    </ul>
    <div class="tab-content" id="feedTabContent">
        {{-- Tab Bài viết --}}
        <div class="tab-pane fade show active" id="posts-pane" role="tabpanel" aria-labelledby="posts-tab">
            {{-- Hiển thị bài viết --}}
            @if($posts->count() > 0)
            <h5 class="mb-3">Bài viết mới nhất</h5>
            <div class="row">
                @foreach($posts as $index => $post)
                <div class="col-md-12" style="animation-delay: {{ $index * 0.1 }}s;">
                    <div class="card post-card {{ in_array($post->id, $readPostIds) ? 'opacity-50' : '' }}">
                        <div class="card-body">
                            <a href="{{ route('posts.show', $post) }}" class="text-decoration-none">
                                <h5 class="post-title">{{ $post->title }}</h5>
                            </a>
                            <div class="post-meta">
                                <span>
                                    <i class="fas fa-user-circle"></i>
                                    {{ $post->user->name }}
                                </span>
                                <span>
                                    <i class="fas fa-clock"></i>
                                    {{ $post->created_at->format('d/m/Y H:i') }}
                                </span>
                            </div>
                            <p class="post-excerpt">{{ Str::limit($post->content, 200) }}</p>
                            <div class="d-flex justify-content-end align-items-center">
                                <button class="btn btn-sm like-button {{ $post->isLikedBy(auth()->id()) ? 'active' : '' }}"
                                    data-post-id="{{ $post->id }}">
                                    <i class="fas fa-heart {{ $post->isLikedBy(auth()->id()) ? 'text-danger' : '' }}"></i>
                                    <span class="like-count">{{ $post->getLikesCount() }}</span>
                                </button>
                                <button class="btn btn-sm btn-view-likes ms-2" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#likesModal{{ $post->id }}">
                                    <i class="fas fa-users"></i>
                                    <span>Xem người thích</span>
                                </button>
                                <a href="{{ route('posts.show', $post) }}" class="btn btn-view-post ms-2">
                                    <i class="fas fa-eye"></i> Xem chi tiết
                                </a>
                                <form action="{{ route('posts.favorites.toggle', $post) }}" method="POST" class="d-inline ms-2">
                                    @csrf
                                    <button type="submit" class="btn btn-sm favorite-button {{ $post->isFavoritedBy(auth()->id()) ? 'active' : '' }}">
                                        <i class="{{ $post->isFavoritedBy(auth()->id()) ? 'fas' : 'far' }} fa-bookmark"></i>
                                        <span class="favorite-text">{{ $post->isFavoritedBy(auth()->id()) ? 'Đã lưu' : 'Lưu' }}</span>
                                    </button>
                                </form>
                                @if(in_array($post->id, $readPostIds))
                                    <span class="badge bg-success ms-2">Đã đọc</span>
                                @else
                                    <button type="button" class="btn btn-sm btn-outline-secondary ms-2 mark-as-read-btn" data-post-id="{{ $post->id }}">
                                        <i class="fas fa-check"></i> Đánh dấu đã đọc
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal hiển thị danh sách người like -->
                <div class="modal fade" id="likesModal{{ $post->id }}" tabindex="-1" aria-labelledby="likesModalLabel{{ $post->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="likesModalLabel{{ $post->id }}">Người đã thích bài viết</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="likes-list">
                                    @foreach($post->likes as $user)
                                    <div class="d-flex align-items-center mb-2">
                                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                        <div>
                                            <h6 class="mb-0">{{ $user->name }}</h6>
                                            <small class="text-muted">{{ $user->email }}</small>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
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
            <div class="alert custom-alert">
                Chưa có bài viết nào được đăng. <a href="{{ route('posts.create') }}">Hãy là người đầu tiên đăng bài</a>
            </div>
            @endif
        </div>
        {{-- Tab Nhóm của tôi --}}
        <div class="tab-pane fade" id="groups-pane" role="tabpanel" aria-labelledby="groups-tab">
            <h4>Nhóm của bạn</h4>
            {{-- Form tìm kiếm nhóm trong tab Nhóm của tôi (sử dụng cho autocomplete) --}}
            <div class="group-search-container position-relative mb-4">
                <div class="input-group">
                    <input type="text" name="q_groups" class="form-control" id="groupSearchInput" placeholder="Tìm kiếm nhóm..." value="{{ request('q') }}" autocomplete="off">
                    <button class="btn btn-outline-secondary" type="button"><i class="fas fa-search"></i></button>
                </div>
                {{-- Kết quả tìm kiếm gợi ý --}}
                <div id="groupSearchResults" class="list-group position-absolute w-100 mt-1" style="z-index: 1000;"></div>
            </div>

            @php
                // Lấy các nhóm mà người dùng hiện tại là thành viên
                $userGroupsQuery = auth()->user()->joinedGroups();

                // Áp dụng bộ lọc tìm kiếm nếu có (cho lần tải trang ban đầu)
                $q = request('q');
                if ($q) {
                    $userGroupsQuery->where(function($query) use ($q) {
                        $query->where('name', 'like', "%$q%")
                              ->orWhere('description', 'like', "%$q%");
                    });
                }

                // Lấy kết quả
                $userGroups = $userGroupsQuery->withCount('members')->get();
            @endphp

            @if($userGroups->count() > 0)
                <div class="row">
                    @foreach($userGroups as $group)
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="position-relative">
                                    <img src="{{ $group->cover_image ? asset('storage/' . $group->cover_image) : asset('images/default-cover.jpg') }}"
                                         onerror="this.onerror=null;this.src='{{ asset('images/default-cover.jpg') }}';"
                                         class="card-img-top" alt="Cover" style="height: 100px; object-fit: cover;">
                                    <img src="{{ $group->avatar ? asset('storage/' . $group->avatar) : asset('images/default-avatar.jpg') }}"
                                         onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.jpg') }}';"
                                         class="rounded-circle position-absolute"
                                         style="width: 40px; height: 40px; bottom: -20px; left: 15px; border: 3px solid white;">
                                </div>
                                <div class="card-body pt-4">
                                    <h5 class="card-title">{{ $group->name }}</h5>
                                    <p class="card-text text-muted small">
                                        {{ Str::limit($group->description, 100) }}
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted small">
                                            <i class="fas fa-users"></i> {{ $group->members_count }} thành viên
                                        </span>
                                        <span class="badge {{ $group->is_private ? 'bg-secondary' : 'bg-success' }}">
                                            {{ $group->is_private ? 'Riêng tư' : 'Công khai' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('groups.show', $group) }}" class="btn btn-outline-primary mb-2">
                                            Xem chi tiết
                                        </a>
                                        {{-- Nút Rời nhóm (trong tab Nhóm của tôi) --}}
                                        <form method="POST" action="{{ route('groups.leave', $group->id) }}" onsubmit="return confirm('Bạn có chắc chắn muốn rời nhóm này?');">
                                             @csrf
                                             <button type="submit" class="btn btn-danger btn-sm w-100">
                                                 <i class="fas fa-sign-out-alt"></i> Rời nhóm
                                             </button>
                                         </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    @if(request('q'))
                        Không tìm thấy nhóm nào phù hợp với từ khóa "{{ request('q') }}".
                    @else
                        Bạn chưa tham gia nhóm nào. Hãy <a href="{{ route('groups.index') }}">tìm và tham gia nhóm</a>!
                    @endif
                </div>
            @endif
        </div>
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
                // Cập nhật lại danh sách người đã thích trong modal
                fetch(`/posts/${postId}/likes-list`)
                    .then(res => res.text())
                    .then(html => {
                        const modalLikesList = document.querySelector(`#likesModal${postId} .likes-list`);
                        if (modalLikesList) {
                            modalLikesList.innerHTML = html;
                        }
                    });
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    });

    // Chức năng tìm kiếm nhóm (Autocomplete)
    const groupSearchInput = document.getElementById('groupSearchInput');
    const groupSearchResults = document.getElementById('groupSearchResults');
    let groupSearchTimeout;

    if (groupSearchInput && groupSearchResults) {
        groupSearchInput.addEventListener('input', function() {
            clearTimeout(groupSearchTimeout);
            const query = this.value.trim();

            if (query.length < 2) {
                groupSearchResults.innerHTML = '';
                groupSearchResults.classList.remove('list-group'); // Xóa class list-group khi rỗng
                return;
            }

            groupSearchTimeout = setTimeout(() => {
                fetch(`/api/groups/search?q=${encodeURIComponent(query)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    groupSearchResults.innerHTML = '';
                    groupSearchResults.classList.add('list-group'); // Thêm lại class list-group khi có kết quả
                    
                    if (data.length === 0) {
                        groupSearchResults.innerHTML = `
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
                                    <img src="${group.avatar ? '/storage/' + group.avatar : '/images/default-avatar.jpg'}" 
                                         class="rounded-circle me-2" 
                                         style="width: 32px; height: 32px; object-fit: cover;">
                                    <div>
                                        <div class="fw-bold">${group.name}</div>
                                        <small class="text-muted">${group.members_count || 0} thành viên</small>
                                    </div>
                                </div>`;
                            groupSearchResults.appendChild(item);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    groupSearchResults.innerHTML = `
                        <div class="list-group-item text-center text-danger">
                            Đã có lỗi xảy ra khi tìm kiếm
                        </div>`;
                     groupSearchResults.classList.add('list-group'); // Thêm lại class list-group khi có lỗi
                });
            }, 300); // Độ trễ 300ms trước khi tìm kiếm
        });

        // Ẩn kết quả khi click ra ngoài
        document.addEventListener('click', function(e) {
            if (!groupSearchInput.contains(e.target) && !groupSearchResults.contains(e.target)) {
                groupSearchResults.innerHTML = '';
                groupSearchResults.classList.remove('list-group');
            }
        });

        // Ẩn kết quả khi scroll (để tránh bị che)
        document.querySelector('.tab-content').addEventListener('scroll', function() {
             groupSearchResults.innerHTML = '';
             groupSearchResults.classList.remove('list-group');
        });

         // Hiện lại kết quả khi focus vào input nếu đã có query
        groupSearchInput.addEventListener('focus', function() {
            if (this.value.trim().length >= 2 && groupSearchResults.innerHTML !== '') {
                groupSearchResults.classList.add('list-group');
            }
        });
    }

    document.querySelectorAll('.mark-as-read-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var postId = this.getAttribute('data-post-id');
            var button = this;
            fetch('/posts/' + postId + '/read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Làm mờ card và đổi nút thành badge
                    var card = button.closest('.post-card');
                    card.classList.add('opacity-50');
                    button.outerHTML = '<span class="badge bg-success ms-2">Đã đọc</span>';
                }
            });
        });
    });
});
</script>
@endpush