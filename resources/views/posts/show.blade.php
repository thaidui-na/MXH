@extends('layouts.app')

@section('title', $post->title)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success mb-3">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    <h3 class="card-title">{{ $post->title }}</h3>
                    <div class="d-flex justify-content-between my-3">
                        <p class="card-text text-muted">
                            <i class="fas fa-user"></i> {{ $post->user->name }} <br>
                            <i class="fas fa-calendar"></i> {{ $post->created_at->format('d/m/Y H:i') }}
                        </p>
                        @if($post->user_id !== auth()->id())
                            <button type="button" class="btn btn-sm report-button" data-bs-toggle="modal" data-bs-target="#reportModal">
                                <i class="fas fa-flag"></i> Báo cáo
                            </button>
                        @endif
                    </div>
                    
                    <hr>
                    
                    <div class="card-text mb-4">
                        {!! nl2br(e($post->content)) !!}
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="post-actions">
                                <button class="btn btn-sm btn-outline-primary like-button" data-post-id="{{ $post->id }}">
                                    <i class="fas fa-heart"></i> <span class="likes-count">{{ $post->likes_count }}</span>
                                </button>
                                <button class="btn btn-sm btn-outline-primary share-button" data-bs-toggle="modal" data-bs-target="#shareModal">
                                    <i class="fas fa-share"></i> <span class="shares-count">{{ $post->shares_count }}</span>
                                </button>
                            </div>
                            <a href="{{ route('comments.index', $post->id) }}" class="btn btn-sm comment-button ms-2">
                                <i class="fas fa-comment"></i>
                                <span class="comment-count">{{ $post->comments()->count() }}</span>
                            </a>
                        </div>
                        <div>
                            <a href="{{ route('posts.index') }}" class="btn btn-sm back-button me-2">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                            @if($post->user_id === auth()->id())
                                <a href="{{ route('posts.edit', $post) }}" class="btn btn-sm edit-button me-2">
                                    <i class="fas fa-edit"></i> Sửa
                                </a>
                                <form action="{{ route('posts.destroy', $post) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài viết này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm delete-button">
                                        <i class="fas fa-trash"></i> Xóa
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Báo cáo -->
<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reportModalLabel">Báo cáo bài viết</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('posts.report', $post) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Lý do báo cáo:</label>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="reason" id="reason1" value="Nội dung không phù hợp" required>
                            <label class="form-check-label" for="reason1">
                                Nội dung không phù hợp
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="reason" id="reason2" value="Spam" required>
                            <label class="form-check-label" for="reason2">
                                Spam
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="reason" id="reason3" value="Vi phạm bản quyền" required>
                            <label class="form-check-label" for="reason3">
                                Vi phạm bản quyền
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="reason" id="reason4" value="Quấy rối" required>
                            <label class="form-check-label" for="reason4">
                                Quấy rối
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="reason" id="reason5" value="Bạo lực" required>
                            <label class="form-check-label" for="reason5">
                                Bạo lực
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="reason" id="reason6" value="other" required>
                            <label class="form-check-label" for="reason6">
                                Lý do khác
                            </label>
                        </div>
                        <div class="mt-2 d-none" id="otherReasonContainer">
                            <textarea class="form-control" name="other_reason" rows="2" placeholder="Vui lòng mô tả lý do báo cáo..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-danger">Gửi báo cáo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Share Modal -->
<div class="modal fade" id="shareModal" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="shareModalLabel">Chia sẻ bài viết</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="shareForm">
                    <div class="mb-3">
                        <label for="shareCaption" class="form-label">Thêm nội dung (tùy chọn)</label>
                        <textarea class="form-control" id="shareCaption" rows="3" maxlength="500"></textarea>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-primary" onclick="sharePost()">
                            <i class="fas fa-share"></i> Chia sẻ lên trang cá nhân
                        </button>
                        <button type="button" class="btn btn-outline-primary" onclick="showMessageShare()">
                            <i class="fas fa-paper-plane"></i> Gửi qua tin nhắn
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Message Share Modal -->
<div class="modal fade" id="messageShareModal" tabindex="-1" aria-labelledby="messageShareModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="messageShareModalLabel">Gửi qua tin nhắn</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" class="form-control" id="messageSearch" placeholder="Tìm kiếm người dùng...">
                </div>
                <div id="messageUsers" class="list-group">
                    <!-- Danh sách người dùng sẽ được thêm vào đây -->
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý scroll đến bình luận cụ thể nếu có hash trong URL
    const hash = window.location.hash;
    if (hash && hash.startsWith('#comment-')) {
        const commentId = hash.replace('#comment-', '');
        const commentElement = document.getElementById(`comment-${commentId}`);
        if (commentElement) {
            // Thêm highlight cho bình luận
            commentElement.classList.add('highlight-comment');
            // Scroll đến bình luận
            commentElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
            // Xóa highlight sau 2 giây
            setTimeout(() => {
                commentElement.classList.remove('highlight-comment');
            }, 2000);
        }
    }

    // Handle other reason textarea visibility
    const otherRadio = document.getElementById('reason6');
    const otherReasonContainer = document.getElementById('otherReasonContainer');
    
    document.querySelectorAll('input[name="reason"]').forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'other') {
                otherReasonContainer.classList.remove('d-none');
            } else {
                otherReasonContainer.classList.add('d-none');
            }
        });
    });

    // Handle form submission
    const reportForm = document.querySelector('#reportModal form');
    if (reportForm) {
        reportForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const selectedReason = document.querySelector('input[name="reason"]:checked');
            if (!selectedReason) {
                alert('Vui lòng chọn lý do báo cáo');
                return;
            }

            if (selectedReason.value === 'other') {
                const otherReason = document.querySelector('textarea[name="other_reason"]').value.trim();
                if (!otherReason) {
                    alert('Vui lòng nhập lý do báo cáo');
                    return;
                }
            }

            // Submit the form
            this.submit();
        });
    }

    // Xử lý nút like
    const likeButton = document.querySelector('.like-button');
    if (likeButton) {
        likeButton.addEventListener('click', function() {
            const postId = this.dataset.postId;
            fetch(`/posts/${postId}/like`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                const likeCount = this.querySelector('.likes-count');
                if (likeCount) {
                    likeCount.textContent = data.likesCount;
                }
                
                if (data.liked) {
                    this.classList.remove('btn-outline-primary');
                    this.classList.add('btn-primary');
                } else {
                    this.classList.remove('btn-primary');
                    this.classList.add('btn-outline-primary');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    }
});

function sharePost() {
    const caption = document.getElementById('shareCaption').value;

    fetch(`/posts/{{ $post->id }}/share`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ caption })
    })
    .then(async response => {
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            const data = await response.json();
            if (data.success) {
                document.querySelector('.shares-count').textContent = parseInt(document.querySelector('.shares-count').textContent) + 1;
                bootstrap.Modal.getInstance(document.getElementById('shareModal')).hide();
                alert(data.message);
            } else {
                alert(data.message || 'Có lỗi xảy ra khi chia sẻ bài viết');
            }
        } else {
            // Nếu không phải JSON, có thể là bị logout hoặc lỗi CSRF
            if (response.status === 419) {
                alert('Phiên đăng nhập đã hết hạn hoặc lỗi CSRF. Vui lòng tải lại trang và thử lại.');
            } else if (response.status === 401) {
                alert('Bạn cần đăng nhập để thực hiện chức năng này.');
            } else {
                alert('Có lỗi xảy ra (không nhận được dữ liệu hợp lệ từ server).');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi chia sẻ bài viết. Vui lòng thử lại sau.');
    });
}

function showMessageShare() {
    // Đóng modal chia sẻ
    bootstrap.Modal.getInstance(document.getElementById('shareModal')).hide();
    // Mở modal tin nhắn
    new bootstrap.Modal(document.getElementById('messageShareModal')).show();
}

// Tìm kiếm người dùng khi nhập
document.getElementById('messageSearch').addEventListener('input', function(e) {
    const query = e.target.value.trim();
    if (query.length < 2) return;

    fetch(`/api/users/search?q=${encodeURIComponent(query)}`, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(async response => {
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            const data = await response.json();
            const usersList = document.getElementById('messageUsers');
            usersList.innerHTML = '';
            
            if (data.users && data.users.length > 0) {
                data.users.forEach(user => {
                    const userElement = document.createElement('button');
                    userElement.className = 'list-group-item list-group-item-action d-flex align-items-center';
                    userElement.innerHTML = `
                        <img src="${user.avatar || '/images/default-avatar.jpg'}" 
                             class="rounded-circle me-2" 
                             style="width: 40px; height: 40px; object-fit: cover;"
                             onerror="this.src='/images/default-avatar.jpg'">
                        <div>
                            <div class="fw-bold">${user.name}</div>
                            <small class="text-muted">${user.email}</small>
                        </div>
                    `;
                    userElement.onclick = () => shareToMessage(user.id);
                    usersList.appendChild(userElement);
                });
            } else {
                usersList.innerHTML = '<div class="list-group-item text-center text-muted">Không tìm thấy người dùng</div>';
            }
        } else {
            if (response.status === 419) {
                alert('Phiên đăng nhập đã hết hạn. Vui lòng tải lại trang và thử lại.');
            } else if (response.status === 401) {
                alert('Bạn cần đăng nhập để thực hiện chức năng này.');
            } else {
                alert('Có lỗi xảy ra khi tìm kiếm người dùng.');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi tìm kiếm người dùng. Vui lòng thử lại sau.');
    });
});

function shareToMessage(userId) {
    fetch(`/posts/{{ $post->id }}/share-to-message`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ user_id: userId })
    })
    .then(async response => {
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            const data = await response.json();
            if (data.success) {
                // Đóng modal
                bootstrap.Modal.getInstance(document.getElementById('messageShareModal')).hide();
                // Hiển thị thông báo thành công
                alert(data.message);
            } else {
                alert(data.message || 'Có lỗi xảy ra khi gửi bài viết qua tin nhắn');
            }
        } else {
            // Nếu không phải JSON, có thể là bị logout hoặc lỗi CSRF
            if (response.status === 419) {
                alert('Phiên đăng nhập đã hết hạn hoặc lỗi CSRF. Vui lòng tải lại trang và thử lại.');
            } else if (response.status === 401) {
                alert('Bạn cần đăng nhập để thực hiện chức năng này.');
            } else {
                alert('Có lỗi xảy ra khi gửi bài viết qua tin nhắn.');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi gửi bài viết qua tin nhắn. Vui lòng thử lại sau.');
    });
}
</script>
<style>
.highlight-comment {
    animation: highlight 2s ease-out;
}

@keyframes highlight {
    0% {
        background-color: #fff3cd;
    }
    100% {
        background-color: transparent;
    }
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

.back-button {
    transition: all 0.3s ease;
    padding: 8px 14px;
    font-size: 0.875rem;
    border-radius: 20px;
    border: 1px solid #6c757d;
    background-color: transparent;
    color: #6c757d;
    text-align: center;
}

.back-button i {
    font-size: 1.1rem;
    margin-right: 0.3rem;
    transition: all 0.3s ease;
}

.back-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    background-color: #f8f9fa;
    color: #495057;
}

.edit-button {
    transition: all 0.3s ease;
    padding: 8px 14px;
    font-size: 0.875rem;
    border-radius: 20px;
    border: 1px solid #ffc107;
    background-color: transparent;
    color: #ffc107;
    text-align: center;
}

.edit-button i {
    font-size: 1.1rem;
    margin-right: 0.3rem;
    transition: all 0.3s ease;
}

.edit-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    background-color: #fff3cd;
    color: #856404;
}

.delete-button {
    transition: all 0.3s ease;
    padding: 8px 14px;
    font-size: 0.875rem;
    border-radius: 20px;
    border: 1px solid #dc3545;
    background-color: transparent;
    color: #dc3545;
    text-align: center;
}

.delete-button i {
    font-size: 1.1rem;
    margin-right: 0.3rem;
    transition: all 0.3s ease;
}

.delete-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    background-color: #ffebee;
    color: #dc3545;
}

.report-button {
    transition: all 0.3s ease;
    padding: 8px 14px;
    font-size: 0.875rem;
    border-radius: 20px;
    border: 1px solid #dc3545;
    background-color: transparent;
    color: #dc3545;
    text-align: center;
}

.report-button i {
    font-size: 1.1rem;
    margin-right: 0.3rem;
    transition: all 0.3s ease;
}

.report-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    background-color: #ffebee;
    color: #dc3545;
}

.report-button:hover i {
    transform: scale(1.1);
}
</style>
@endpush

@endsection 
