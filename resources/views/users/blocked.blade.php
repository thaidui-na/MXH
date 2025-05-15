@extends('layouts.app')

@section('title', 'Người dùng đã chặn')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Người dùng đã chặn</h4>
                </div>
                <div class="card-body">
                    @if($blockedUsers->count() > 0)
                        <div class="list-group">
                            @foreach($blockedUsers as $user)
                                <div class="list-group-item" id="blocked-user-{{ $user->id }}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('images/default-avatar.jpg') }}"
                                                 class="rounded-circle me-3"
                                                 style="width: 50px; height: 50px; object-fit: cover;"
                                                 alt="{{ $user->name }}'s avatar">
                                            <div>
                                                <h5 class="mb-1">{{ $user->name }}</h5>
                                                <p class="text-muted mb-0">{{ $user->email }}</p>
                                            </div>
                                        </div>
                                        <div>
                                            <form action="{{ route('users.unblock', $user->id) }}" 
                                                  method="POST" 
                                                  class="d-inline unblock-form"
                                                  data-user-id="{{ $user->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-primary">
                                                    <i class="fas fa-user-check me-1"></i> Bỏ chặn
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-4">
                            {{ $blockedUsers->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Bạn chưa chặn người dùng nào.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý form bỏ chặn người dùng
    document.querySelectorAll('.unblock-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const userId = this.dataset.userId;
            const userElement = document.getElementById(`blocked-user-${userId}`);

            fetch(this.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    _method: 'DELETE'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Xóa phần tử người dùng khỏi danh sách với hiệu ứng fade out
                    userElement.style.transition = 'opacity 0.3s ease';
                    userElement.style.opacity = '0';
                    setTimeout(() => {
                        userElement.remove();
                        // Kiểm tra nếu không còn người dùng nào bị chặn
                        if (document.querySelectorAll('.list-group-item').length === 0) {
                            location.reload(); // Tải lại trang để hiển thị thông báo "chưa chặn người dùng nào"
                        }
                    }, 300);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi bỏ chặn người dùng. Vui lòng thử lại.');
            });
        });
    });
});
</script>
@endpush

@push('styles')
<style>
.list-group-item {
    transition: all 0.3s ease;
}
.list-group-item:hover {
    background-color: #f8f9fa;
}
.btn-outline-primary:hover {
    background-color: #0d6efd;
    color: white;
}
</style>
@endpush 