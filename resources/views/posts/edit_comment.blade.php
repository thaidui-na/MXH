@extends('layouts.app')

@section('title', 'Sửa bình luận')

@section('content')
<div class="container py-4">
    <h4>Sửa bình luận</h4>
    <form action="{{ route('comments.update', $comment->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="content" class="form-label">Nội dung</label>
            <textarea name="content" id="content" class="form-control" rows="3" required>{{ old('content', $comment->content) }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">Cập nhật</button>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>
@endsection
