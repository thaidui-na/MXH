@extends('layouts.app')

@section('title', $group->name)

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <h3>{{ $group->name }}</h3>
        <p class="text-muted">
            Tạo bởi <strong>{{ $group->user->name ?? 'Không rõ' }}</strong> 
            vào ngày {{ $group->created_at->format('d/m/Y H:i') }}
        </p>
        <p>{{ $group->description }}</p>

        @if($group->image)
            <img src="{{ asset('storage/' . $group->image) }}" 
                 class="img-fluid mb-3" 
                 style="max-height: 300px; object-fit: cover;">
        @endif
    </div>

    <h5 class="mt-4">Bài viết trong nhóm</h5>

    @forelse($posts as $post)
        <div class="card mb-3">
            <div class="card-body">
                <h5>{{ $post->title }}</h5>
                <p class="text-muted small">
                    Đăng bởi {{ $post->user->name }} vào {{ $post->created_at->format('d/m/Y H:i') }}
                </p>
                <p>{{ Str::limit($post->content, 200) }}</p>
                <a href="{{ route('posts.show', $post) }}" class="btn btn-sm btn-info">Xem</a>
            </div>
        </div>
    @empty
        <p class="text-muted">Chưa có bài viết nào trong nhóm này.</p>
    @endforelse

    <div class="d-flex justify-content-center mt-3">
        {{ $posts->links() }}
    </div>
</div>
@endsection
