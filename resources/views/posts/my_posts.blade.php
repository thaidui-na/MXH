@extends('layouts.app')

@section('title', 'Bài viết của tôi')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Bài viết của tôi</h4>
        <a href="{{ route('posts.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tạo bài viết mới
        </a>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    
    @if($posts->count() > 0)
        <div class="row">
            @foreach($posts as $post)
                <div class="col-md-12 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <h5 class="card-title">{{ $post->title }}</h5>
                            </div>
                            <p class="card-text text-muted small">
                                Đăng ngày {{ $post->created_at->format('d/m/Y H:i') }}
                            </p>
                            <p class="card-text">{{ Str::limit($post->content, 200) }}</p>
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('posts.show', $post) }}" class="btn btn-sm btn-info me-2">
                                    <i class="fas fa-eye"></i> Xem
                                </a>
                                <a href="{{ route('posts.edit', $post) }}" class="btn btn-sm btn-warning me-2">
                                    <i class="fas fa-edit"></i> Sửa
                                </a>
                                <form action="{{ route('posts.destroy', $post) }}" method="POST" 
                                      onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài viết này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i> Xóa
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Hiển thị phân trang -->
        <div class="d-flex justify-content-center mt-4">
            {{ $posts->links() }}
        </div>
    @else
        <div class="alert alert-info">
            Bạn chưa có bài viết nào. <a href="{{ route('posts.create') }}">Tạo bài viết đầu tiên</a>
        </div>
    @endif
</div>
@endsection