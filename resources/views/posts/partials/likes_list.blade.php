@foreach($users as $user)
<div class="d-flex align-items-center mb-2">
    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
    <div>
        <h6 class="mb-0">{{ $user->name }}</h6>
        <small class="text-muted">{{ $user->email }}</small>
    </div>
</div>
@endforeach 