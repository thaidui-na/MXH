@extends('layouts.app')

@section('title', 'Đăng nhập')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0 rounded-lg mt-5">
                <div class="card-header bg-primary text-white">
                    <h3 class="text-center font-weight-light my-2">{{ __('Đăng nhập') }}</h3>
                </div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-floating mb-3">
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                id="email" name="email" value="{{ old('email') }}" 
                                required autocomplete="email" autofocus
                                placeholder="name@example.com">
                            <label for="email">{{ __('Địa chỉ Email') }}</label>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" name="password" required autocomplete="current-password"
                                placeholder="Nhập mật khẩu">
                            <label for="password">{{ __('Mật khẩu') }}</label>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="remember" 
                                id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                {{ __('Ghi nhớ đăng nhập') }}
                            </label>
                        </div>

                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                            <a class="small text-decoration-none" href="{{ route('password.request') }}">
                                <i class="fas fa-key me-1"></i>
                                {{ __('Quên mật khẩu?') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt me-1"></i>
                                {{ __('Đăng nhập') }}
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