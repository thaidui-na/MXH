@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0 rounded-lg mt-5">
                <div class="card-header bg-primary text-white">
                    <h3 class="text-center font-weight-light my-2">{{ __('Đặt lại mật khẩu') }}</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="form-floating mb-3">
                            <input id="email" type="email" 
                                class="form-control @error('email') is-invalid @enderror" 
                                name="email" value="{{ $email ?? old('email') }}" 
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
                            <input id="password" type="password" 
                                class="form-control @error('password') is-invalid @enderror" 
                                name="password" required autocomplete="new-password"
                                placeholder="Nhập mật khẩu mới">
                            <label for="password">{{ __('Mật khẩu mới') }}</label>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <input id="password-confirm" type="password" 
                                class="form-control" 
                                name="password_confirmation" required 
                                autocomplete="new-password"
                                placeholder="Xác nhận mật khẩu mới">
                            <label for="password-confirm">{{ __('Xác nhận mật khẩu mới') }}</label>
                        </div>

                        <div class="d-flex align-items-center justify-content-end mt-4 mb-0">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-key me-1"></i>
                                {{ __('Đặt lại mật khẩu') }}
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center py-3">
                    <div class="small">
                        <a href="{{ route('login') }}" class="text-decoration-none">
                            <i class="fas fa-arrow-left me-1"></i>
                            {{ __('Quay lại đăng nhập') }}
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