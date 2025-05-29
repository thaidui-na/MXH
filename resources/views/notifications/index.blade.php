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
                        <div class="notification-item mb-3 p-3 border rounded" 
                             id="notification-{{ $notification->id }}"
                             data-notification-type="{{ $notification->data['type'] }}"
                             data-user-id="{{ $notification->data['user_id'] }}"
                             data-notification-id="{{ $notification->id }}">
                            <div class="d-flex align-items-center">
                                @if($notification->type === 'App\\Notifications\\PostLikeNotification')
                                    <a href="{{ route('posts.show', $notification->data['post_id']) }}" class="text-decoration-none text-dark flex-grow-1">
                                @elseif($notification->type === 'App\\Notifications\\CommentNotification')
                                    <a href="{{ route('comments.index', $notification->data['post_id']) }}#comment-{{ $notification->data['comment_id'] }}" class="text-decoration-none text-dark flex-grow-1">
                                @else
                                    <a href="{{ route('posts.my_posts', $notification->data['user_id']) }}" class="text-decoration-none text-dark flex-grow-1">
                                @endif
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $notification->data['avatar'] }}" alt="Avatar" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                        <div class="flex-grow-1">
                                            <p class="mb-0 notification-message">
                                                @if($notification->type === 'App\\Notifications\\FriendRequestAcceptedNotification')
                                                    Bạn đã đồng ý kết bạn với {{ $notification->data['senderName'] }}
                                                @else
                                                    {{ $notification->data['message'] }}
                                                @endif
                                            </p>
                                            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                </a>
                                @if($notification->type === 'App\\Notifications\\FriendRequestNotification')
                                    <div class="ms-2 mt-2">
                                        <button class="btn btn-sm btn-primary me-2 accept-friend" data-user-id="{{ $notification->data['user_id'] }}">
                                            <i class="fas fa-check"></i> Chấp nhận
                                        </button>
                                        <button class="btn btn-sm btn-danger reject-friend" data-user-id="{{ $notification->data['user_id'] }}">
                                            <i class="fas fa-times"></i> Từ chối
                                        </button>
                                    </div>
                                @endif
                                <button class="btn btn-sm btn-link text-danger ms-2 delete-notification" data-notification-id="{{ $notification->id }}">
                                    <i class="fas fa-times"></i>
                                </button>
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
    document.querySelectorAll('.accept-friend').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.userId;
            console.log('Accept button clicked:', { userId });
            handleFriendRequest('accept', userId);
        });
    });

    // Xử lý nút từ chối kết bạn
    document.querySelectorAll('.reject-friend').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.userId;
            console.log('Reject button clicked:', { userId });
            handleFriendRequest('reject', userId);
        });
    });

    // Xử lý nút xóa thông báo
    document.querySelectorAll('.delete-notification').forEach(button => {
        button.addEventListener('click', function() {
            const notificationId = this.dataset.notificationId;
            console.log('Delete button clicked:', notificationId);
            deleteNotification(notificationId);
        });
    });

    // Xử lý click vào thông báo
    document.querySelectorAll('.notification-item a').forEach(link => {
        link.addEventListener('click', function(e) {
            // Nếu click vào nút chấp nhận/từ chối, không chuyển trang
            if (e.target.closest('.accept-friend') || e.target.closest('.reject-friend')) {
                e.preventDefault();
                return;
            }
            
            // Nếu click vào nút xóa thông báo, không chuyển trang
            if (e.target.closest('.delete-notification')) {
                e.preventDefault();
                return;
            }
            
            // Nếu là thông báo kết bạn hoặc chấp nhận kết bạn, chuyển đến trang của người gửi
            const notificationType = this.closest('.notification-item').dataset.notificationType;
            if (notificationType === 'friend_request' || notificationType === 'friend_request_accepted') {
                e.preventDefault();
                const userId = this.closest('.notification-item').dataset.userId;
                window.location.href = `/posts/my_posts/${userId}`;
            }
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

function handleFriendRequest(action, userId) {
    console.log('Handling friend request:', { action, userId });
    
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
            // Tìm tất cả các thông báo liên quan đến user này
            const notifications = document.querySelectorAll(`.notification-item[data-user-id="${userId}"]`);
            notifications.forEach(noti => {
                const messageElement = noti.querySelector('.notification-message');
                if (messageElement) {
                    if (action === 'accept') {
                        messageElement.textContent = `Bạn đã đồng ý kết bạn với ${data.senderName}`;
                        // Thêm badge "Bạn bè"
                        const badge = document.createElement('span');
                        badge.className = 'badge bg-success ms-2';
                        badge.textContent = 'Bạn bè';
                        messageElement.parentElement.appendChild(badge);
                    } else {
                        messageElement.textContent = 'Bạn đã từ chối lời mời kết bạn này.';
                    }
                }

                // Xóa các nút chấp nhận/từ chối
                const buttonsContainer = noti.querySelector('.ms-2');
                if (buttonsContainer) {
                    buttonsContainer.innerHTML = '';
                }
            });

            // Cập nhật tất cả các nút kết bạn có cùng user_id
            document.querySelectorAll(`.friend-button[data-user-id="${userId}"]`).forEach(button => {
                if (action === 'accept') {
                    button.classList.remove('btn-outline-primary', 'btn-secondary');
                    button.classList.add('btn-primary');
                    button.innerHTML = '<i class="fas fa-user-friends"></i> Bạn bè';
                    // Cập nhật onclick handler
                    button.onclick = function(e) {
                        e.preventDefault();
                        toggleFriend(userId, this);
                    };
                } else {
                    button.classList.remove('btn-primary', 'btn-secondary');
                    button.classList.add('btn-outline-primary');
                    button.innerHTML = '<i class="fas fa-user-plus"></i> Kết bạn';
                }
            });

            // Hiển thị thông báo thành công
            showAlert(data.message);

            // Nếu từ chối, xóa thông báo sau 3 giây
            if (action === 'reject') {
                setTimeout(() => {
                    notifications.forEach(noti => noti.remove());
                    updateNotificationCount();
                }, 3000);
            }

            // Nếu chấp nhận, cập nhật danh sách bạn bè
            if (action === 'accept' && data.friend) {
                const friendsList = document.querySelector('.friends-list');
                if (friendsList) {
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
                }

                // Cập nhật số lượng bạn bè
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
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const url = '{{ route("notifications.clearAll") }}';
        console.log('Request URL:', url); // Debug log
        console.log('CSRF Token:', token); // Debug log

        fetch(url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(async response => {
            console.log('Response status:', response.status); // Debug log
            const data = await response.json();
            console.log('Response data:', data); // Debug log
            
            if (!response.ok) {
                throw new Error(data.message || `HTTP error! status: ${response.status}`);
            }
            return data;
        })
        .then(data => {
            if (data.success) {
                // Xóa tất cả các thông báo khỏi giao diện
                document.querySelectorAll('.notification-item').forEach(item => {
                    item.remove();
                });
                
                // Hiển thị thông báo trống
                const cardBody = document.querySelector('.card-body');
                cardBody.innerHTML = `
                    <div class="text-center py-4">
                        <i class="fas fa-bell fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Không có thông báo nào</p>
                    </div>
                `;
                
                // Cập nhật số lượng thông báo
                const countElement = document.querySelector('.notification-count');
                if (countElement) {
                    countElement.remove();
                }
                
                showAlert('Đã xóa tất cả thông báo thành công');
            } else {
                showAlert(data.error || 'Có lỗi xảy ra khi xóa thông báo', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Có lỗi xảy ra khi xóa thông báo: ' + error.message, 'danger');
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