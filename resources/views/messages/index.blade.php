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
                        <div class="message-input-wrapper">
                            <div class="message-actions mb-2">
                                <button type="button" class="btn btn-light btn-sm" onclick="toggleEmojiPicker()">
                                    <i class="far fa-smile"></i>
                                </button>
                                <button type="button" class="btn btn-light btn-sm" onclick="toggleStickerPicker()">
                                    <i class="far fa-sticky-note"></i>
                                </button>
                            </div>

                            <!-- Emoji Picker -->
                            <div id="emoji-picker" class="emoji-picker" style="display: none;">
                                <!-- Tab navigation -->
                                <ul class="nav nav-tabs nav-fill mb-2">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#smileys">😊</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#gestures">👋</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#love">❤️</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#activities">⚽</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#food">🍔</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#animals">🐶</a>
                                    </li>
                                </ul>

                                <!-- Tab content -->
                                <div class="tab-content">
                                    <!-- Mặt cười -->
                                    <div class="tab-pane fade show active" id="smileys">
                                        <div class="emoji-list">
                                            <span onclick="insertEmoji('😀')">😀</span>
                                            <span onclick="insertEmoji('😃')">😃</span>
                                            <span onclick="insertEmoji('😄')">😄</span>
                                            <span onclick="insertEmoji('😁')">😁</span>
                                            <span onclick="insertEmoji('😅')">😅</span>
                                            <span onclick="insertEmoji('😂')">😂</span>
                                            <span onclick="insertEmoji('🤣')">🤣</span>
                                            <span onclick="insertEmoji('😊')">😊</span>
                                            <span onclick="insertEmoji('😇')">😇</span>
                                            <span onclick="insertEmoji('🙂')">🙂</span>
                                            <span onclick="insertEmoji('😉')">😉</span>
                                            <span onclick="insertEmoji('😌')">😌</span>
                                            <span onclick="insertEmoji('😍')">😍</span>
                                            <span onclick="insertEmoji('🥰')">🥰</span>
                                            <span onclick="insertEmoji('😘')">😘</span>
                                            <span onclick="insertEmoji('😋')">😋</span>
                                            <span onclick="insertEmoji('😎')">😎</span>
                                            <span onclick="insertEmoji('🤩')">🤩</span>
                                            <span onclick="insertEmoji('🥳')">🥳</span>
                                            <span onclick="insertEmoji('😏')">😏</span>
                                        </div>
                                    </div>

                                    <!-- Cử chỉ -->
                                    <div class="tab-pane fade" id="gestures">
                                        <div class="emoji-list">
                                            <span onclick="insertEmoji('👋')">👋</span>
                                            <span onclick="insertEmoji('🤚')">🤚</span>
                                            <span onclick="insertEmoji('🖐')">🖐</span>
                                            <span onclick="insertEmoji('✋')">✋</span>
                                            <span onclick="insertEmoji('🖖')">🖖</span>
                                            <span onclick="insertEmoji('👌')">👌</span>
                                            <span onclick="insertEmoji('🤌')">🤌</span>
                                            <span onclick="insertEmoji('🤏')">🤏</span>
                                            <span onclick="insertEmoji('✌️')">✌️</span>
                                            <span onclick="insertEmoji('🤞')">🤞</span>
                                            <span onclick="insertEmoji('🤟')">🤟</span>
                                            <span onclick="insertEmoji('🤘')">🤘</span>
                                            <span onclick="insertEmoji('👍')">👍</span>
                                            <span onclick="insertEmoji('👎')">👎</span>
                                            <span onclick="insertEmoji('👊')">👊</span>
                                        </div>
                                    </div>

                                    <!-- Tình yêu -->
                                    <div class="tab-pane fade" id="love">
                                        <div class="emoji-list">
                                            <span onclick="insertEmoji('❤️')">❤️</span>
                                            <span onclick="insertEmoji('🧡')">🧡</span>
                                            <span onclick="insertEmoji('💛')">💛</span>
                                            <span onclick="insertEmoji('💚')">💚</span>
                                            <span onclick="insertEmoji('💙')">💙</span>
                                            <span onclick="insertEmoji('💜')">💜</span>
                                            <span onclick="insertEmoji('🤎')">🤎</span>
                                            <span onclick="insertEmoji('🖤')">🖤</span>
                                            <span onclick="insertEmoji('🤍')">🤍</span>
                                            <span onclick="insertEmoji('💯')">💯</span>
                                            <span onclick="insertEmoji('💢')">💢</span>
                                            <span onclick="insertEmoji('💥')">💥</span>
                                            <span onclick="insertEmoji('💫')">💫</span>
                                            <span onclick="insertEmoji('💝')">💝</span>
                                            <span onclick="insertEmoji('💞')">💞</span>
                                        </div>
                                    </div>

                                    <!-- Hoạt động -->
                                    <div class="tab-pane fade" id="activities">
                                        <div class="emoji-list">
                                            <span onclick="insertEmoji('⚽')">⚽</span>
                                            <span onclick="insertEmoji('🏀')">🏀</span>
                                            <span onclick="insertEmoji('🏈')">🏈</span>
                                            <span onclick="insertEmoji('⚾')">⚾</span>
                                            <span onclick="insertEmoji('🎾')">🎾</span>
                                            <span onclick="insertEmoji('🏐')">🏐</span>
                                            <span onclick="insertEmoji('🎮')">🎮</span>
                                            <span onclick="insertEmoji('🎲')">🎲</span>
                                            <span onclick="insertEmoji('🎭')">🎭</span>
                                            <span onclick="insertEmoji('🎨')">🎨</span>
                                            <span onclick="insertEmoji('🎬')">🎬</span>
                                            <span onclick="insertEmoji('🎤')">🎤</span>
                                            <span onclick="insertEmoji('🎧')">🎧</span>
                                            <span onclick="insertEmoji('🎸')">🎸</span>
                                            <span onclick="insertEmoji('🎹')">🎹</span>
                                        </div>
                                    </div>

                                    <!-- Đồ ăn -->
                                    <div class="tab-pane fade" id="food">
                                        <div class="emoji-list">
                                            <span onclick="insertEmoji('🍕')">🍕</span>
                                            <span onclick="insertEmoji('🍔')">🍔</span>
                                            <span onclick="insertEmoji('🍟')">🍟</span>
                                            <span onclick="insertEmoji('🌭')">🌭</span>
                                            <span onclick="insertEmoji('🍿')">🍿</span>
                                            <span onclick="insertEmoji('🧂')">🧂</span>
                                            <span onclick="insertEmoji('🥓')">🥓</span>
                                            <span onclick="insertEmoji('🥚')">🥚</span>
                                            <span onclick="insertEmoji('🍳')">🍳</span>
                                            <span onclick="insertEmoji('🧇')">🧇</span>
                                            <span onclick="insertEmoji('🥞')">🥞</span>
                                            <span onclick="insertEmoji('🧈')">🧈</span>
                                            <span onclick="insertEmoji('🍞')">🍞</span>
                                            <span onclick="insertEmoji('🥐')">🥐</span>
                                            <span onclick="insertEmoji('🥨')">🥨</span>
                                        </div>
                                    </div>

                                    <!-- Động vật -->
                                    <div class="tab-pane fade" id="animals">
                                        <div class="emoji-list">
                                            <span onclick="insertEmoji('🐶')">🐶</span>
                                            <span onclick="insertEmoji('🐱')">🐱</span>
                                            <span onclick="insertEmoji('🐭')">🐭</span>
                                            <span onclick="insertEmoji('🐹')">🐹</span>
                                            <span onclick="insertEmoji('🐰')">🐰</span>
                                            <span onclick="insertEmoji('🦊')">🦊</span>
                                            <span onclick="insertEmoji('🐻')">🐻</span>
                                            <span onclick="insertEmoji('🐼')">🐼</span>
                                            <span onclick="insertEmoji('🐨')">🐨</span>
                                            <span onclick="insertEmoji('🐯')">🐯</span>
                                            <span onclick="insertEmoji('🦁')">🦁</span>
                                            <span onclick="insertEmoji('🐮')">🐮</span>
                                            <span onclick="insertEmoji('🐷')">🐷</span>
                                            <span onclick="insertEmoji('🐸')">🐸</span>
                                            <span onclick="insertEmoji('🐵')">🐵</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sticker Picker -->
                            <div id="sticker-picker" class="sticker-picker" style="display: none;">
                                <div class="sticker-list">
                                    <!-- Thêm danh sách sticker -->
                                    <img src="/stickers/1.png" onclick="selectSticker('1.png')" class="sticker-thumb">
                                    <img src="/stickers/2.png" onclick="selectSticker('2.png')" class="sticker-thumb">
                                    <!-- Thêm các sticker khác -->
                                    <img src="/stickers/3.png" onclick="selectSticker('3.png')" class="sticker-thumb">
                                    <img src="/stickers/4.png" onclick="selectSticker('4.png')" class="sticker-thumb">
                                    <img src="/stickers/5.png" onclick="selectSticker('5.png')" class="sticker-thumb">
                                    <img src="/stickers/6.png" onclick="selectSticker('6.png')" class="sticker-thumb">
                                    <img src="/stickers/7.png" onclick="selectSticker('7.png')" class="sticker-thumb">
                                    <img src="/stickers/8.png" onclick="selectSticker('8.png')" class="sticker-thumb">
                                    <img src="/stickers/9.png" onclick="selectSticker('9.png')" class="sticker-thumb">
                                    <img src="/stickers/10.png" onclick="selectSticker('10.png')" class="sticker-thumb">
                                    <img src="/stickers/11.png" onclick="selectSticker('11.png')" class="sticker-thumb">
                                    <img src="/stickers/12.png" onclick="selectSticker('12.png')" class="sticker-thumb">
                                </div>
                            </div>

                            <form id="message-form" action="{{ route('messages.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="receiver_id" value="{{ $selectedUser->id }}">
                                <input type="hidden" name="sticker" id="selected-sticker">
                                
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

.emoji-picker {
    width: 300px;
    background: white;
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 10px;
    position: absolute;
    bottom: 100%;
    left: 0;
    z-index: 1000;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

.nav-tabs .nav-link {
    padding: 5px;
    font-size: 1.2em;
}

.emoji-list {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 5px;
    padding: 10px;
}

.emoji-list span {
    cursor: pointer;
    font-size: 1.5em;
    text-align: center;
    padding: 5px;
    border-radius: 5px;
    transition: background-color 0.2s;
}

.emoji-list span:hover {
    background-color: #f0f0f0;
}

.tab-content {
    max-height: 200px;
    overflow-y: auto;
}

.sticker-picker {
    position: absolute;
    bottom: 100%;
    left: 0;
    background: white;
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 10px;
    max-height: 200px;
    overflow-y: auto;
    z-index: 1000;
}

.sticker-thumb {
    width: 60px;
    height: 60px;
    object-fit: contain;
}

.message-input-wrapper {
    position: relative;
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

function toggleEmojiPicker() {
    const picker = document.getElementById('emoji-picker');
    picker.style.display = picker.style.display === 'none' ? 'block' : 'none';
    document.getElementById('sticker-picker').style.display = 'none';
}

function toggleStickerPicker() {
    const picker = document.getElementById('sticker-picker');
    picker.style.display = picker.style.display === 'none' ? 'block' : 'none';
    document.getElementById('emoji-picker').style.display = 'none';
}

function insertEmoji(emoji) {
    const input = document.querySelector('input[name="content"]');
    input.value += emoji;
}

function selectSticker(stickerId) {
    document.getElementById('selected-sticker').value = stickerId;
    document.getElementById('message-form').dispatchEvent(new Event('submit'));
    document.getElementById('selected-sticker').value = '';
    document.getElementById('sticker-picker').style.display = 'none';
}
</script>
@endpush 