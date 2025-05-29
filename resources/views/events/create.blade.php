@extends('layouts.app')

@section('title', 'Tạo sự kiện mới')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-calendar-plus text-primary me-2"></i>
                            Tạo sự kiện mới
                        </h5>
                        <a href="{{ route('events.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Quay lại
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label for="title" class="form-label">
                                <i class="fas fa-heading text-primary me-1"></i>
                                Tên sự kiện <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('title') is-invalid @enderror" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title') }}" 
                                   required
                                   placeholder="Nhập tên sự kiện">
                            @error('title')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-exclamation-circle me-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">
                                <i class="fas fa-align-left text-primary me-1"></i>
                                Mô tả <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="4" 
                                      required
                                      placeholder="Nhập mô tả sự kiện">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-exclamation-circle me-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-tag text-primary me-1"></i>
                                Loại sự kiện <span class="text-danger">*</span>
                            </label>
                            <div class="form-check">
                                <input class="form-check-input @error('event_type') is-invalid @enderror" 
                                       type="radio" 
                                       name="event_type" 
                                       id="online" 
                                       value="online" 
                                       {{ old('event_type') == 'online' ? 'checked' : '' }}>
                                <label class="form-check-label" for="online">
                                    Online
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input @error('event_type') is-invalid @enderror" 
                                       type="radio" 
                                       name="event_type" 
                                       id="offline" 
                                       value="offline" 
                                       {{ old('event_type') == 'offline' ? 'checked' : '' }}>
                                <label class="form-check-label" for="offline">
                                    Offline
                                </label>
                            </div>
                            @error('event_type')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-exclamation-circle me-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="event_time" class="form-label">
                                    <i class="fas fa-clock text-primary me-1"></i>
                                    Thời gian <span class="text-danger">*</span>
                                </label>
                                <input type="datetime-local" 
                                       class="form-control @error('event_time') is-invalid @enderror" 
                                       id="event_time" 
                                       name="event_time" 
                                       value="{{ old('event_time') }}" 
                                       required>
                                @error('event_time')
                                    <div class="invalid-feedback d-block">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="location" class="form-label">
                                    <i class="fas fa-map-marker-alt text-primary me-1"></i>
                                    Địa điểm <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('location') is-invalid @enderror" 
                                       id="location" 
                                       name="location" 
                                       value="{{ old('location') }}" 
                                       required
                                       placeholder="Nhập địa chỉ tổ chức sự kiện">
                                @error('location')
                                    <div class="invalid-feedback d-block">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="image" class="form-label">
                                <i class="fas fa-image text-primary me-1"></i>
                                Ảnh đại diện
                            </label>
                            <input type="file" 
                                   class="form-control @error('image') is-invalid @enderror" 
                                   id="image" 
                                   name="image" 
                                   accept="image/*">
                            <div class="form-text">Chọn ảnh đại diện cho sự kiện (tùy chọn)</div>
                            @error('image')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-exclamation-circle me-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                Tạo sự kiện
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

    .invalid-feedback {
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .invalid-feedback i {
        color: #dc3545;
    }

    .form-check-input.is-invalid {
        border-color: #dc3545;
    }

    .form-check-input.is-invalid:checked {
        background-color: #dc3545;
        border-color: #dc3545;
    }

    .text-danger {
        font-size: 0.875rem;
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

    // Add real-time validation
    const inputs = form.querySelectorAll('input, textarea');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            if (this.checkValidity()) {
                this.classList.remove('is-invalid');
            } else {
                this.classList.add('is-invalid');
            }
        });
    });
});
</script>
@endpush
@endsection