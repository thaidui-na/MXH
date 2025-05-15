@extends('layouts.app')

@section('title', 'Bình luận bài viết')

@section('content')
<div class="container py-4">
    <h4>Bình luận cho bài viết: <span class="text-primary">{{ $post->title ?? 'Bài viết' }}</span></h4>
    <hr>
    <!-- Form nhập bình luận chính -->
    <form action="/posts/{{ $post->id }}/comments" method="POST" class="mb-4">
        @csrf
        <div class="mb-3">
            <label for="content" class="form-label">Nội dung bình luận</label>
            <textarea name="content" id="content" class="form-control" rows="3" required>{{ old('content') }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">Gửi bình luận</button>
    </form>

    <!-- Danh sách bình luận -->
    <h5 class="mt-4">Danh sách bình luận</h5>
    <ul class="list-group">
        @forelse($comments as $comment)
            @if(!$comment->parent_id) {{-- Chỉ hiển thị bình luận gốc --}}
                <li class="list-group-item">
                    <div class="comment-main">
                        <!-- Nội dung bình luận chính -->
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong>{{ $comment->user->name ?? 'Ẩn danh' }}:</strong> 
                                <span class="comment-content" id="comment-{{ $comment->id }}">
                                    {{ $comment->content }}
                                </span>
                                <br>
                                <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                            </div>
                            @if(auth()->id() == $comment->user_id)
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-warning me-1" 
                                            onclick="showEditForm({{ $comment->id }})">
                                        <i class="fas fa-edit"></i> Sửa
                                    </button>
                                    <form action="/comments/{{ $comment->id }}" method="POST" 
                                          class="d-inline" 
                                          onsubmit="return confirm('Bạn có chắc muốn xóa bình luận này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i> Xóa
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>

                        <!-- Form sửa bình luận -->
                        <div class="edit-form mt-2" id="edit-form-{{ $comment->id }}" style="display: none;">
                            <form action="/comments/{{ $comment->id }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="input-group">
                                    <textarea name="content" class="form-control" required>{{ $comment->content }}</textarea>
                                    <button type="submit" class="btn btn-primary">Lưu</button>
                                    <button type="button" class="btn btn-secondary" 
                                            onclick="hideEditForm({{ $comment->id }})">Hủy</button>
                                </div>
                            </form>
                        </div>

                        <!-- Nút trả lời -->
                        <div class="mt-2">
                            <button class="btn btn-sm btn-link text-primary p-0" 
                                    onclick="toggleReplyForm({{ $comment->id }})">
                                <i class="fas fa-reply"></i> Trả lời
                            </button>
                        </div>

                        <!-- Form trả lời -->
                        <div class="reply-form ms-4 mt-2" id="reply-form-{{ $comment->id }}" style="display: none;">
                            <form action="/comments/{{ $comment->id }}/replies" method="POST">
                                @csrf
                                <div class="input-group">
                                    <textarea name="content" class="form-control" rows="2" 
                                              placeholder="Nhập nội dung trả lời..." required></textarea>
                                    <button type="submit" class="btn btn-primary">Gửi</button>
                                    <button type="button" class="btn btn-secondary" 
                                            onclick="toggleReplyForm({{ $comment->id }})">Hủy</button>
                                </div>
                            </form>
                        </div>

                        <!-- Hiển thị các câu trả lời -->
                        <div class="replies ms-4 mt-2">
                            @foreach($comments as $reply)
                                @if($reply->parent_id == $comment->id)
                                    <div class="reply border-start ps-3 mb-2">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <strong>{{ $reply->user->name }}:</strong> 
                                                {{ $reply->content }}
                                                <br>
                                                <small class="text-muted">{{ $reply->created_at->diffForHumans() }}</small>
                                            </div>
                                            @if(auth()->id() == $reply->user_id)
                                                <div class="btn-group">
                                                    <form action="/comments/{{ $reply->id }}" method="POST" 
                                                          onsubmit="return confirm('Bạn có chắc muốn xóa trả lời này?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </li>
            @endif
        @empty
            <li class="list-group-item text-muted">Chưa có bình luận nào.</li>
        @endforelse
    </ul>
</div>

@push('scripts')
<script>
function showEditForm(commentId) {
    document.getElementById(`edit-form-${commentId}`).style.display = 'block';
    document.getElementById(`comment-${commentId}`).style.display = 'none';
}

function hideEditForm(commentId) {
    document.getElementById(`edit-form-${commentId}`).style.display = 'none';
    document.getElementById(`comment-${commentId}`).style.display = 'inline';
}

function toggleReplyForm(commentId) {
    const replyForm = document.getElementById(`reply-form-${commentId}`);
    replyForm.style.display = replyForm.style.display === 'none' ? 'block' : 'none';
}
</script>
@endpush
@endsection
