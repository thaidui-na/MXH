@extends('layouts.app')

@section('title', 'Đổi mật khẩu')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            {{-- Thông báo thành công hoặc lỗi --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            {{-- Hiển thị lỗi validation chung --}}
            @if ($errors->any() && !$errors->has('current_password') && !$errors->has('password'))
                 <div class="alert alert-danger alert-dismissible fade show" role="alert">
                     Có lỗi xảy ra, vui lòng kiểm tra lại thông tin.
                     <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                 </div>
            @endif


            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-lock me-2"></i>Đổi mật khẩu</h5>
                </div>
                <div class="card-body">
                    {{-- Form đổi mật khẩu --}}
                    <form action="{{ route('password.update') }}" method="POST">
                        @csrf
                        {{-- Sử dụng method PUT --}}
                        @method('PUT')

                        {{-- Mật khẩu hiện tại --}}
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                            <input type="password" 
                                   class="form-control @error('current_password') is-invalid @enderror" 
                                   id="current_password" 
                                   name="current_password" 
                                   required>
                            {{-- Hiển thị lỗi cụ thể cho current_password --}}
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Mật khẩu mới --}}
                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu mới</label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   required>
                            {{-- Hiển thị lỗi cụ thể cho password --}}
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Xác nhận mật khẩu mới --}}
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
                            <input type="password" 
                                   class="form-control" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   required>
                            {{-- Không cần hiển thị lỗi riêng cho confirmation, lỗi 'confirmed' đã xử lý ở trên --}}
                        </div>

                        {{-- Nút submit --}}
                        <div class="d-grid">
                             <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Lưu thay đổi
                             </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="text-center mt-3">
                 <a href="{{ route('profile.edit') }}" class="text-decoration-none">
                    <i class="fas fa-arrow-left me-1"></i> Quay lại trang hồ sơ
                 </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
{{-- Thêm Font Awesome nếu chưa có trong layout chính --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    .card {
        border: none;
        border-radius: 10px;
    }
    .card-header {
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }
</style>
@endpush

