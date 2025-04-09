<div class="mb-3 {{ $message->sender_id === auth()->id() ? 'text-end' : '' }}">
    <div class="d-inline-block">
        <!-- Hiển thị nội dung tin nhắn nếu có -->
        @if($message->content)
            <div class="p-2 rounded {{ $message->sender_id === auth()->id() ? 'bg-primary text-white' : 'bg-light' }}" 
                 style="max-width: 70%;">
                {{ $message->content }}
            </div>
        @endif

        <!-- Hiển thị hình ảnh nếu có -->
        @if($message->hasImage())
            <div class="{{ $message->content ? 'mt-1' : '' }}">
                <img src="{{ $message->getImageUrl() }}" 
                     alt="Message image" 
                     class="rounded img-fluid message-image" 
                     style="max-width: 200px; cursor: pointer;"
                     onclick="showFullImage(this.src)">
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