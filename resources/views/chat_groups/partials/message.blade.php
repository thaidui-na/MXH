<div class="message mb-3 {{ $message->sender_id === auth()->id() ? 'text-end' : '' }}">
    <div class="message-wrapper d-inline-block">
        @if($message->sender_id !== auth()->id())
            <div class="d-flex align-items-center mb-1">
                <img src="{{ $message->sender->avatar_url }}" 
                     class="rounded-circle me-2" 
                     style="width: 24px; height: 24px; object-fit: cover;">
                <small class="text-muted">{{ $message->sender->name }}</small>
            </div>
        @endif
        
        <div class="message-bubble p-2 rounded {{ $message->sender_id === auth()->id() ? 'bg-primary text-white' : 'bg-light' }}">
            {{ $message->content }}
        </div>
        
        <div class="message-time mt-1">
            <small class="text-muted">
                {{ $message->created_at->format('H:i') }}
            </small>
        </div>
    </div>
</div>
