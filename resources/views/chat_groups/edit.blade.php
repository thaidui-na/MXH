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
                    <form action="{{ route('chat-groups.update', $group) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <!-- Avatar nhóm -->
                        <div class="mb-3">
                            <label class="form-label">Avatar nhóm</label>
                            <div class="d-flex align-items-center">
                                @if($group->avatar)
                                    <img src="{{ asset('storage/' . $group->avatar) }}" 
                                         class="rounded-circle me-3" 
                                         style="width: 64px; height: 64px; object-fit: cover;">
                                @endif
                                <input type="file" name="avatar" class="form-control" accept="image/*">
                            </div>
                            @error('avatar')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Tên nhóm -->
                        <div class="mb-3">
                            <label class="form-label">Tên nhóm</label>
                            <input type="text" name="name" class="form-control" 
                                   value="{{ old('name', $group->name) }}" required>
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Mô tả -->
                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description', $group->description) }}</textarea>
                            @error('description')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Thành viên -->
                        <div class="mb-3">
                            <label class="form-label">Thành viên</label>
                            <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                                @foreach($users as $user)
                                    <div class="form-check">
                                        <input type="checkbox" name="members[]" value="{{ $user->id }}" 
                                               class="form-check-input" 
                                               {{ in_array($user->id, $currentMembers) ? 'checked' : '' }}>
                                        <label class="form-check-label">{{ $user->name }}</label>
                                    </div>
                                @endforeach
                            </div>
                            @error('members')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Nút xóa và cập nhật -->
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="{{ route('chat-groups.show', $group) }}" class="btn btn-secondary me-2">
                                    <i class="fas fa-times me-1"></i> Hủy
                                </a>
                                <button type="submit" class="btn btn-primary custom-update-btn">
                                    <i class="fas fa-save me-1"></i> Cập nhật nhóm
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Kiểm tra form cập nhật 
    const updateForm = document.querySelector('form[action*="chat-groups"][action*="update"]');
    if (updateForm) {
        console.log('Found update form, ensuring it works correctly');
        
        // Đảm bảo form hoạt động như mong đợi, ghi đè bất kỳ sự kiện nào khác
        updateForm.addEventListener('submit', function(e) {
            console.log('Update form submitted');
            // Để form hoạt động bình thường
        });
    }
    
    // Loại bỏ bất kỳ event listener nào có thể đang can thiệp không đúng cách
    const deleteButtons = document.querySelectorAll('form.delete-group-form button');
    deleteButtons.forEach(button => {
        button.disabled = false; // Đảm bảo nút xóa không bị vô hiệu hóa
    });
});
</script>
@endpush

@push('styles')
<style>
.custom-update-btn {
    background-color: #4e73df;
    border-color: #4e73df;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.custom-update-btn:hover {
    background-color: #2e59d9;
    border-color: #2653d4;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    transform: translateY(-2px);
}

.custom-update-btn:active {
    transform: translateY(0);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}
</style>
@endpush
@endsection
