@extends('layouts.app')

@section('title', 'Bình luận bài viết')

@section('content')
<div class="container py-4">
    <h4>Bình luận cho bài viết: <span class="text-primary">{{ $post->title ?? 'Bài viết' }}</span></h4>
    <hr>
    <!-- Form nhập bình luận -->
    <form action="{{ route('comments.store', $post->id) }}" method="POST" class="mb-4">
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
            <li class="list-group-item">
                <strong>{{ $comment->user->name ?? 'Ẩn danh' }}:</strong> {{ $comment->content }}
                <br>
                <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                @if(auth()->check() && (auth()->user()->is_admin || auth()->id() === $comment->user_id))
                    <a href="{{ route('comments.edit', $comment->id) }}" class="btn btn-sm btn-warning ms-2">Sửa</a>
                    <form action="{{ route('admin.comments.destroy', $comment->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bình luận này?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger ms-2">Xóa</button>
                    </form>
                @endif
            </li>
        @empty
            <li class="list-group-item text-muted">Chưa có bình luận nào.</li>
        @endforelse
    </ul>
</div>
@endsection
