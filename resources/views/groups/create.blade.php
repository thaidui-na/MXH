@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Tạo Nhóm Mới</h2>

    <form action="{{ route('groups.store') }}" method="POST" enctype="multipart/form-data" class="mt-4">
        @csrf

        <div class="mb-3">
            <label class="form-label">Ten Nhóm</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4"></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">chọn ảnh làm avata nhóm</label>
            <input type="file" name="image" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Privacy</label>
            <select name="privacy" class="form-select" required>
                <option value="public">Public</option>
                <option value="private">Private</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Xác Nhận</button>
    </form>
</div>
@endsection
