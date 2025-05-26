@extends('layouts.app')

@section('title', $event->title)

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-8">
            {{-- Thông tin sự kiện --}}
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <h2 class="card-title mb-0">{{ $event->title }}</h2>
                        <div class="dropdown">
                            <button class="btn btn-link text-dark p-0" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v fa-lg"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @if(auth()->id() === $event->user_id)
                                    <li>
                                        <a class="dropdown-item" href="{{ route('events.edit', $event) }}">
                                            <i class="fas fa-edit me-2"></i> Chỉnh sửa
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('events.destroy', $event) }}" method="POST" 
                                              onsubmit="return confirm('Bạn có chắc chắn muốn xóa sự kiện này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fas fa-trash me-2"></i> Xóa
                                            </button>
                                        </form>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>

                    @if($event->image_path)
                        <img src="{{ Storage::url($event->image_path) }}" 
                             alt="{{ $event->title }}" 
                             class="img-fluid rounded mb-4">
                    @endif

                    <div class="mb-4">
                        <h5 class="text-muted mb-3">Chi tiết sự kiện</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <i class="fas fa-clock me-2"></i>
                                    {{ $event->event_time->format('d/m/Y H:i') }}
                                </p>
                                <p class="mb-0">
                                    <i class="fas fa-map-marker-alt me-2"></i>
                                    {{ $event->location }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <i class="fas fa-user me-2"></i>
                                    Tạo bởi: {{ $event->user->name }}
                                </p>
                                <p class="mb-0">
                                    <i class="fas fa-users me-2"></i>
                                    {{ $event->participants_count }} người tham gia
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5 class="text-muted mb-3">Mô tả</h5>
                        <p class="mb-0">{{ $event->description }}</p>
                    </div>

                    @if(auth()->id() !== $event->user_id)
                        <div class="d-grid">
                            @if($event->isParticipant(auth()->user()))
                                <form action="{{ route('events.leave', $event) }}" method="POST" 
                                      onsubmit="return confirm('Bạn có chắc chắn muốn rời khỏi sự kiện này?')">
                                    @csrf
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="fas fa-sign-out-alt me-2"></i>
                                        Rời khỏi sự kiện
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('events.join', $event) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-user-plus me-2"></i>
                                        Tham gia sự kiện
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            {{-- Danh sách người tham gia --}}
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-users me-2"></i>
                        Người tham gia ({{ $event->participants_count }})
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($event->activeParticipants as $participant)
                            <div class="list-group-item">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $participant->avatar_url }}" 
                                         alt="{{ $participant->name }}"
                                         class="rounded-circle me-3"
                                         style="width: 40px; height: 40px; object-fit: cover;">
                                    <div>
                                        <h6 class="mb-0">{{ $participant->name }}</h6>
                                        <small class="text-muted">
                                            Tham gia {{ \Carbon\Carbon::parse($participant->pivot->joined_at)->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item text-center text-muted py-4">
                                <i class="fas fa-users fa-2x mb-2"></i>
                                <p class="mb-0">Chưa có người tham gia</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    .card-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.125);
    }
    
    .list-group-item {
        border-left: none;
        border-right: none;
    }
    
    .list-group-item:first-child {
        border-top: none;
    }
    
    .list-group-item:last-child {
        border-bottom: none;
    }
</style>
@endpush
@endsection 