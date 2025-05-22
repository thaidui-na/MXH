@extends('layouts.app')

@section('title', 'Thông báo của tôi')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Tất cả thông báo</h5>
                </div>
                <div class="card-body">
                    @if($notifications->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($notifications as $notification)
                                <li class="list-group-item {{ $notification->read_at ? '' : 'fw-bold' }}">
                                    <a href="{{ $notification->data['url'] ?? '#' }}" class="text-decoration-none">
                                        {{ $notification->data['message'] ?? 'Có thông báo mới' }}
                                    </a>
                                    <br>
                                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                </li>
                            @endforeach
                        </ul>
                        <div class="mt-3">
                            {{ $notifications->links() }}
                        </div>
                    @else
                        <div class="alert alert-info mb-0">Bạn chưa có thông báo nào.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 