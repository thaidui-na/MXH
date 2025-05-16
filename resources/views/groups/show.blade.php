@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            {{-- Cover và avatar nhóm --}}
            <div class="position-relative mb-4">
                <img src="{{ $group->cover_image ? asset('storage/' . $group->cover_image) : asset('images/default-cover.jpg') }}"
                     onerror="this.onerror=null;this.src='{{ asset('images/default-cover.jpg') }}';"
                     class="w-100 rounded" style="height: 220px; object-fit: cover;">
                <img src="{{ $group->avatar ? asset('storage/' . $group->avatar) : asset('images/default-avatar.jpg') }}"
                     onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.jpg') }}';"
                     class="rounded-circle border border-3 border-white position-absolute" style="width: 110px; height: 110px; left: 40px; bottom: -55px; object-fit: cover; background: #fff;">
            </div>
            <div class="d-flex align-items-center mb-4" style="margin-top: 40px;">
                <div class="flex-grow-1">
                    <h3 class="mb-1">{{ $group->name }}</h3>
                    <div class="text-muted mb-1">{{ $group->description }}</div>
                    <div class="d-flex align-items-center gap-3">
                        <span><i class="fas fa-users"></i> {{ $group->members->count() }} thành viên</span>
                        <span class="badge {{ $group->is_private ? 'bg-secondary' : 'bg-success' }}">{{ $group->is_private ? 'Riêng tư' : 'Công khai' }}</span>
                    </div>
                </div>
                <div>
                    @if($group->created_by == auth()->id())
                        <a href="{{ route('groups.edit', $group->id) }}" class="btn btn-outline-primary me-2">Chỉnh sửa nhóm</a>
                        <form action="{{ route('groups.destroy', $group->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa nhóm này? Hành động này không thể hoàn tác!');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">Xóa nhóm</button>
                        </form>
                    @endif
                    @if($group->members->where('user_id', auth()->id())->count() == 0)
                        <form method="POST" action="{{ route('groups.join', $group->id) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success">Tham gia nhóm</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('groups.leave', $group->id) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-danger">Rời nhóm</button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- Tabs: Thảo luận, Thành viên --}}
            <ul class="nav nav-tabs mb-4" id="groupTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="discussion-tab" data-bs-toggle="tab" data-bs-target="#discussion-pane" type="button" role="tab" aria-controls="discussion-pane" aria-selected="true">
                        <i class="fas fa-comments"></i> Thảo luận
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="members-tab" data-bs-toggle="tab" data-bs-target="#members-pane" type="button" role="tab" aria-controls="members-pane" aria-selected="false">
                        <i class="fas fa-user-friends"></i> Thành viên
                    </button>
                </li>
            </ul>
            <div class="tab-content" id="groupTabContent">
                {{-- Tab Thảo luận --}}
                <div class="tab-pane fade show active" id="discussion-pane" role="tabpanel" aria-labelledby="discussion-tab">
                    @if($group->members->where('user_id', auth()->id())->count() > 0)
                        <form method="POST" action="{{ route('groups.post', $group->id) }}" class="mb-4">
                            @csrf
                            <div class="mb-3">
                                <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" placeholder="Tiêu đề bài viết" value="{{ old('title') }}" required>
                                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <textarea class="form-control @error('content') is-invalid @enderror" name="content" rows="3" placeholder="Bạn muốn chia sẻ điều gì?" required>{{ old('content') }}</textarea>
                                @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Đăng bài</button>
                            </div>
                        </form>
                    @endif
                    @if($group->posts->count() > 0)
                        @foreach($group->posts as $post)
                            <div class="card mb-3 post-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $post->user->avatar ? Storage::url($post->user->avatar) : asset('images/default-avatar.jpg') }}" 
                                                 class="rounded-circle me-2" 
                                                 style="width: 40px; height: 40px; object-fit: cover;">
                                            <div>
                                                <h6 class="mb-0">{{ $post->user->name }}</h6>
                                                <small class="text-muted">{{ $post->created_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                        @if($post->user_id == auth()->id() || $group->hasAdmin(auth()->id()))
                                            <div class="dropdown">
                                                <button class="btn btn-link text-dark" type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    @if($post->user_id == auth()->id())
                                                        <li>
                                                            <form action="{{ route('groups.posts.destroy', ['group' => $group->id, 'post' => $post->id]) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa bài viết này?')">
                                                                    <i class="fas fa-trash"></i> Xóa
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif
                                                    @if($group->hasAdmin(auth()->id()))
                                                        <li>
                                                            <form action="{{ route('groups.posts.destroy', ['group' => $group->id, 'post' => $post->id]) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa bài viết này?')">
                                                                    <i class="fas fa-trash"></i> Xóa bài viết
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                    <p class="card-text">{{ $post->content }}</p>
                                    @if($post->image)
                                        <img src="{{ Storage::url($post->image) }}" class="img-fluid rounded mb-3" alt="Post image">
                                    @endif
                                    <div class="post-actions mt-2">
                                        <form class="d-inline like-form">
                                            @csrf
                                            <button type="button" class="btn btn-sm {{ $post->isLikedBy(auth()->id()) ? 'btn-primary' : 'btn-outline-primary' }} like-button" 
                                                data-post-id="{{ $post->id }}" 
                                                data-group-id="{{ $group->id }}">
                                                <i class="fas fa-heart"></i> 
                                                <span class="like-count">{{ $post->likes()->count() }}</span>
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-outline-secondary ms-2 comment-toggle" data-post-id="{{ $post->id }}">
                                            <i class="fas fa-comment"></i> 
                                            <span class="comment-count">{{ $post->comments()->count() }}</span>
                                        </button>
                                        <div>
                                            <form action="{{ route('group-posts.favorites.toggle', $post) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-link {{ $post->isFavoritedBy(auth()->id()) ? 'text-danger' : 'text-muted' }} p-0">
                                                    <i class="{{ $post->isFavoritedBy(auth()->id()) ? 'fas' : 'far' }} fa-heart"></i>
                                                    {{ $post->isFavoritedBy(auth()->id()) ? 'Đã lưu' : 'Lưu bài viết' }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    {{-- Phần bình luận --}}
                                    <div class="comments-section mt-3" id="comments-{{ $post->id }}" style="display: none;">
                                        {{-- Form thêm bình luận mới --}}
                                        @if($group->members->where('user_id', auth()->id())->count() > 0)
                                            <form class="comment-form mb-3" data-post-id="{{ $post->id }}">
                                                @csrf
                                                <div class="input-group">
                                                    <input type="text" class="form-control comment-input" placeholder="Viết bình luận..." required>
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fas fa-paper-plane"></i>
                                                    </button>
                                                </div>
                                            </form>
                                        @endif

                                        {{-- Danh sách bình luận --}}
                                        <div class="comments-list">
                                            @foreach($post->comments()->with('user')->latest()->get() as $comment)
                                                <div class="comment-item mb-2">
                                                    <div class="d-flex">
                                                        <img src="{{ $comment->user->avatar ? Storage::url($comment->user->avatar) : asset('images/default-avatar.jpg') }}" 
                                                             class="rounded-circle me-2" 
                                                             style="width: 32px; height: 32px; object-fit: cover;">
                                                        <div class="flex-grow-1">
                                                            <div class="bg-light rounded p-2">
                                                                <div class="d-flex justify-content-between">
                                                                    <strong>{{ $comment->user->name }}</strong>
                                                                    <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                                                                </div>
                                                                <p class="mb-1">{{ $comment->content }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-center">Chưa có bài viết nào.</p>
                    @endif
                </div>
                {{-- Tab Thành viên --}}
                <div class="tab-pane fade" id="members-pane" role="tabpanel" aria-labelledby="members-tab">
                    @if($group->hasAdmin(auth()->id()))
                        <div class="mb-3">
                            <a href="{{ route('groups.members', $group) }}" class="btn btn-primary">
                                <i class="fas fa-users-cog"></i> Quản lý thành viên
                            </a>
                        </div>
                    @endif
                    <div class="row">
                        @foreach($group->members as $member)
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-body d-flex align-items-center">
                                        <img src="{{ $member->user->avatar_url }}" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                        <div>
                                            <strong>{{ $member->user->name }}</strong><br>
                                            <small class="text-muted">{{ $member->role }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý sự kiện click cho nút like
    document.querySelectorAll('.like-button').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const postId = this.dataset.postId;
            const groupId = this.dataset.groupId;
            const token = document.querySelector('meta[name="csrf-token"]').content;
            const button = this;
            
            console.log('Sending like request:', {
                postId,
                groupId,
                token
            });

            fetch(`/groups/${groupId}/posts/${postId}/like`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                credentials: 'same-origin'
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json().catch(() => {
                    throw new Error('Invalid JSON response');
                });
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    // Cập nhật số lượt like
                    const likeCount = button.querySelector('.like-count');
                    if (likeCount) {
                        likeCount.textContent = data.likeCount;
                    }
                    
                    // Cập nhật trạng thái nút
                    if (data.isLiked) {
                        button.classList.remove('btn-outline-primary');
                        button.classList.add('btn-primary');
                    } else {
                        button.classList.remove('btn-primary');
                        button.classList.add('btn-outline-primary');
                    }
                } else if (data.error) {
                    console.error('Server error:', data.error);
                    alert(data.error);
                }
            })
            .catch(error => {
                console.error('Error details:', error);
                alert('Có lỗi xảy ra khi thực hiện thao tác like: ' + error.message);
            });
        });
    });

    // Xử lý hiển thị/ẩn phần bình luận
    document.querySelectorAll('.comment-toggle').forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.dataset.postId;
            const commentsSection = document.getElementById(`comments-${postId}`);
            commentsSection.style.display = commentsSection.style.display === 'none' ? 'block' : 'none';
        });
    });

    // Xử lý thêm bình luận mới
    document.querySelectorAll('.comment-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const postId = this.dataset.postId;
            const input = this.querySelector('.comment-input');
            const content = input.value.trim();
            
            if (!content) return;

            const token = document.querySelector('meta[name="csrf-token"]').content;
            
            fetch(`/groups/posts/${postId}/comments`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ content })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Thêm bình luận mới vào danh sách
                    const commentsList = this.closest('.comments-section').querySelector('.comments-list');
                    const newComment = createCommentElement(data.comment);
                    commentsList.insertBefore(newComment, commentsList.firstChild);
                    
                    // Cập nhật số lượng bình luận
                    const commentCount = document.querySelector(`.comment-toggle[data-post-id="${postId}"] .comment-count`);
                    commentCount.textContent = parseInt(commentCount.textContent) + 1;
                    
                    // Xóa nội dung input
                    input.value = '';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi thêm bình luận');
            });
        });
    });

    // Hàm tạo element bình luận mới
    function createCommentElement(comment) {
        const div = document.createElement('div');
        div.className = 'comment-item mb-2';
        div.innerHTML = `
            <div class="d-flex">
                <img src="${comment.user.avatar || '/images/default-avatar.jpg'}" 
                     class="rounded-circle me-2" 
                     style="width: 32px; height: 32px; object-fit: cover;">
                <div class="flex-grow-1">
                    <div class="bg-light rounded p-2">
                        <div class="d-flex justify-content-between">
                            <strong>${comment.user.name}</strong>
                            <small class="text-muted">Vừa xong</small>
                        </div>
                        <p class="mb-1">${comment.content}</p>
                    </div>
                </div>
            </div>
        `;
        return div;
    }
});
</script>
@endpush 