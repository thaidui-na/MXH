@extends('layouts.app')

@section('title', 'Bài viết yêu thích của tôi')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Bài viết yêu thích của tôi</h4>
                </div>
                <div class="card-body">
                    @if($favoritedPosts->count() > 0)
                        @foreach($favoritedPosts as $post)
                            <div class="card mb-3 shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title"><a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a></h5>
                                    <p class="card-text text-muted small">
                                        Đăng bởi <a href="{{ route('posts.user_posts', $post->user) }}">{{ $post->user->name }}</a> vào ngày {{ $post->created_at->format('d/m/Y H:i') }}
                                    </p>
                                    {{-- Hiển thị một phần nội dung nếu cần --}}
                                    <p class="card-text">{{ Str::limit($post->content, 150) }}</p>
                                </div>
                            </div>
                        @endforeach

                        {{-- Hiển thị phân trang --}}
                        <div class="d-flex justify-content-center">
                            {{ $favoritedPosts->links() }}
                        </div>
                    @else
                        <div class="alert alert-info text-center" role="alert">
                            Bạn chưa lưu bài viết yêu thích nào.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 