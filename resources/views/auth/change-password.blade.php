@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0 rounded-lg mt-5">
                <div class="card-header bg-primary text-white">
                    <h3 class="text-center font-weight-light my-2">{{ __('Đổi mật khẩu') }}</h3>
                </div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.change') }}">
                        @csrf

                        <div class="form-floating mb-3">
                            <input id="current_password" type="password" 
                                class="form-control @error('current_password') is-invalid @enderror" 
                                name="current_password" required
                                placeholder="Nhập mật khẩu hiện tại">
                            <label for="current_password">{{ __('Mật khẩu hiện tại') }}</label>
                            @error('current_password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <input id="password" type="password" 
                                class="form-control @error('password') is-invalid @enderror" 
                                name="password" required
                                placeholder="Nhập mật khẩu mới">
                            <label for="password">{{ __('Mật khẩu mới') }}</label>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <input id="password_confirmation" type="password" 
                                class="form-control" 
                                name="password_confirmation" required
                                placeholder="Xác nhận mật khẩu mới">
                            <label for="password_confirmation">{{ __('Xác nhận mật khẩu mới') }}</label>
                        </div>

                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                            <a class="small text-decoration-none" href="{{ route('dashboard') }}">
                                <i class="fas fa-arrow-left me-1"></i>
                                {{ __('Quay lại trang chủ') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-key me-1"></i>
                                {{ __('Đổi mật khẩu') }}
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
.form-floating > .form-control:focus ~ label,
.form-floating > .form-control:not(:placeholder-shown) ~ label {
    transform: scale(.85) translateY(-0.75rem) translateX(0.15rem);
}
.card {
    backdrop-filter: blur(10px);
    background-color: rgba(255, 255, 255, 0.9);
}
</style>
@endpush 