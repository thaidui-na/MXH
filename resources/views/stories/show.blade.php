@extends('layouts.app')

@section('title', 'Story của ' . $story->user->name)

@section('content')
<div class="container-fluid p-0">
    <div class="story-viewer">
        <div class="story-content">
            @if($story->media_type === 'image')
                <img src="{{ $story->media_url }}" alt="Story" class="story-media">
            @else
                <video src="{{ $story->media_url }}" controls class="story-media"></video>
            @endif

            <div class="story-info">
                <div class="user-info">
                    <img src="{{ $story->user->avatar_url }}" alt="{{ $story->user->name }}" class="user-avatar">
                    <span class="username">{{ $story->user->name }}</span>
                    <span class="time">{{ $story->created_at->diffForHumans() }}</span>
                </div>
                @if($story->caption)
                    <div class="caption">{{ $story->caption }}</div>
                @endif
            </div>

            @if($story->user_id === auth()->id())
                <form action="{{ route('stories.destroy', $story) }}" method="POST" class="delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa story này?')">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
.story-viewer {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.9);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.story-content {
    position: relative;
    max-width: 100%;
    max-height: 100%;
    margin: auto;
}

.story-media {
    max-width: 100%;
    max-height: 90vh;
    object-fit: contain;
}

.story-info {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 1rem;
    background: linear-gradient(transparent, rgba(0, 0, 0, 0.8));
    color: white;
}

.user-info {
    display: flex;
    align-items: center;
    margin-bottom: 0.5rem;
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    margin-right: 0.5rem;
}

.username {
    font-weight: bold;
    margin-right: 0.5rem;
}

.time {
    font-size: 0.8rem;
    opacity: 0.8;
}

.caption {
    margin-top: 0.5rem;
    font-size: 0.9rem;
}

.delete-form {
    position: absolute;
    top: 1rem;
    right: 1rem;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tự động chuyển về trang danh sách sau 5 giây
    setTimeout(function() {
        window.location.href = '{{ route('stories.index') }}';
    }, 5000);
});
</script>
@endpush
@endsection 