@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Alert Container -->
    <div id="alertContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 1050"></div>

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
                                <div class="d-flex align-items-center flex-grow-1">
                                    <img src="{{ $notification->data['avatar'] ?? asset('images/default-avatar.jpg') }}" 
                                         class="rounded-circle me-3" 
                                         style="width: 50px; height: 50px; object-fit: cover;"
                                         alt="Avatar">
                                    <div class="flex-grow-1">
                                        <p class="mb-0">{{ $notification->data['message'] }}</p>
                                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    @if($notification->type === 'App\\Notifications\\FriendRequestNotification')
                                        <button type="button" 
                                                class="btn btn-sm btn-success me-2 accept-friend-btn" 
                                                data-user-id="{{ $notification->data['user_id'] }}"
                                                data-notification-id="{{ $notification->id }}">
                                            <i class="fas fa-check"></i> Chấp nhận
                                        </button>
                                        <button type="button" 
                                                class="btn btn-sm btn-danger reject-friend-btn"
                                                data-user-id="{{ $notification->data['user_id'] }}"
                                                data-notification-id="{{ $notification->id }}">
                                            <i class="fas fa-times"></i> Từ chối
                                        </button>
                                    @else
                                        <button type="button" 
                                                class="btn btn-sm btn-danger delete-notification-btn"
                                                data-notification-id="{{ $notification->id }}">
                                            <i class="fas fa-trash"></i> Xóa
                                        </button>
                                    @endif
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
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, setting up event listeners');

    // Xử lý nút chấp nhận kết bạn
    document.querySelectorAll('.accept-friend-btn').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.userId;
            const notificationId = this.dataset.notificationId;
            console.log('Accept button clicked:', { userId, notificationId });
            handleFriendRequest('accept', userId, notificationId);
        });
    });

    // Xử lý nút từ chối kết bạn
    document.querySelectorAll('.reject-friend-btn').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.userId;
            const notificationId = this.dataset.notificationId;
            console.log('Reject button clicked:', { userId, notificationId });
            handleFriendRequest('reject', userId, notificationId);
        });
    });

    // Xử lý nút xóa thông báo
    document.querySelectorAll('.delete-notification-btn').forEach(button => {
        button.addEventListener('click', function() {
            const notificationId = this.dataset.notificationId;
            console.log('Delete button clicked:', notificationId);
            deleteNotification(notificationId);
        });
    });
});

function showAlert(message, type = 'success') {
    console.log('Showing alert:', { message, type });
    const alertContainer = document.getElementById('alertContainer');
    const alertId = 'alert-' + Date.now();
    
    const alertHtml = `
        <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    alertContainer.insertAdjacentHTML('beforeend', alertHtml);
    
    // Tự động ẩn sau 3 giây
    setTimeout(() => {
        const alert = document.getElementById(alertId);
        if (alert) {
            alert.remove();
        }
    }, 3000);
}

function handleFriendRequest(action, userId, notificationId) {
    console.log('Handling friend request:', { action, userId, notificationId });
    
    const url = action === 'accept' ? `/friends/accept/${userId}` : `/friends/reject/${userId}`;
    const method = 'POST';
    
    console.log('Making request to:', url);
    
    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        
        if (data.success) {
            const noti = document.getElementById(`notification-${notificationId}`);
            if (noti) {
                const messageElement = noti.querySelector('.flex-grow-1 p');
                if (messageElement) {
                    messageElement.textContent = action === 'accept' 
                        ? 'Bạn đã chấp nhận lời mời kết bạn này.'
                        : 'Bạn đã từ chối lời mời kết bạn này.';
                }
                
                const buttonsContainer = noti.querySelector('.ms-3');
                if (buttonsContainer) {
                    buttonsContainer.innerHTML = '';
                }
            }

            showAlert(data.message);

            if (action === 'reject') {
                setTimeout(() => {
                    const noti = document.getElementById(`notification-${notificationId}`);
                    if (noti) {
                        noti.remove();
                        updateNotificationCount();
                    }
                }, 3000);
            }

            if (action === 'accept' && data.friend) {
                console.log('Updating friend list with:', data.friend);
                
                // Cập nhật trạng thái nút kết bạn trong trang cá nhân nếu có
                const friendButton = document.querySelector(`.friend-button[data-user-id="${userId}"]`);
                if (friendButton) {
                    console.log('Updating friend button');
                    friendButton.classList.remove('btn-outline-primary');
                    friendButton.classList.add('btn-primary');
                    friendButton.innerHTML = '<i class="fas fa-user-friends"></i> Bạn bè';
                }

                // Thêm bạn bè mới vào danh sách bạn bè
                const friendsList = document.querySelector('.friends-list');
                if (friendsList) {
                    console.log('Found friends list, adding new friend');
                    const friendCard = document.createElement('div');
                    friendCard.className = 'col-md-4 mb-3';
                    friendCard.innerHTML = `
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <img src="${data.friend.avatar || '/images/default-avatar.jpg'}"
                                         class="rounded-circle me-3"
                                         style="width: 50px; height: 50px; object-fit: cover;"
                                         alt="${data.friend.name}'s avatar">
                                    <div>
                                        <h5 class="mb-0">${data.friend.name}</h5>
                                        <p class="text-muted mb-0">${data.friend.email}</p>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <a href="/posts/my_posts/${data.friend.id}" class="btn btn-sm btn-info">
                                        <i class="fas fa-user me-1"></i>Xem trang cá nhân
                                    </a>
                                    <a href="/messages/${data.friend.id}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-envelope me-1"></i>Nhắn tin
                                    </a>
                                </div>
                            </div>
                        </div>
                    `;
                    friendsList.appendChild(friendCard);
                } else {
                    console.log('Friends list not found');
                }

                // Cập nhật số lượng bạn bè nếu có
                const friendCountElement = document.querySelector('.friend-count');
                if (friendCountElement) {
                    const currentCount = parseInt(friendCountElement.textContent) || 0;
                    friendCountElement.textContent = currentCount + 1;
                }
            }
        } else {
            showAlert(data.error || `Có lỗi xảy ra khi ${action === 'accept' ? 'chấp nhận' : 'từ chối'} lời mời kết bạn.`, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert(`Có lỗi xảy ra khi ${action === 'accept' ? 'chấp nhận' : 'từ chối'} lời mời kết bạn.`, 'danger');
    });
}

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
                const noti = document.getElementById(`notification-${id}`);
                if (noti) {
                    noti.remove();
                    updateNotificationCount();
                }
                showAlert('Đã xóa thông báo thành công');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Có lỗi xảy ra khi xóa thông báo', 'danger');
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
            showAlert('Có lỗi xảy ra khi xóa thông báo', 'danger');
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
</script>
@endpush
@endsection 