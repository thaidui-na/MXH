@extends('layouts.app')

@section('title', $group->name)

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Khung chat -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            @if($group->avatar)
                                <img src="{{ asset('storage/' . $group->avatar) }}" 
                                     class="rounded-circle me-2" 
                                     style="width: 40px; height: 40px; object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2"
                                     style="width: 40px; height: 40px;">
                                    {{ substr($group->name, 0, 1) }}
                                </div>
                            @endif
                            <div>
                                <h5 class="mb-0">{{ $group->name }}</h5>
                                <small class="text-muted">{{ $group->members->count() }} thành viên</small>
                            </div>
                        </div>
                        @if($group->members()->where('user_id', auth()->id())->where(function($query) use ($group) {
                            $query->where('is_admin_group_chat', true)->orWhere('user_id', $group->created_by);
                        })->exists())
                            <div class="btn-group">
                                <a href="{{ route('chat-groups.edit', $group) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i> Chỉnh sửa
                                </a>
                                <form action="{{ route('chat-groups.destroy', $group) }}" 
                                      method="POST" 
                                      class="d-inline delete-group-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i> Xóa nhóm
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="message-container">
                        @foreach($messages as $message)
                            @include('chat_groups.partials.message', ['message' => $message])
                        @endforeach
                    </div>
                </div>
                <div class="card-footer">
                    <form id="group-message-form" action="{{ route('group.messages.store', $group) }}" method="POST">
                        @csrf
                        <div class="input-group">
                            <input type="text" name="content" class="form-control" placeholder="Nhập tin nhắn..." required>
                            <button type="submit" class="btn btn-primary">Gửi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Danh sách thành viên -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Thành viên nhóm</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($group->members as $member)
                            <div class="list-group-item">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $member->avatar_url }}" 
                                         class="rounded-circle me-2" 
                                         style="width: 32px; height: 32px; object-fit: cover;">
                                    <div>
                                        <h6 class="mb-0">
                                            {{ $member->name }}
                                            @if($member->id === $group->created_by)
                                                <span class="badge bg-primary">Người tạo</span>
                                            @endif
                                            @if($member->pivot->is_admin_group_chat)
                                                <span class="badge bg-info">Admin</span>
                                            @endif
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.message {
    margin-bottom: 1rem;
}

.message-wrapper {
    max-width: 70%;
}

.message-bubble {
    display: inline-block;
    position: relative;
    border-radius: 1rem !important;
    padding: 0.5rem 1rem !important;
}

.message.text-end .message-bubble {
    border-top-right-radius: 0.3rem !important;
}

.message:not(.text-end) .message-bubble {
    border-top-left-radius: 0.3rem !important;
}

.message-time {
    font-size: 0.75rem;
    margin-top: 0.25rem;
}

#message-container {
    height: 400px;
    overflow-y: auto;
    scroll-behavior: smooth;
    padding: 1rem;
    background-color: #f8f9fa;
}

#message-container::-webkit-scrollbar {
    width: 6px;
}

#message-container::-webkit-scrollbar-track {
    background: #f1f1f1;
}

#message-container::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

#message-container::-webkit-scrollbar-thumb:hover {
    background: #555;
}

.message-input-wrapper {
    position: relative;
    padding: 1rem;
    background-color: #fff;
    border-top: 1px solid rgba(0,0,0,.125);
}

.input-group {
    background-color: #fff;
}

.input-group .form-control {
    border-radius: 1.5rem;
    padding-left: 1rem;
    padding-right: 1rem;
    border: 1px solid #dee2e6;
}

.input-group .btn {
    border-radius: 1.5rem;
    margin-left: 0.5rem;
    padding-left: 1.5rem;
    padding-right: 1.5rem;
}

/* Hiệu ứng hover cho tin nhắn */
.message-bubble:hover {
    opacity: 0.95;
}

/* Màu sắc cho tin nhắn */
.message.text-end .message-bubble {
    background-color: #0d6efd !important;
    color: white;
}

.message:not(.text-end) .message-bubble {
    background-color: #e9ecef;
    color: #212529;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const messageContainer = document.getElementById('message-container');
    const messageForm = document.getElementById('group-message-form');
    const groupId = '{{ $group->id }}'; // Lấy ID của nhóm chat
    let lastMessageId = '{{ $messages->last() ? $messages->last()->id : 0 }}'; // ID tin nhắn cuối cùng
    
    // Hàm cuộn xuống cuối
    function scrollToBottom() {
        if (messageContainer) {
            messageContainer.scrollTop = messageContainer.scrollHeight;
        }
    }
    
    scrollToBottom();

    // Hàm kiểm tra tin nhắn mới
    function checkNewMessages() {
        fetch(`/chat-groups/${groupId}/messages/check?last_id=${lastMessageId}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.messages && data.messages.length > 0) {
                // Thêm các tin nhắn mới vào container
                data.messages.forEach(messageHtml => {
                    messageContainer.insertAdjacentHTML('beforeend', messageHtml);
                });
                
                // Cập nhật ID tin nhắn cuối cùng
                lastMessageId = data.lastMessageId;
                
                // Cuộn xuống nếu người dùng đang ở cuối
                if (messageContainer.scrollHeight - messageContainer.scrollTop <= messageContainer.clientHeight + 100) {
                    scrollToBottom();
                }
            }
        })
        .catch(error => console.error('Error checking new messages:', error));
    }

    // Kiểm tra tin nhắn mới mỗi 3 giây
    setInterval(checkNewMessages, 3000);
    
    // Xử lý gửi tin nhắn
    if (messageForm) {
        messageForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Thêm tin nhắn mới vào container
                    messageContainer.insertAdjacentHTML('beforeend', data.messageHtml);
                    
                    // Cập nhật ID tin nhắn cuối cùng
                    if (data.message && data.message.id) {
                        lastMessageId = data.message.id;
                    }
                    
                    // Cuộn xuống cuối
                    scrollToBottom();
                    
                    // Reset form
                    this.reset();
                } else {
                    alert(data.error || 'Có lỗi xảy ra khi gửi tin nhắn');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi gửi tin nhắn');
            });
        });
    }

    // Thêm xử lý xóa nhóm
    const deleteForm = document.querySelector('.delete-group-form');
    if (deleteForm) {
        deleteForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (confirm('Bạn có chắc chắn muốn xóa nhóm chat này? Hành động này không thể hoàn tác.')) {
                const formData = new FormData(this);
                
                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        window.location.href = data.redirect;
                    } else {
                        alert(data.message || 'Có lỗi xảy ra khi xóa nhóm chat');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi xóa nhóm chat');
                });
            }
        });
    }
});
</script>
@endpush
