@extends('layouts.app')

@section('title', 'Bình luận bài viết')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Bình luận cho bài viết: {{ $post->title }}</h5>
            <a href="/posts/{{ $post->id }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Quay lại bài viết
            </a>
        </div>
        
        <div class="card-body">
            <!-- Form bình luận mới -->
            <form id="commentForm" class="mb-4">
                @csrf
                <div class="form-group">
                    <label for="content">Viết bình luận</label>
                    <textarea class="form-control @error('content') is-invalid @enderror" 
                              id="content" 
                              name="content" 
                              rows="3" 
                              required>{{ old('content') }}</textarea>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="text-end mt-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Gửi bình luận
                    </button>
                </div>
            </form>

            <!-- Danh sách bình luận -->
            <div class="comments-list">
                @forelse($comments as $comment)
                    <div class="comment-item mb-3" id="comment-{{ $comment->id }}">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div class="d-flex gap-2">
                                        <img src="{{ $comment->user->avatar ? asset('storage/' . $comment->user->avatar) : asset('images/default-avatar.jpg') }}" 
                                             class="rounded-circle" 
                                             width="40" 
                                             height="40"
                                             alt="{{ $comment->user->name }}">
                                        <div>
                                            <h6 class="mb-0">{{ $comment->user->name }}</h6>
                                            <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                    @if(auth()->id() === $comment->user_id)
                                        <div class="dropdown">
                                            <button class="btn btn-link text-dark p-0" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <button class="dropdown-item" onclick="editComment({{ $comment->id }})">
                                                        <i class="fas fa-edit"></i> Sửa
                                                    </button>
                                                </li>
                                                <li>
                                                    <form action="/comments/{{ $comment->id }}" method="POST" 
                                                          onsubmit="return confirm('Bạn có chắc muốn xóa bình luận này?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="fas fa-trash"></i> Xóa
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="comment-content mt-2">
                                    <p class="mb-2">{{ $comment->content }}</p>
                                    <button class="btn btn-sm btn-link p-0" 
                                            onclick="showReplyForm({{ $comment->id }})">
                                        <i class="fas fa-reply"></i> Trả lời
                                    </button>
                                </div>

                                <!-- Form trả lời (ẩn) -->
                                <div class="reply-form mt-3" id="reply-form-{{ $comment->id }}" style="display: none;">
                                    <form action="/comments/{{ $comment->id }}/reply" method="POST">
                                        @csrf
                                        <div class="input-group">
                                            <textarea class="form-control" 
                                                      name="content" 
                                                      rows="1" 
                                                      placeholder="Viết trả lời..." 
                                                      required></textarea>
                                            <button type="submit" class="btn btn-primary">Gửi</button>
                                            <button type="button" 
                                                    class="btn btn-secondary" 
                                                    onclick="hideReplyForm({{ $comment->id }})">
                                                Hủy
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Danh sách trả lời -->
                                @if($comment->replies && $comment->replies->count() > 0)
                                    <div class="replies-list mt-3 ms-4 border-start ps-3">
                                        @foreach($comment->replies as $reply)
                                            <div class="reply-item mb-2">
                                                <div class="d-flex gap-2">
                                                    <img src="{{ $reply->user->avatar ? asset('storage/' . $reply->user->avatar) : asset('images/default-avatar.jpg') }}" 
                                                         class="rounded-circle" 
                                                         width="32" 
                                                         height="32"
                                                         alt="{{ $reply->user->name }}">
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-0">{{ $reply->user->name }}</h6>
                                                        <small class="text-muted">{{ $reply->created_at->diffForHumans() }}</small>
                                                        <p class="mb-0">{{ $reply->content }}</p>
                                                    </div>
                                                    @if(auth()->id() === $reply->user_id)
                                                        <form action="/comments/{{ $reply->id }}" method="POST" 
                                                              class="d-inline"
                                                              onsubmit="return confirm('Bạn có chắc muốn xóa trả lời này?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-link text-danger p-0">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted">
                        <p>Chưa có bình luận nào. Hãy là người đầu tiên bình luận!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const commentForm = document.getElementById('commentForm');
    const contentInput = document.getElementById('content');
    const commentsList = document.querySelector('.comments-list');

    commentForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const content = contentInput.value.trim();
        if (!content) return;

        const token = document.querySelector('meta[name="csrf-token"]').content;
        
        fetch(`/posts/{{ $post->id }}/comments`, {
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
                const commentHtml = createCommentElement(data.comment);
                if (commentsList.querySelector('.text-center')) {
                    commentsList.innerHTML = '';
                }
                commentsList.insertAdjacentHTML('afterbegin', commentHtml);
                
                // Xóa nội dung input
                contentInput.value = '';
                
                // Xóa thông báo lỗi nếu có
                contentInput.classList.remove('is-invalid');
                contentInput.nextElementSibling.textContent = '';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            contentInput.classList.add('is-invalid');
            contentInput.nextElementSibling.textContent = 'Có lỗi xảy ra khi gửi bình luận';
        });
    });

    function createCommentElement(comment) {
        return `
            <div class="comment-item mb-3" id="comment-${comment.id}">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="d-flex gap-2">
                                <img src="${comment.user.avatar ? '/storage/' + comment.user.avatar : '/images/default-avatar.jpg'}" 
                                     class="rounded-circle" 
                                     width="40" 
                                     height="40"
                                     alt="${comment.user.name}">
                                <div>
                                    <h6 class="mb-0">${comment.user.name}</h6>
                                    <small class="text-muted">Vừa xong</small>
                                </div>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-link text-dark p-0" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <button class="dropdown-item" onclick="editComment(${comment.id})">
                                            <i class="fas fa-edit"></i> Sửa
                                        </button>
                                    </li>
                                    <li>
                                        <form action="/comments/${comment.id}" method="POST" 
                                              onsubmit="return confirm('Bạn có chắc muốn xóa bình luận này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fas fa-trash"></i> Xóa
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="comment-content mt-2">
                            <p class="mb-2">${comment.content}</p>
                            <button class="btn btn-sm btn-link p-0" 
                                    onclick="showReplyForm(${comment.id})">
                                <i class="fas fa-reply"></i> Trả lời
                            </button>
                        </div>

                        <div class="reply-form mt-3" id="reply-form-${comment.id}" style="display: none;">
                            <form action="/comments/${comment.id}/reply" method="POST">
                                @csrf
                                <div class="input-group">
                                    <textarea class="form-control" 
                                              name="content" 
                                              rows="1" 
                                              placeholder="Viết trả lời..." 
                                              required></textarea>
                                    <button type="submit" class="btn btn-primary">Gửi</button>
                                    <button type="button" 
                                            class="btn btn-secondary" 
                                            onclick="hideReplyForm(${comment.id})">
                                        Hủy
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
});

function showReplyForm(commentId) {
    document.getElementById(`reply-form-${commentId}`).style.display = 'block';
}

function hideReplyForm(commentId) {
    document.getElementById(`reply-form-${commentId}`).style.display = 'none';
}

function editComment(commentId) {
    // Implement edit functionality
}
</script>
@endpush
@endsection
