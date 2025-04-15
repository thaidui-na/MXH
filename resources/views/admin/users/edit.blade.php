@extends('admin.layout')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Chỉnh sửa người dùng</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label class="form-label">Tên</label>
                <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Mật khẩu mới (để trống nếu không đổi)</label>
                <input type="password" name="password" class="form-control">
            </div>

            <div class="mb-3">
                <div class="form-check">
                    <input type="checkbox" name="is_admin" class="form-check-input" value="1" 
                           {{ $user->is_admin ? 'checked' : '' }}>
                    <label class="form-check-label">Là Admin</label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Cập nhật</button>
            <a href="{{ route('admin.users') }}" class="btn btn-secondary">Hủy</a>
        </form>
    </div>
</div>
@endsection
