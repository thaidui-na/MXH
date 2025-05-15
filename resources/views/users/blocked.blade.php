@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">Người dùng đã chặn</h5>
        </div>
        <div class="card-body">
            @if($blockedUsers->count() > 0)
                <div class="list-group">
                    @foreach($blockedUsers as $blockedUser)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <img src="{{ $blockedUser->avatar_url }}" 
                                     alt="{{ $blockedUser->name }}" 
                                     class="rounded-circle me-3"
                                     style="width: 40px; height: 40px; object-fit: cover;">
                                <div>
                                    <h6 class="mb-0">{{ $blockedUser->name }}</h6>
                                    <small class="text-muted">{{ $blockedUser->email }}</small>
                                </div>
                            </div>
                            <button class="btn btn-success btn-sm unblock-user" 
                                    data-user-id="{{ $blockedUser->id }}"
                                    data-user-name="{{ $blockedUser->name }}">
                                <i class="fas fa-unlock me-1"></i>
                                Bỏ chặn
                            </button>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center text-muted my-4">Bạn chưa chặn người dùng nào.</p>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('.unblock-user').forEach(button => {
    button.addEventListener('click', function() {
        const userId = this.dataset.userId;
        const userName = this.dataset.userName;
        
        if (confirm(`Bạn có chắc chắn muốn bỏ chặn người dùng ${userName}?`)) {
            fetch(`/users/${userId}/unblock`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Xóa phần tử khỏi danh sách
                    this.closest('.list-group-item').remove();
                    
                    // Kiểm tra nếu không còn người dùng nào bị chặn
                    if (document.querySelectorAll('.list-group-item').length === 0) {
                        document.querySelector('.card-body').innerHTML = 
                            '<p class="text-center text-muted my-4">Bạn chưa chặn người dùng nào.</p>';
                    }
                } else {
                    alert(data.error || 'Có lỗi xảy ra');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra');
            });
        }
    });
});
</script>
@endpush
@endsection 