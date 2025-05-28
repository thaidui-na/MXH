@extends('layouts.app')

@section('title', $post->title)

@push('styles')
<style>
    .like-button {
        background: none;
        border: none;
        padding: 5px 10px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .like-button:hover {
        transform: scale(1.1);
    }

    .like-button.active .fa-heart {
        color: #dc3545;
    }

    .like-button .fa-heart {
        transition: all 0.3s ease;
    }

    .like-count {
        margin-left: 5px;
        font-weight: 500;
    }
</style>
@endpush

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
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            
            fetch(`/posts/${postId}/like`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                const likeCount = this.querySelector('.like-count');
                const heartIcon = this.querySelector('.fa-heart');
                
                if (data.liked) {
                    this.classList.add('active');
                    heartIcon.classList.add('text-danger');
                } else {
                    this.classList.remove('active');
                    heartIcon.classList.remove('text-danger');
                }
                
                likeCount.textContent = data.likesCount;
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    }
});
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
