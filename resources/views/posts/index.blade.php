@extends('layouts.app')

@section('title', 'Bảng tin')

@section('content')
<div class="container py-4">
    <h4 class="mb-4">Bảng tin</h4>
    
    @if($posts->count() > 0)
        <div class="row">
            @foreach($posts as $post)
                <div class="col-md-12 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ $post->title }}</h5>
                            <p class="card-text text-muted small">
                                Đăng bởi: {{ $post->user->name }} - 
                                {{ $post->created_at->format('d/m/Y H:i') }}
                            </p>
                            <p class="card-text">{{ Str::limit($post->content, 200) }}</p>
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('posts.show', $post) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> Xem chi tiết
                                </a>
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
            Chưa có bài viết nào được đăng. <a href="{{ route('posts.create') }}">Hãy là người đầu tiên đăng bài</a>
        </div>
    @endif
</div>
@endsection 