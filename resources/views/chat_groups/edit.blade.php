@extends('layouts.app')

@section('title', 'Chỉnh sửa nhóm ' . $group->name)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Chỉnh sửa nhóm chat</h5>
                </div>
                <div class="card-body">
                    {{-- Hiển thị lỗi validation nếu có --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                     {{-- Hiển thị thông báo lỗi chung nếu có --}}
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Form cập nhật nhóm --}}
                    <form action="{{ route('chat-groups.update', $group->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <!-- Avatar nhóm -->
                        <div class="mb-3">
                            <label class="form-label">Avatar nhóm</label>
                            <div class="d-flex align-items-center">
                                {{-- Hiển thị avatar hiện tại --}}
                                @if($group->avatar)
                                    <img src="{{ asset('storage/' . $group->avatar) }}" 
                                         id="avatar-preview"
                                         class="rounded-circle me-3" 
                                         style="width: 64px; height: 64px; object-fit: cover;">
                                @else
                                     {{-- Ảnh mặc định nếu chưa có avatar --}}
                                     <div id="avatar-preview" class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-3"
                                          style="width: 64px; height: 64px; font-size: 1.5rem;">
                                         {{ substr($group->name, 0, 1) }}
                                     </div>
                                @endif
                                {{-- Input để chọn file avatar mới --}}
                                <input type="file" name="avatar" id="avatar-input" class="form-control" accept="image/*">
                            </div>
                            @error('avatar')
                                <span class="text-danger d-block mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Tên nhóm -->
                        <div class="mb-3">
                            <label class="form-label">Tên nhóm <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $group->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Mô tả -->
                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $group->description) }}</textarea>
                             @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Thành viên -->
                        <div class="mb-3">
                            <label class="form-label">Thành viên (Admin không thể bị xóa)</label>
                            {{-- Danh sách người dùng để chọn/bỏ chọn --}}
                            <div class="border rounded p-3 bg-light" style="max-height: 250px; overflow-y: auto;">
                                @php
                                    // Lấy ID của tất cả thành viên hiện tại
                                    $currentMemberIds = $group->members()->pluck('user_id')->toArray();
                                    // Lấy tất cả user ngoại trừ admin hiện tại (để hiển thị và cho phép chọn)
                                    $availableUsers = \App\Models\User::where('id', '!=', auth()->id())->get(); 
                                @endphp
                                @foreach($availableUsers as $user)
                                    <div class="form-check">
                                        <input type="checkbox" 
                                               name="members[]" 
                                               value="{{ $user->id }}" 
                                               class="form-check-input" 
                                               id="user-{{ $user->id }}"
                                               {{-- Kiểm tra nếu user là thành viên hiện tại thì check vào ô --}}
                                               {{ in_array($user->id, $currentMemberIds) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="user-{{ $user->id }}">
                                             <img src="{{ $user->avatar_url }}" class="rounded-circle me-1" style="width: 20px; height: 20px; object-fit: cover;">
                                            {{ $user->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                             {{-- Hiển thị lỗi nếu có vấn đề với việc chọn thành viên --}}
                            @error('members')
                                <span class="text-danger d-block mt-1">{{ $message }}</span>
                             @enderror
                             @error('members.*')
                                <span class="text-danger d-block mt-1">{{ $message }}</span>
                            @enderror
                             <small class="form-text text-muted">Chọn ít nhất 1 thành viên khác ngoài bạn.</small>
                        </div>

                        <!-- Nút hành động -->
                        <div class="d-flex justify-content-between mt-4">
                             {{-- Nút hủy, quay về trang xem nhóm --}}
                            <a href="{{ route('chat-groups.show', $group) }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Hủy
                            </a>
                            {{-- Nút submit form cập nhật --}}
                            <button type="submit" class="btn btn-primary custom-update-btn">
                                <i class="fas fa-save me-1"></i> Lưu thay đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* CSS cho nút cập nhật */
.custom-update-btn {
    background-color: #4e73df; /* Màu nền chính */
    border-color: #4e73df; /* Màu viền */
    color: white; /* Màu chữ */
    padding: 0.5rem 1rem; /* Padding */
    border-radius: 0.35rem; /* Bo góc */
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Đổ bóng nhẹ */
    transition: all 0.3s ease; /* Hiệu ứng chuyển động */
}

/* Hiệu ứng khi hover nút */
.custom-update-btn:hover {
    background-color: #2e59d9; /* Màu nền đậm hơn khi hover */
    border-color: #2653d4; /* Màu viền đậm hơn */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15); /* Đổ bóng rõ hơn */
    transform: translateY(-1px); /* Nâng nút lên một chút */
}

/* Hiệu ứng khi nhấn nút */
.custom-update-btn:active {
    transform: translateY(0); /* Hạ nút xuống */
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); /* Giảm bóng */
}

/* CSS cho nút hủy */
.btn-secondary {
     padding: 0.5rem 1rem;
     border-radius: 0.35rem;
}
</style>
@endpush

@push('scripts')
<script>
// Script để xem trước avatar khi chọn file mới
document.addEventListener('DOMContentLoaded', function() {
    const avatarInput = document.getElementById('avatar-input');
    const avatarPreview = document.getElementById('avatar-preview');

    if (avatarInput && avatarPreview) {
        avatarInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Nếu preview là thẻ img
                    if (avatarPreview.tagName.toLowerCase() === 'img') {
                         avatarPreview.src = e.target.result;
                    } 
                    // Nếu preview là thẻ div (ảnh mặc định)
                    else {
                        // Tạo thẻ img mới để thay thế div
                        const newImg = document.createElement('img');
                        newImg.src = e.target.result;
                        newImg.id = 'avatar-preview';
                        newImg.className = 'rounded-circle me-3';
                        newImg.style.width = '64px';
                        newImg.style.height = '64px';
                        newImg.style.objectFit = 'cover';
                        avatarPreview.parentNode.replaceChild(newImg, avatarPreview);
                        // Cập nhật lại biến avatarPreview sau khi thay thế
                        // avatarPreview = newImg; // Dòng này có thể không cần thiết nếu không dùng lại biến này nữa
                    }
                }
                reader.readAsDataURL(file);
            }
        });
    }
});
</script>
@endpush
