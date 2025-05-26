@extends('layouts.app')

@section('title', 'Danh sách sự kiện')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="h3 mb-0">Danh sách sự kiện</h2>
        </div>
        <div class="col text-end">
            <a href="{{ route('events.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tạo sự kiện mới
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        @forelse($events as $event)
            <div class="col">
                <div class="card h-100 shadow-sm">
                    @if($event->image_path)
                        <img src="{{ Storage::url($event->image_path) }}" alt="{{ $event->title }}" class="card-img-top" style="height: 200px; object-fit: cover;">
                    @else
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                            <span class="text-muted">Không có ảnh</span>
                        </div>
                    @endif
                    
                    <div class="card-body">
                        <h5 class="card-title">{{ $event->title }}</h5>
                        <p class="card-text text-muted">{{ Str::limit($event->description, 100) }}</p>
                        <div class="small text-muted mb-3">
                            <p class="mb-1"><i class="fas fa-clock"></i> {{ $event->event_time->format('d/m/Y H:i') }}</p>
                            <p class="mb-0"><i class="fas fa-map-marker-alt"></i> {{ $event->location }}</p>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('events.show', $event) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i> Xem chi tiết
                            </a>
                            @if(auth()->id() === $event->user_id)
                                <div class="btn-group">
                                    <a href="{{ route('events.edit', $event) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-edit"></i> Sửa
                                    </a>
                                    <form action="{{ route('events.destroy', $event) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa sự kiện này?')">
                                            <i class="fas fa-trash"></i> Xóa
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Chưa có sự kiện nào.</p>
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $events->links() }}
    </div>
</div>
@endsection 