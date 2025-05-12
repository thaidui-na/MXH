@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Tạo nhóm mới</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('groups.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên nhóm</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="cover_image" class="form-label">Ảnh bìa</label>
                            <input type="file" class="form-control @error('cover_image') is-invalid @enderror" id="cover_image" name="cover_image" accept="image/*">
                            @error('cover_image')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <div class="mt-2" id="cover_image_preview" style="display: none;">
                                <img src="" alt="Cover Preview" class="img-fluid" style="max-height: 200px;">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="avatar" class="form-label">Avatar nhóm</label>
                            <input type="file" class="form-control @error('avatar') is-invalid @enderror" id="avatar" name="avatar" accept="image/*">
                            @error('avatar')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <div class="mt-2" id="avatar_preview" style="display: none;">
                                <img src="" alt="Avatar Preview" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_private" name="is_private" value="1" {{ old('is_private') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_private">Nhóm riêng tư</label>
                            </div>
                            <small class="form-text text-muted">
                                Nhóm riêng tư yêu cầu phê duyệt trước khi cho phép thành viên tham gia
                            </small>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Tạo nhóm</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Preview ảnh bìa
document.getElementById('cover_image').addEventListener('change', function(e) {
    const preview = document.getElementById('cover_image_preview');
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.style.display = 'block';
            preview.querySelector('img').src = e.target.result;
        }
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
    }
});

// Preview avatar
document.getElementById('avatar').addEventListener('change', function(e) {
    const preview = document.getElementById('avatar_preview');
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.style.display = 'block';
            preview.querySelector('img').src = e.target.result;
        }
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
    }
});
</script>
@endpush

@endsection 