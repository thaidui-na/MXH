@extends('layouts.app')

@section('title', 'Chỉnh sửa thông tin cá nhân')

@section('content')
<div class="container py-5">
    <div class="row">
        <!-- Sidebar với avatar và thông tin cơ bản -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <!-- Hiển thị avatar hiện tại -->
                    <img src="{{ auth()->user()->avatar_url }}" 
                         alt="Avatar" 
                         class="rounded-circle img-fluid mb-3" 
                         style="width: 150px; height: 150px; object-fit: cover;">
                    
                    <h5 class="card-title">{{ auth()->user()->name }}</h5>
                    <p class="text-muted">Thành viên từ: {{ auth()->user()->created_at->format('d/m/Y') }}</p>

                    <!-- Hiển thị thông tin chi tiết nếu có -->
                    @if(auth()->user()->bio || auth()->user()->phone || auth()->user()->birthday)
                        <hr>
                        <div class="text-start profile-info">
                            @if(auth()->user()->bio)
                                <div class="mb-3">
                                    <h6 class="text-muted mb-1">Giới thiệu:</h6>
                                    <p class="mb-0">{{ auth()->user()->bio }}</p>
                                </div>
                            @endif

                            @if(auth()->user()->phone)
                                <div class="mb-2">
                                    <h6 class="text-muted mb-1">Số điện thoại:</h6>
                                    <p class="mb-0">{{ auth()->user()->phone }}</p>
                                </div>
                            @endif

                            @if(auth()->user()->birthday)
                                <div class="mb-2">
                                    <h6 class="text-muted mb-1">Ngày sinh:</h6>
                                    <p class="mb-0">{{ auth()->user()->birthday->format('d/m/Y') }}</p>
                                </div>
                            @endif

                            <div class="mb-2">
                                <h6 class="text-muted mb-1">Email:</h6>
                                <p class="mb-0">{{ auth()->user()->email }}</p>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info mt-3 mb-0">
                            <i class="fas fa-info-circle"></i> Vui lòng cập nhật thông tin cá nhân của bạn
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Form chỉnh sửa thông tin -->
        <div class="col-md-8">
            <!-- Thông báo -->
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Form cập nhật thông tin cá nhân -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Thông tin cá nhân</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Upload avatar -->
                        <div class="mb-3">
                            <label for="avatar" class="form-label">Avatar</label>
                            <input type="file" class="form-control @error('avatar') is-invalid @enderror" 
                                   id="avatar" name="avatar">
                            @error('avatar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Họ tên -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Họ tên</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $user->name) }}">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $user->email) }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Số điện thoại -->
                        <div class="mb-3">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Ngày sinh -->
                        <div class="mb-3">
                            <label for="birthday" class="form-label">Ngày sinh</label>
                            <input type="date" class="form-control @error('birthday') is-invalid @enderror" 
                                   id="birthday" name="birthday" 
                                   value="{{ old('birthday', $user->birthday?->format('Y-m-d')) }}">
                            @error('birthday')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Giới thiệu bản thân -->
                        <div class="mb-3">
                            <label for="bio" class="form-label">Giới thiệu bản thân</label>
                            <textarea class="form-control @error('bio') is-invalid @enderror" 
                                      id="bio" name="bio" rows="3">{{ old('bio', $user->bio) }}</textarea>
                            @error('bio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Cập nhật thông tin</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    .profile-info h6 {
        font-size: 0.9rem;
        color: #6c757d;
    }
    .profile-info p {
        font-size: 1rem;
    }
</style>
@endpush 