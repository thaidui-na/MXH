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
        @forelse($group->posts as $post)
            <div class="col-md-6 mb-3">
                <div class="card shadow-sm p-3">
                    <div class="d-flex align-items-center mb-2">
                        <img src="https://via.placeholder.com/40" class="rounded-circle me-2" alt="Avatar">
                        <div>
                            <strong>{{ $post->user->name }}</strong><br>
                            <small class="text-muted">{{ $post->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                    <p class="mb-0">{{ $post->content }}</p>
                </div>
            </div>
        @empty
            <p class="text-center text-muted">Chưa có bài viết nào.</p>
        @endforelse
    </div>

</div>
@endsection
