@extends('layouts.app')

@section('title', 'Đăng Story Mới')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Đăng Story Mới</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('stories.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="media" class="form-label">Chọn ảnh hoặc video</label>
                            <input type="file" class="form-control @error('media') is-invalid @enderror" 
                                   id="media" name="media" accept="image/*,video/*" required>
                            <div class="form-text">Hỗ trợ: JPG, PNG, GIF, MP4. Tối đa 10MB</div>
                            @error('media')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="caption" class="form-label">Caption (tùy chọn)</label>
                            <textarea class="form-control @error('caption') is-invalid @enderror" 
                                      id="caption" name="caption" rows="3" 
                                      maxlength="500">{{ old('caption') }}</textarea>
                            @error('caption')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Đăng Story
                            </button>
                            <a href="{{ route('stories.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const mediaInput = document.getElementById('media');
    const captionTextarea = document.getElementById('caption');
    
    // Hiển thị preview khi chọn file
    mediaInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // Tạo preview element nếu chưa có
                let preview = document.getElementById('media-preview');
                if (!preview) {
                    preview = document.createElement('div');
                    preview.id = 'media-preview';
                    preview.className = 'mt-3';
                    mediaInput.parentNode.appendChild(preview);
                }
                
                // Hiển thị preview
                if (file.type.startsWith('image/')) {
                    preview.innerHTML = `<img src="${e.target.result}" class="img-fluid rounded" style="max-height: 300px;">`;
                } else if (file.type.startsWith('video/')) {
                    preview.innerHTML = `
                        <video controls class="w-100 rounded" style="max-height: 300px;">
                            <source src="${e.target.result}" type="${file.type}">
                            Your browser does not support the video tag.
                        </video>`;
                }
            };
            reader.readAsDataURL(file);
        }
    });
});
</script>
@endpush
@endsection 