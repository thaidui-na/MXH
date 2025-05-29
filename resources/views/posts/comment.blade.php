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
            <form action="{{ route('comments.store', $post->id) }}" method="POST" class="mb-4">
                @csrf
                <div class="form-group">
                    <label for="content">Viết bình luận</label>
                    <textarea class="form-control @error('content') is-invalid @enderror" 
                              id="content" 
                              name="content" 
                              rows="3" 
                              required>{{ old('content') }}</textarea>
                    @error('content')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
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
                                                    <a href="{{ route('comments.edit', $comment->id) }}" class="dropdown-item">
                                                        <i class="fas fa-edit"></i> Sửa
                                                    </a>
                                                </li>
                                                <li>
                                                    <form action="{{ route('comments.destroy', $comment->id) }}" method="POST" 
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
                                    <form action="{{ route('comments.reply', $comment->id) }}" method="POST">
                                        @csrf
                                        <div class="input-group">
                                            <textarea class="form-control @error('content') is-invalid @enderror" 
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
                                        @error('content')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
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
                                                        <form action="{{ route('comments.destroy', $reply->id) }}" method="POST" 
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
function showReplyForm(commentId) {
    document.getElementById(`reply-form-${commentId}`).style.display = 'block';
}

function hideReplyForm(commentId) {
    document.getElementById(`reply-form-${commentId}`).style.display = 'none';
}
</script>
@endpush
@endsection
