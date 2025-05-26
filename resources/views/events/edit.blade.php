@extends('layouts.app')

@section('title', 'Chỉnh sửa sự kiện')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-edit text-primary me-2"></i>
                            Chỉnh sửa sự kiện
                        </h5>
                        <a href="{{ route('events.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Quay lại
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('events.update', $event) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="title" class="form-label">
                                <i class="fas fa-heading text-primary me-1"></i>
                                Tên sự kiện
                            </label>
                            <input type="text" 
                                   class="form-control @error('title') is-invalid @enderror" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title', $event->title) }}" 
                                   required
                                   placeholder="Nhập tên sự kiện">
                            @error('title')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">
                                <i class="fas fa-align-left text-primary me-1"></i>
                                Mô tả
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="4" 
                                      required
                                      placeholder="Nhập mô tả sự kiện">{{ old('description', $event->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="event_time" class="form-label">
                                <i class="fas fa-clock text-primary me-1"></i>
                                Thời gian
                            </label>
                            <input type="datetime-local" 
                                   class="form-control @error('event_time') is-invalid @enderror" 
                                   id="event_time" 
                                   name="event_time" 
                                   value="{{ old('event_time', $event->event_time->format('Y-m-d\TH:i')) }}" 
                                   required>
                            @error('event_time')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Loại sự kiện</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="event_type" id="online" value="online" 
                                    {{ (old('event_type', str_contains($event->location, 'Online:')) ? 'online' : 'offline') == 'online' ? 'checked' : '' }}>
                                <label class="form-check-label" for="online">
                                    Online
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="event_type" id="offline" value="offline" 
                                    {{ (old('event_type', str_contains($event->location, 'Online:')) ? 'online' : 'offline') == 'offline' ? 'checked' : '' }}>
                                <label class="form-check-label" for="offline">
                                    Offline
                                </label>
                            </div>
                            @error('event_type')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="location" class="form-label">
                                <i class="fas fa-map-marker-alt text-primary me-1"></i>
                                Địa điểm
                            </label>
                            <input type="text" 
                                   class="form-control @error('location') is-invalid @enderror" 
                                   id="location" 
                                   name="location" 
                                   value="{{ old('location', str_replace('Online: ', '', $event->location)) }}" 
                                   required
                                   placeholder="Nhập địa chỉ tổ chức sự kiện">
                            @error('location')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="image" class="form-label">
                                <i class="fas fa-image text-primary me-1"></i>
                                Ảnh đại diện
                            </label>
                            @if($event->image_path)
                                <div class="mb-2">
                                    <img src="{{ Storage::url($event->image_path) }}" 
                                         alt="Ảnh hiện tại" 
                                         class="img-thumbnail" 
                                         style="max-height: 150px;">
                                </div>
                            @endif
                            <input type="file" 
                                   class="form-control @error('image') is-invalid @enderror" 
                                   id="image" 
                                   name="image" 
                                   accept="image/*">
                            <div class="form-text">Chọn ảnh mới để thay thế ảnh hiện tại (tùy chọn)</div>
                            @error('image')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                Cập nhật sự kiện
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    
    .form-label {
        font-weight: 500;
    }
    
    .card {
        border: none;
        border-radius: 10px;
    }
    
    .card-header {
        border-bottom: 1px solid rgba(0,0,0,.125);
        border-radius: 10px 10px 0 0 !important;
    }
    
    .btn-primary {
        padding: 0.5rem 1.5rem;
    }
    
    .form-text {
        font-size: 0.875rem;
        color: #6c757d;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const eventTypeInputs = document.querySelectorAll('input[name="event_type"]');
    const locationInput = document.getElementById('location');
    const form = document.querySelector('form');

    function updateLocationField() {
        const selectedType = document.querySelector('input[name="event_type"]:checked').value;
        if (selectedType === 'online') {
            locationInput.value = 'Online Meeting';
            locationInput.readOnly = true;
            locationInput.placeholder = 'Tự động điền cho sự kiện online';
        } else {
            locationInput.value = '';
            locationInput.readOnly = false;
            locationInput.placeholder = 'Nhập địa chỉ tổ chức sự kiện';
        }
    }

    eventTypeInputs.forEach(input => {
        input.addEventListener('change', updateLocationField);
    });

    // Set initial state
    updateLocationField();

    // Ensure location value is set before form submission
    form.addEventListener('submit', function(e) {
        const selectedType = document.querySelector('input[name="event_type"]:checked').value;
        if (selectedType === 'online') {
            locationInput.value = 'Online Meeting';
        }
    });
});
</script>
@endpush
@endsection 