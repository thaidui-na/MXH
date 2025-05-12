@extends('layouts.app')

@section('title', $user->name . ' - Profile')

@section('content')
<div class="container py-5">
    <div class="row">
        <!-- Profile Header -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <img src="{{ $user->avatar_url }}" 
                         alt="{{ $user->name }}" 
                         class="rounded-circle img-fluid mb-3" 
                         style="width: 150px; height: 150px; object-fit: cover;">
                    
                    <h5 class="card-title">{{ $user->name }}</h5>
                    <p class="text-muted">Thành viên từ: {{ $user->created_at->format('d/m/Y') }}</p>

                    <!-- Follow Button -->
                    <div class="mt-3">
                        <x-follow-button :user="$user" />
                    </div>

                    <!-- User Info -->
                    @if($user->bio || $user->phone || $user->birthday)
                        <hr>
                        <div class="text-start profile-info">
                            @if($user->bio)
                                <div class="mb-3">
                                    <h6 class="text-muted mb-1">Giới thiệu:</h6>
                                    <p class="mb-0">{{ $user->bio }}</p>
                                </div>
                            @endif

                            @if($user->phone)
                                <div class="mb-2">
                                    <h6 class="text-muted mb-1">Số điện thoại:</h6>
                                    <p class="mb-0">{{ $user->phone }}</p>
                                </div>
                            @endif

                            @if($user->birthday)
                                <div class="mb-2">
                                    <h6 class="text-muted mb-1">Ngày sinh:</h6>
                                    <p class="mb-0">{{ $user->birthday->format('d/m/Y') }}</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- User's Posts -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Bài viết của {{ $user->name }}</h5>
                </div>
                <div class="card-body">
                    @forelse($user->posts as $post)
                        <div class="post mb-4">
                            <h6>{{ $post->title }}</h6>
                            <p class="text-muted small">{{ $post->created_at->format('d/m/Y H:i') }}</p>
                            <p>{{ Str::limit($post->content, 200) }}</p>
                            <a href="{{ route('posts.show', $post) }}" class="btn btn-sm btn-outline-primary">Xem chi tiết</a>
                        </div>
                    @empty
                        <p class="text-muted">Chưa có bài viết nào.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    .profile-info h6 {
        font-size: 0.9rem;
        color: #6c757d;
    }
    .profile-info p {
        font-size: 1rem;
    }
    .post {
        padding: 1rem;
        border-bottom: 1px solid #eee;
    }
    .post:last-child {
        border-bottom: none;
    }
</style>
@endpush 