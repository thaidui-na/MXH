@extends('layouts.app')

@section('title', $event->title)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-calendar-alt text-primary me-2"></i>
                            {{ $event->title }}
                        </h5>
                        <a href="{{ route('events.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Quay lại
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if($event->image_path)
                        <div class="mb-4">
                            <img src="{{ Storage::url($event->image_path) }}" 
                                 alt="{{ $event->title }}" 
                                 class="img-fluid rounded w-100" 
                                 style="max-height: 400px; object-fit: cover;">
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 bg-light">
                                <div class="card-body">
                                    <h6 class="card-subtitle mb-3 text-primary">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Thông tin sự kiện
                                    </h6>
                                    <div class="mb-3">
                                        <small class="text-muted d-block mb-1">
                                            <i class="fas fa-clock me-1"></i>
                                            Thời gian
                                        </small>
                                        <p class="mb-0 fw-medium">{{ $event->event_time->format('d/m/Y H:i') }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <small class="text-muted d-block mb-1">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            Địa điểm
                                        </small>
                                        <p class="mb-0 fw-medium">{{ $event->location }}</p>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block mb-1">
                                            <i class="fas fa-user me-1"></i>
                                            Người tạo
                                        </small>
                                        <p class="mb-0 fw-medium">{{ $event->user->name }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 bg-light">
                                <div class="card-body">
                                    <h6 class="card-subtitle mb-3 text-primary">
                                        <i class="fas fa-align-left me-2"></i>
                                        Mô tả
                                    </h6>
                                    <p class="mb-0 text-dark" style="white-space: pre-line;">{{ $event->description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(auth()->id() === $event->user_id)
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('events.edit', $event) }}" 
                               class="btn btn-warning">
                                <i class="fas fa-edit me-1"></i>
                                Chỉnh sửa
                            </a>
                            <form action="{{ route('events.destroy', $event) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="btn btn-danger"
                                        onclick="return confirm('Bạn có chắc chắn muốn xóa sự kiện này?')">
                                    <i class="fas fa-trash-alt me-1"></i>
                                    Xóa
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        border-radius: 10px;
    }
    
    .card-header {
        border-bottom: 1px solid rgba(0,0,0,.125);
        border-radius: 10px 10px 0 0 !important;
    }
    
    .bg-light {
        background-color: #f8f9fa !important;
    }
    
    .btn {
        padding: 0.5rem 1.5rem;
    }
    
    .text-muted {
        font-size: 0.875rem;
    }
    
    .fw-medium {
        font-weight: 500;
    }
</style>
@endpush
@endsection 