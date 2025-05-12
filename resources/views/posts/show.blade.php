@extends('layouts.app')

@section('title', $post->title)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success mb-3">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="card-title mb-0">{{ $post->title }}</h3>
                        <button class="btn btn-sm btn-outline-danger like-button {{ $post->isLikedBy(auth()->id()) ? 'active' : '' }}"
                            data-post-id="{{ $post->id }}">
                            <i class="fas fa-heart {{ $post->isLikedBy(auth()->id()) ? 'text-danger' : 'text-muted' }}"></i>
                            <span class="like-count ms-1">{{ $post->getLikesCount() }}</span>
                        </button>
                    </div>
                    <div class="d-flex justify-content-between my-3">
                        <p class="card-text text-muted">
                            <i class="fas fa-user"></i> {{ $post->user->name }} <br>
                            <i class="fas fa-calendar"></i> {{ $post->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    
                    <hr>
                    
                    <div class="card-text mb-4">
                        {!! nl2br(e($post->content)) !!}
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ url()->previous() }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                        
                        @if($post->user_id === auth()->id())
                            <div>
                                <a href="{{ route('posts.edit', $post) }}" class="btn btn-warning me-2">
                                    <i class="fas fa-edit"></i> Sửa
                                </a>
                                <form action="{{ route('posts.destroy', $post) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài viết này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash"></i> Xóa
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Like button functionality
    document.querySelectorAll('.like-button').forEach(button => {
        let isProcessing = false; // Flag to prevent double clicks
        
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Prevent double clicks
            if (isProcessing) {
                console.log('Like action is already in progress');
                return;
            }
            
            isProcessing = true;
            const postId = this.dataset.postId;
            const icon = this.querySelector('i');
            const countSpan = this.querySelector('.like-count');
            
            console.log('Like button clicked for post:', postId);
            
            // Disable button while processing
            this.disabled = true;
            
            fetch(`/posts/${postId}/like`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.liked) {
                    icon.classList.remove('text-muted');
                    icon.classList.add('text-danger');
                    this.classList.add('active');
                } else {
                    icon.classList.remove('text-danger');
                    icon.classList.add('text-muted');
                    this.classList.remove('active');
                }
                countSpan.textContent = data.likesCount;
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi thích bài viết. Vui lòng thử lại sau.');
            })
            .finally(() => {
                // Re-enable button and reset processing flag
                this.disabled = false;
                isProcessing = false;
            });
        });
    });
});
</script>
@endpush 
