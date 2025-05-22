@extends('layouts.app')

@section('title', 'Xóa tài khoản')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-times text-danger me-2"></i>
                        Xóa tài khoản
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Lưu ý quan trọng:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Việc xóa tài khoản là không thể hoàn tác.</li>
                            <li>Tất cả dữ liệu của bạn sẽ bị xóa vĩnh viễn.</li>
                            <li>Bạn sẽ không thể đăng nhập lại vào tài khoản này.</li>
                        </ul>
                    </div>

                    <form method="POST" action="{{ route('profile.delete.confirm') }}" class="mt-4">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="form-label">Bạn muốn:</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="delete_type" id="disable" value="disable" checked>
                                <label class="form-check-label" for="disable">
                                    <i class="fas fa-pause-circle me-2"></i>
                                    Vô hiệu hóa tài khoản (có thể khôi phục sau)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="delete_type" id="delete" value="delete">
                                <label class="form-check-label" for="delete">
                                    <i class="fas fa-trash-alt me-2"></i>
                                    Xóa tài khoản vĩnh viễn (không thể khôi phục)
                                </label>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label">Nhập mật khẩu để xác nhận</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('profile.edit') }}" class="btn btn-light">
                                <i class="fas fa-arrow-left me-2"></i>
                                Quay lại
                            </a>
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Bạn có chắc chắn muốn thực hiện hành động này?')">
                                <i class="fas fa-user-times me-2"></i>
                                Xác nhận xóa tài khoản
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 