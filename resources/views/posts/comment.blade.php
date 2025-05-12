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
            </li>
        @empty
            <li class="list-group-item text-muted">Chưa có bình luận nào.</li>
        @endforelse
    </ul>
</div>
@endsection
