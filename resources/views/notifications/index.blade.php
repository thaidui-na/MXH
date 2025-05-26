@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Thông báo</h5>
                    @if($notifications->count() > 0)
                        <button class="btn btn-sm btn-outline-danger" onclick="clearAllNotifications()">
                            <i class="fas fa-trash"></i> Xóa tất cả
                        </button>
                    @endif
                </div>
                <div class="card-body">
                    @forelse($notifications as $notification)
                        <div class="notification-item mb-3 p-3 border rounded" id="notification-{{ $notification->id }}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $notification->data['avatar'] ?? asset('images/default-avatar.jpg') }}" 
                                         class="rounded-circle me-3" 
                                         style="width: 50px; height: 50px; object-fit: cover;"
                                         alt="Avatar">
                                    <div class="flex-grow-1">
                                        <p class="mb-0">{{ $notification->data['message'] }}</p>
                                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                    <div class="ms-3">
                                        @if($notification->type === 'App\\Notifications\\FriendRequestNotification')
                                            <button class="btn btn-sm btn-success me-2" onclick="acceptFriendRequest({{ $notification->data['user_id'] }}, {{ $notification->id }})">
                                                <i class="fas fa-check"></i> Chấp nhận
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="rejectFriendRequest({{ $notification->data['user_id'] }}, {{ $notification->id }})">
                                                <i class="fas fa-times"></i> Từ chối
                                            </button>
                                        @else
                                            <button class="btn btn-sm btn-danger" onclick="deleteNotification({{ $notification->id }})">
                                                <i class="fas fa-trash"></i> Xóa
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-link text-dark p-0" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <button class="dropdown-item" onclick="deleteNotification('{{ $notification->id }}')">
                                                <i class="fas fa-trash me-2"></i> Xóa
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="fas fa-bell fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Không có thông báo nào</p>
                        </div>
                    @endforelse

                    @if($notifications->hasPages())
                        <div class="d-flex justify-content-center mt-4">
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
function deleteNotification(id) {
    if (confirm('Bạn có chắc chắn muốn xóa thông báo này?')) {
        fetch(`/notifications/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById(`notification-${id}`).remove();
                updateNotificationCount();
                setTimeout(() => location.reload(), 300);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi xóa thông báo');
        });
    }
}

function clearAllNotifications() {
    if (confirm('Bạn có chắc chắn muốn xóa tất cả thông báo?')) {
        fetch('/notifications/clear-all', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi xóa thông báo');
        });
    }
}

function updateNotificationCount() {
    const countElement = document.querySelector('.notification-count');
    if (countElement) {
        const currentCount = parseInt(countElement.textContent);
        if (currentCount > 1) {
            countElement.textContent = currentCount - 1;
        } else {
            countElement.remove();
        }
    }
}

function acceptFriendRequest(userId, notificationId) {
    fetch(`/friends/accept/${userId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Thay đổi nội dung thông báo thay vì xóa
            const noti = document.getElementById(`notification-${notificationId}`);
            if (noti) {
                noti.querySelector('.flex-grow-1 p').textContent = 'Bạn đã chấp nhận lời mời kết bạn này.';
                // Ẩn các nút chấp nhận/từ chối
                const btns = noti.querySelectorAll('button');
                btns.forEach(btn => btn.style.display = 'none');
            }
            alert('Đã chấp nhận lời mời kết bạn!');
        } else {
            alert(data.message || 'Có lỗi xảy ra khi chấp nhận lời mời kết bạn.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi chấp nhận lời mời kết bạn.');
    });
}

function rejectFriendRequest(userId, notificationId) {
    fetch(`/friends/reject/${userId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Xóa thông báo sau khi từ chối
            deleteNotification(notificationId);
            alert('Đã từ chối lời mời kết bạn.');
        } else {
            alert(data.message || 'Có lỗi xảy ra khi từ chối lời mời kết bạn.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi từ chối lời mời kết bạn.');
    });
}
</script>
@endpush
@endsection 