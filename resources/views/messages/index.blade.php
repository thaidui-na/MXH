@extends('layouts.app')

@section('title', 'Tin nhắn')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Danh sách người dùng -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Danh sách người dùng</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" id="users-list">
                        @foreach($users as $user)
                            @php
                                // Lấy số tin nhắn chưa đọc từ người dùng này
                                $unreadCount = auth()->user()->getUnreadMessagesFrom($user->id);
                                // Lấy tin nhắn cuối cùng
                                $lastMessage = auth()->user()->getLastMessageWith($user->id);
                            @endphp
                            <a href="{{ route('messages.show', $user->id) }}" 
                               class="list-group-item list-group-item-action user-chat-item {{ $selectedUser && $selectedUser->id == $user->id ? 'active' : '' }}"
                               data-user-id="{{ $user->id }}">
                                <div class="d-flex align-items-center">
                                    <!-- Avatar người dùng -->
                                    <div class="position-relative">
                                        <img src="{{ $user->avatar_url }}" 
                                             class="rounded-circle me-2" 
                                             style="width: 40px; height: 40px; object-fit: cover;">
                                        @if($unreadCount > 0)
                                            <!-- Badge hiển thị số tin nhắn chưa đọc -->
                                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                                {{ $unreadCount }}
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <!-- Thông tin người dùng và tin nhắn -->
                                    <div class="flex-grow-1 min-width-0">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">{{ $user->name }}</h6>
                                            @if($lastMessage)
                                                <small class="text-muted">
                                                    {{ $lastMessage->created_at->diffForHumans(null, true) }}
                                                </small>
                                            @endif
                                        </div>
                                        @if($lastMessage)
                                            <p class="mb-0 small text-truncate {{ !$lastMessage->is_read && $lastMessage->receiver_id === auth()->id() ? 'fw-bold' : 'text-muted' }}">
                                                {{ $lastMessage->sender_id === auth()->id() ? 'Bạn: ' : '' }}
                                                {{ Str::limit($lastMessage->content, 30) }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Khung chat -->
        <div class="col-md-9">
            @if($selectedUser)
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <img src="{{ $selectedUser->avatar_url }}" 
                                 class="rounded-circle me-2" 
                                 style="width: 40px; height: 40px; object-fit: cover;">
                            <h5 class="mb-0">{{ $selectedUser->name }}</h5>
                        </div>
                    </div>
                    <div class="card-body" style="height: 400px; overflow-y: auto;" id="message-container">
                        @include('messages.partials.message-list')
                    </div>
                    <div class="card-footer">
                        <form id="message-form" action="{{ route('messages.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="receiver_id" value="{{ $selectedUser->id }}">
                            
                            <!-- Preview hình ảnh -->
                            <div id="image-preview" class="mb-2" style="display: none;">
                                <div class="position-relative d-inline-block">
                                    <img src="" alt="Preview" style="max-height: 100px; max-width: 200px;">
                                    <button type="button" class="btn-close position-absolute top-0 end-0" 
                                            style="background-color: white; border-radius: 50%;"
                                            onclick="removeImage()"></button>
                                </div>
                            </div>

                            <div class="input-group">
                                <!-- Input nhập tin nhắn -->
                                <input type="text" name="content" class="form-control" 
                                       placeholder="Nhập tin nhắn...">
                                
                                <!-- Button upload ảnh -->
                                <label class="btn btn-outline-secondary" for="image-upload">
                                    <i class="fas fa-image"></i>
                                </label>
                                <input type="file" id="image-upload" name="image" 
                                       accept="image/*" style="display: none;">
                                
                                <!-- Button gửi -->
                                <button type="submit" class="btn btn-primary">Gửi</button>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <div class="alert alert-info">
                    Chọn một người dùng để bắt đầu cuộc trò chuyện
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* CSS cho danh sách người dùng */
.user-chat-item {
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
}

.user-chat-item:hover {
    background-color: rgba(0,0,0,0.05);
}

.user-chat-item.active {
    border-left-color: #0d6efd;
    background-color: rgba(13,110,253,0.1);
}

.user-chat-item.has-unread {
    background-color: rgba(13,110,253,0.05);
}

/* Giới hạn chiều cao danh sách và thêm scroll */
.list-group {
    max-height: 600px;
    overflow-y: auto;
}

/* Đảm bảo tin nhắn dài không bị tràn */
.min-width-0 {
    min-width: 0;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const messageContainer = document.getElementById('message-container');
    const messageForm = document.getElementById('message-form');
    const usersList = document.getElementById('users-list');
    
    // Cuộn xuống cuối cùng
    function scrollToBottom() {
        messageContainer.scrollTop = messageContainer.scrollHeight;
    }
    
    scrollToBottom();
    
    // Xử lý gửi tin nhắn bằng Ajax
    if (messageForm) {
        messageForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                messageContainer.insertAdjacentHTML('beforeend', data.message);
                scrollToBottom();
                messageForm.reset();
            });
        });
    }
    
    // Polling để kiểm tra tin nhắn mới
    if (messageContainer) {
        setInterval(() => {
            const receiverId = document.querySelector('input[name="receiver_id"]').value;
            fetch(`/messages/${receiverId}/new`)
                .then(response => response.json())
                .then(data => {
                    if (data.messages) {
                        messageContainer.insertAdjacentHTML('beforeend', data.messages);
                        scrollToBottom();
                    }
                });
        }, 5000);
    }

    // Thêm function cập nhật danh sách người dùng
    function updateUsersList() {
        fetch('/messages/users/status')
            .then(response => response.json())
            .then(data => {
                // Cập nhật trạng thái cho từng người dùng
                data.users.forEach(user => {
                    const userItem = document.querySelector(`.user-chat-item[data-user-id="${user.id}"]`);
                    if (userItem) {
                        // Cập nhật số tin nhắn chưa đọc
                        const badge = userItem.querySelector('.badge');
                        if (user.unread_count > 0) {
                            if (badge) {
                                badge.textContent = user.unread_count;
                            } else {
                                const avatarContainer = userItem.querySelector('.position-relative');
                                avatarContainer.insertAdjacentHTML('beforeend', `
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        ${user.unread_count}
                                    </span>
                                `);
                            }
                            userItem.classList.add('has-unread');
                        } else {
                            if (badge) {
                                badge.remove();
                            }
                            userItem.classList.remove('has-unread');
                        }

                        // Cập nhật tin nhắn cuối cùng nếu có
                        if (user.last_message) {
                            const messagePreview = userItem.querySelector('p.small');
                            if (messagePreview) {
                                messagePreview.textContent = user.last_message.sender_id === {{ auth()->id() }}
                                    ? `Bạn: ${user.last_message.content}`
                                    : user.last_message.content;
                                messagePreview.className = `mb-0 small text-truncate ${
                                    !user.last_message.is_read && user.last_message.receiver_id === {{ auth()->id() }}
                                        ? 'fw-bold'
                                        : 'text-muted'
                                }`;
                            }
                        }
                    }
                });
            });
    }

    // Cập nhật danh sách người dùng mỗi 5 giây
    setInterval(updateUsersList, 5000);
});
</script>
@endpush 