@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Thông báo</h5>
                    <button class="btn btn-sm btn-primary" onclick="markAllAsRead()">Đánh dấu tất cả đã đọc</button>
                </div>

                <div class="card-body">
                    @if($notifications->isEmpty())
                        <p class="text-center">Không có thông báo nào</p>
                    @else
                        <div class="list-group">
                            @foreach($notifications as $notification)
                                <div class="list-group-item {{ $notification->read ? '' : 'bg-light' }}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            @if($notification->type === 'like')
                                                <strong>{{ $notification->sender->name }}</strong> đã thích bài viết của bạn
                                            @elseif($notification->type === 'comment')
                                                <strong>{{ $notification->sender->name }}</strong> đã bình luận bài viết của bạn
                                            @endif
                                            <small class="text-muted d-block">{{ $notification->created_at->diffForHumans() }}</small>
                                        </div>
                                        @if(!$notification->read)
                                            <button class="btn btn-sm btn-outline-primary" onclick="markAsRead({{ $notification->id }})">
                                                Đánh dấu đã đọc
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-3">
                            {{ $notifications->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function markAsRead(id) {
    fetch(`/notifications/${id}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            location.reload();
        }
    });
}

function markAllAsRead() {
    fetch('/notifications/read-all', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            location.reload();
        }
    });
}
</script>
@endpush
@endsection 