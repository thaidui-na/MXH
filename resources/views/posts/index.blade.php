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
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        transition: all 0.3s ease;
        background-color: #3498db;
        border-color: #3498db;
        color: white;
    }

    .btn-view-post:hover {
        background-color: #2980b9;
        border-color: #2980b9;
        transform: translateX(5px);
    }

    .btn-view-post i {
        margin-right: 0.5rem;
        transition: transform 0.3s ease;
    }

    .btn-view-post:hover i {
        transform: translateX(3px);
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
</style>
@endpush

@section('content')
<div class="container py-4">
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
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="friends-tab" data-bs-toggle="tab" data-bs-target="#friends-pane" type="button" role="tab" aria-controls="friends-pane" aria-selected="false">
                <i class="fas fa-user-friends"></i> Tìm bạn bè
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
                    <div class="card post-card">
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
                            <div class="d-flex justify-content-end">
                                <button class="btn btn-sm btn-outline-danger me-2 like-button {{ $post->isLikedBy(auth()->id()) ? 'active' : '' }}"
                                    data-post-id="{{ $post->id }}">
                                    <i class="fas fa-heart {{ $post->isLikedBy(auth()->id()) ? 'text-danger' : 'text-muted' }}"></i>
                                    <span class="like-count ms-1">{{ $post->getLikesCount() }}</span>
                                </button>
                                <a href="{{ route('posts.show', $post) }}" class="btn btn-view-post">
                                    <i class="fas fa-eye"></i> Xem chi tiết
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
            <div class="alert custom-alert">
                Chưa có bài viết nào được đăng. <a href="{{ route('posts.create') }}">Hãy là người đầu tiên đăng bài</a>
            </div>
            @endif
        </div>
        {{-- Tab Nhóm của tôi --}}
        <div class="tab-pane fade" id="groups-pane" role="tabpanel" aria-labelledby="groups-tab">
            @if($groups->count() > 0)
            <h5 class="mb-3">Nhóm của bạn</h5>
            <div class="row mb-4">
                @foreach($groups as $group)
                <div class="col-md-4">
                    <div class="group-card">
                        <img src="{{ $group->cover_image ? Storage::url($group->cover_image) : asset('images/default-cover.jpg') }}" class="group-cover" alt="Cover">
                        <img src="{{ $group->avatar ? Storage::url($group->avatar) : asset('images/default-avatar.jpg') }}" class="group-avatar" alt="Avatar">
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
                                <a href="{{ route('groups.show', $group) }}" class="btn btn-outline-primary">
                                    Xem chi tiết
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="alert custom-alert">
                Bạn chưa tham gia nhóm nào. <a href="{{ route('groups.create') }}">Tạo nhóm mới</a>
            </div>
            @endif
        </div>
        {{-- Tab Tìm bạn bè --}}
        <div class="tab-pane fade" id="friends-pane" role="tabpanel" aria-labelledby="friends-tab">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('posts.index') }}" class="mb-4" id="friendSearchForm">
                        @csrf
                        <div class="input-group position-relative">
                            <input type="text" name="friend_search" id="friendSearch" class="form-control" placeholder="Nhập tên hoặc email người dùng cần tìm..." value="{{ request('friend_search') }}">
                            <button class="btn btn-primary" type="button" id="searchButton">
                                <i class="fas fa-search"></i> Tìm kiếm
                            </button>
                            <div id="searchSuggestions" class="position-absolute w-100" style="z-index: 1000; display: none;">
                                <!-- Kết quả gợi ý sẽ được thêm vào đây -->
                            </div>
                        </div>
                    </form>

                    <div id="searchResults">
                        <!-- Kết quả tìm kiếm sẽ được thêm vào đây -->
                    </div>

                    <div class="text-center text-muted py-5" id="defaultView">
                        <i class="fas fa-search fa-3x mb-3"></i>
                        <h5>Tìm kiếm bạn bè</h5>
                        <p>Nhập tên hoặc email để tìm kiếm người dùng</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Thêm JavaScript cho hiệu ứng và tab --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('friendSearch');
        const searchButton = document.getElementById('searchButton');
        const searchResults = document.getElementById('searchResults');
        const defaultView = document.getElementById('defaultView');
        let searchTimeout;

        // Hàm thực hiện tìm kiếm
        function performSearch(query) {
            console.log('Performing search for:', query);
            
            if (query.length < 2) {
                searchResults.innerHTML = '';
                defaultView.style.display = 'block';
                return;
            }

            defaultView.style.display = 'none';
            searchResults.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Đang tìm kiếm...</span>
                    </div>
                    <p class="mt-2 text-muted">Đang tìm kiếm...</p>
                </div>
            `;

            const searchUrl = `/api/users/search?q=${encodeURIComponent(query)}`;
            console.log('Search URL:', searchUrl);

            fetch(searchUrl, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                credentials: 'same-origin'
            })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    return response.json().then(err => {
                        console.error('Search error response:', err);
                        throw new Error(err.message || 'Có lỗi xảy ra khi tìm kiếm');
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Search response data:', data);
                
                if (data.error) {
                    throw new Error(data.message || data.error);
                }
                
                if (!Array.isArray(data) || data.length === 0) {
                    searchResults.innerHTML = `
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Không tìm thấy người dùng nào phù hợp với từ khóa "${query}"
                        </div>
                    `;
                    return;
                }

                searchResults.innerHTML = `
                    <div class="row">
                        ${data.map(user => `
                            <div class="col-md-3 mb-4">
                                <div class="card user-card">
                                    <div class="card-body text-center">
                                        <img src="${user.avatar_url}" 
                                             class="rounded-circle mb-3" 
                                             style="width: 80px; height: 80px; object-fit: cover;"
                                             alt="${user.name}"
                                             onerror="this.src='/images/default-avatar.jpg'">
                                        <h6 class="card-title mb-1">${user.name}</h6>
                                        <p class="text-muted small mb-3">${user.email}</p>
                                        <div class="d-grid">
                                            <a href="/messages/${user.id}" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-comment"></i> Nhắn tin
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                `;
            })
            .catch(error => {
                console.error('Search error:', error);
                searchResults.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> ${error.message || 'Có lỗi xảy ra khi tìm kiếm. Vui lòng thử lại sau.'}
                    </div>
                `;
            });
        }

        // Xử lý sự kiện input
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            console.log('Input changed:', query);
            searchTimeout = setTimeout(() => performSearch(query), 300);
        });

        // Xử lý sự kiện click nút tìm kiếm
        searchButton.addEventListener('click', function() {
            const query = searchInput.value.trim();
            console.log('Search button clicked:', query);
            performSearch(query);
        });

        // Ngăn chặn form submit
        document.getElementById('friendSearchForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const query = searchInput.value.trim();
            console.log('Form submitted:', query);
            performSearch(query);
        });
    });
</script>
@endpush
@endsection