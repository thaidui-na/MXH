<div class="mb-3 {{ $message->sender_id === auth()->id() ? 'text-end' : '' }}">
    <div class="d-inline-block">  
        <!-- Hiển thị nội dung tin nhắn và emoji nếu có -->
        @if($message->content || $message->emoji)
            <div class="p-2 rounded {{ $message->sender_id === auth()->id() ? 'bg-primary text-white' : 'bg-light' }}">
                @if($message->content)
                    {!! $message->content !!}
                @endif
                @if($message->emoji)
                    <span class="emoji">{!! $message->emoji !!}</span>
                @endif
            </div>
        @endif

        <!-- Hiển thị hình ảnh nếu có -->
        @if($message->hasImage())
            <div class="{{ ($message->content || $message->emoji) ? 'mt-1' : '' }}">
                <img src="{{ $message->getImageUrl() }}" 
                     alt="Message image" 
                     class="rounded img-fluid message-image" 
                     style="max-width: 200px; cursor: pointer;"
                     onclick="showFullImage(this.src)">
            </div>
        @endif

        <!-- Hiển thị sticker nếu có -->
        @if($message->hasSticker())
            <div class="{{ ($message->content || $message->emoji || $message->hasImage()) ? 'mt-1' : '' }}">
                <img src="{{ $message->getStickerUrl() }}" 
                     alt="Sticker" 
                     class="sticker-image"
                     style="max-width: 120px;">
            </div>
        @endif

        <!-- Thời gian và trạng thái đã đọc -->
        <div class="small text-muted">
            {{ $message->created_at->format('H:i') }}
            @if($message->sender_id === auth()->id())
                <i class="fas fa-check {{ $message->is_read ? 'text-primary' : '' }}"></i>
            @endif
        </div>
    </div>
</div>

<script>
function insertEmoji(emoji) {
    const input = document.querySelector('input[name="content"]');
    input.value += emoji;
    // Tự động gửi tin nhắn khi chọn emoji
    document.getElementById('message-form').dispatchEvent(new Event('submit'));
}

function selectSticker(stickerId) {
    document.getElementById('selected-sticker').value = stickerId;
    // Tự động gửi tin nhắn khi chọn sticker
    document.getElementById('message-form').dispatchEvent(new Event('submit'));
    document.getElementById('selected-sticker').value = '';
    document.getElementById('sticker-picker').style.display = 'none';
}
</script> 