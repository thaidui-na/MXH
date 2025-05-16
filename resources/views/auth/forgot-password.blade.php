@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0 rounded-lg mt-5">
                <div class="card-header bg-primary text-white">
                    <h3 class="text-center font-weight-light my-2">{{ __('Quên mật khẩu') }}</h3>
                </div>
                <div class="card-body">
                    <div class="small mb-3 text-center">
                        {{ __('Nhập email của bạn và chúng tôi sẽ gửi link đặt lại mật khẩu cho bạn.') }}
                    </div>
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="form-floating mb-3">
                            <input id="email" type="email" 
                                class="form-control @error('email') is-invalid @enderror" 
                                name="email" value="{{ old('email') }}" 
                                required autocomplete="email" autofocus
                                placeholder="name@example.com">
                            <label for="email">{{ __('Địa chỉ Email') }}</label>

                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                            <a class="small text-decoration-none" href="{{ route('login') }}">
                                <i class="fas fa-arrow-left me-1"></i>
                                {{ __('Quay lại đăng nhập') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-1"></i>
                                {{ __('Gửi link đặt lại mật khẩu') }}
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center py-3">
                    <div class="small">
                        {{ __('Chưa có tài khoản?') }}
                        <a href="{{ route('register') }}" class="text-decoration-none">
                            {{ __('Đăng ký ngay!') }}
                        </a>
                    </div>
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