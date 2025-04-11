<a href="{{ route('chat-groups.show', $group) }}" 
   class="list-group-item list-group-item-action">
    <div class="d-flex align-items-center">
        <div class="flex-shrink-0">
            @if($group->avatar)
                <img src="{{ asset('storage/' . $group->avatar) }}" 
                     class="rounded-circle" 
                     style="width: 40px; height: 40px; object-fit: cover;">
            @else
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                     style="width: 40px; height: 40px;">
                    {{ substr($group->name, 0, 1) }}
                </div>
            @endif
        </div>
        <div class="flex-grow-1 ms-3">
            <h6 class="mb-0">{{ $group->name }}</h6>
            <small class="text-muted">
                {{ $group->members->count() }} thành viên
            </small>
        </div>
    </div>
</a>
