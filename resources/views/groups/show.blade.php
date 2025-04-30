@extends('layouts.app')

@section('content')
<div class="container">
    <div class="text-center mb-4">
        @if($group->image)
            <img src="{{ asset($group->image) }}" class="rounded-circle shadow" width="150" height="150" alt="Group Image">
        @else
            <img src="https://via.placeholder.com/150" class="rounded-circle shadow" alt="Default Group">
        @endif
        <h2 class="mt-3">{{ $group->name }}</h2>
        <p>{{ $group->description }}</p>
        <span class="badge bg-{{ $group->privacy == 'public' ? 'success' : 'secondary' }}">{{ ucfirst($group->privacy) }}</span>
    </div>

    <h4>Thành viên</h4>
    <div class="row">
        @foreach($group->members as $member)
            <div class="col-md-3 text-center mb-3">
                <div class="card p-2 shadow-sm">
                    <img src="https://via.placeholder.com/80" class="rounded-circle mx-auto d-block" alt="Avatar">
                    <h6 class="mt-2">{{ $member->name }}</h6>
                    <small>{{ ucfirst($member->pivot->role) }}</small>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Dòng phân cách --}}
    <hr class="my-4">

    <h4>Bài viết trong nhóm</h4>

    {{-- Nếu user đã tham gia nhóm --}}
    @if(auth()->user() && $group->members->contains(auth()->user()))
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <form action="{{ route('groups.posts.store', $group) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <textarea name="content" class="form-control" rows="3" placeholder="Bạn đang nghĩ gì?" required></textarea>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Đăng bài</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Hiển thị danh sách bài viết --}}
    <div class="row">
    @foreach ($group->posts as $post)
    <div class="card mb-3 shadow-sm">
        <div class="card-body">
            <h5>
                <a href="{{ route('groups.show', $post->user->id) }}">{{ $post->user->name }}</a>
            </h5>
            <p>{{ $post->content }}</p>

            {{-- Nút hành động giống Facebook --}}
            <div class="d-flex justify-content-between">
            <form action="{{ route('posts.like', $post) }}" method="POST" style="margin: 0;">
    @csrf
    <button type="submit" class="btn-like" style="background-color:rgb(198, 202, 200); color: white; border: 2px solid white; padding: 10px 20px; border-radius: 5px; cursor: pointer;">
        <i class="fas fa-thumbs-up"></i>
        {{ $post->likes->count() }}
    </button>
</form>

                <button class="btn btn-sm btn-outline-secondary">💬 Bình luận</button>
                <button class="btn btn-sm btn-outline-secondary">🔁 Chia sẻ</button>
            </div>

            <hr>

            <p class="fw-bold mb-1">Bình luận:</p>
            <ul class="list-unstyled">
                @foreach ($post->comments as $comment)
                    <li class="mb-1">
                        <strong>{{ $comment->user->name }}:</strong> {{ $comment->content }}
                    </li>
                @endforeach
            </ul>

            <p class="text-muted small">Đã chia sẻ {{ $post->shares->count() }} lần</p>
        </div>
    </div>
@endforeach


    </div>

</div>
@endsection
