@extends('admin.layout')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Chỉnh sửa bài viết</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.posts.update', $post->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label class="form-label">Tiêu đề</label>
                <input type="text" name="title" class="form-control" value="{{ $post->title }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Nội dung</label>
                <textarea name="content" class="form-control" rows="5" required>{{ $post->content }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary">Cập nhật</button>
            <a href="{{ route('admin.posts') }}" class="btn btn-secondary">Hủy</a>
        </form>
    </div>
</div>
@endsection
