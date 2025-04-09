<div class="mb-3 {{ $message->sender_id === auth()->id() ? 'text-end' : '' }}">
    <div class="d-inline-block p-2 rounded {{ $message->sender_id === auth()->id() ? 'bg-primary text-white' : 'bg-light' }}" 
         style="max-width: 70%;">
        {{ $message->content }}
    </div>
    <div class="small text-muted">
        {{ $message->created_at->format('H:i') }}
        @if($message->sender_id === auth()->id())
            <i class="fas fa-check {{ $message->is_read ? 'text-primary' : '' }}"></i>
        @endif
    </div>
</div> 