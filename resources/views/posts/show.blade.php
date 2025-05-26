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
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#reportModal">
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
                            <button class="btn btn-sm like-button {{ $post->isLikedBy(auth()->id()) ? 'btn-primary' : 'btn-outline-primary' }}"
                                    data-post-id="{{ $post->id }}">
                                <i class="fas fa-heart"></i>
                                <span class="like-count">{{ $post->getLikesCount() }}</span>
                            </button>
                            <a href="{{ route('comments.index', $post->id) }}" class="btn btn-sm btn-secondary ms-2">
                                <i class="fas fa-comments"></i> Bình luận
                            </a>
                        </div>
                        <div>
                            <a href="{{ route('posts.index') }}" class="btn btn-secondary me-2">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                            @if($post->user_id === auth()->id())
                                <a href="{{ route('posts.edit', $post) }}" class="btn btn-warning me-2">
                                    <i class="fas fa-edit"></i> Sửa
                                </a>
                                <form action="{{ route('posts.destroy', $post) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài viết này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
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
                const likeCount = this.querySelector('.like-count');
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
</script>
@endpush

@endsection 
