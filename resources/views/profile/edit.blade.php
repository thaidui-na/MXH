@extends('layouts.app')

@section('title', 'Chỉnh sửa thông tin cá nhân')

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- Thông tin cá nhân -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <img src="{{ auth()->user()->avatar_url }}" 
                         alt="{{ auth()->user()->name }}" 
                         class="rounded-circle mb-3"
                         style="width: 120px; height: 120px; object-fit: cover;">
                    <h5 class="mb-1">{{ auth()->user()->name }}</h5>
                    <p class="text-muted mb-3">{{ auth()->user()->email }}</p>
                    <p class="text-muted small mb-0">
                        Thành viên từ {{ auth()->user()->created_at->format('d/m/Y') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Tab content -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" id="profile-tab" data-bs-toggle="tab" href="#profile">
                                <i class="fas fa-user me-2"></i>Thông tin cá nhân
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="blocked-tab" data-bs-toggle="tab" href="#blocked">
                                <i class="fas fa-user-slash me-2"></i>Người dùng đã chặn
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <div class="tab-content">
                        <!-- Tab thông tin cá nhân -->
                        <div class="tab-pane fade show active" id="profile">
                            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label for="avatar" class="form-label">Avatar</label>
                                    <input type="file" class="form-control" id="avatar" name="avatar">
                                </div>

                                <div class="mb-3">
                                    <label for="name" class="form-label">Họ tên</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', auth()->user()->name) }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="phone" class="form-label">Số điện thoại</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" value="{{ old('phone', auth()->user()->phone) }}">
                                </div>

                                <div class="mb-3">
                                    <label for="birthday" class="form-label">Ngày sinh</label>
                                    <input type="date" class="form-control" id="birthday" name="birthday" value="{{ old('birthday', auth()->user()->birthday ? auth()->user()->birthday->format('Y-m-d') : '') }}">
                                </div>

                                <div class="mb-3">
                                    <label for="bio" class="form-label">Giới thiệu bản thân</label>
                                    <textarea class="form-control" id="bio" name="bio" rows="3">{{ old('bio', auth()->user()->bio) }}</textarea>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Lưu thay đổi
                                </button>

                                <a href="{{ route('profile.delete') }}" class="btn btn-outline-danger ms-2">
                                    <i class="fas fa-user-times me-2"></i>Xóa tài khoản
                                </a>
                            </form>
                        </div>

                        <!-- Tab người dùng đã chặn -->
                        <div class="tab-pane fade" id="blocked">
                            @if(auth()->user()->blockedUsers->count() > 0)
                                <div class="list-group">
                                    @foreach(auth()->user()->blockedUsers as $blockedUser)
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
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý form bỏ chặn người dùng
    document.querySelectorAll('.unblock-user').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.userId;
            const userName = this.dataset.userName;
            const userElement = this.closest('.list-group-item');

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
                    // Xóa phần tử người dùng khỏi danh sách với hiệu ứng fade out
                    userElement.style.transition = 'opacity 0.3s ease';
                    userElement.style.opacity = '0';
                    setTimeout(() => {
                        userElement.remove();
                        // Kiểm tra nếu không còn người dùng nào bị chặn
                        if (document.querySelectorAll('#blocked .list-group-item').length === 0) {
                            document.querySelector('#blocked').innerHTML = `
                                <div class="text-center py-4">
                                    <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Bạn chưa chặn người dùng nào.</p>
                                </div>
                            `;
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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    .profile-info h6 {
        font-size: 0.9rem;
        color: #6c757d;
    }
    .profile-info p {
        font-size: 1rem;
    }
    .list-group-item {
        transition: all 0.3s ease;
    }
    .list-group-item:hover {
        background-color: #f8f9fa;
    }
    .unblock-user {
        transition: all 0.2s ease;
    }
    .unblock-user:hover {
        background-color: #28a745;
        color: white;
    }
</style>
@endpush 