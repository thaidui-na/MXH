@extends('layouts.app')

@section('title', 'Stories')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Stories</h2>
                <a href="{{ route('stories.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Đăng Story Mới
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="stories-container">
                @forelse($stories as $userId => $userStories)
                    @php
                        $user = $userStories->first()->user;
                        $latestStory = $userStories->first();
                    @endphp
                    <div class="story-item">
                        <a href="{{ route('stories.show', $latestStory) }}" class="text-decoration-none">
                            <div class="story-avatar">
                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" 
                                     class="rounded-circle {{ $userStories->where('is_active', true)->count() > 0 ? 'has-story' : '' }}">
                            </div>
                            <div class="story-username">{{ $user->name }}</div>
                        </a>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="fas fa-camera fa-3x text-muted mb-3"></i>
                        <h4>Chưa có story nào</h4>
                        <p class="text-muted">Hãy đăng story đầu tiên của bạn!</p>
                        <a href="{{ route('stories.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Đăng Story Mới
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.stories-container {
    display: flex;
    gap: 1rem;
    overflow-x: auto;
    padding: 1rem 0;
}

.story-item {
    text-align: center;
    min-width: 80px;
}

.story-avatar {
    width: 64px;
    height: 64px;
    margin: 0 auto;
    position: relative;
}

.story-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border: 2px solid #ddd;
    transition: all 0.3s ease;
}

.story-avatar img.has-story {
    border: 2px solid #007bff;
}

.story-username {
    font-size: 0.8rem;
    margin-top: 0.5rem;
    color: #333;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 80px;
}
</style>
@endpush
@endsection 