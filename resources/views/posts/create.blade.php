@extends('layouts.app')

@section('title', 'Tạo bài viết mới')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Tạo bài viết mới</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('posts.store') }}" method="POST">
                        @csrf
                        
                        <!-- Tiêu đề bài viết -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Tiêu đề</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Nội dung bài viết -->
                        <div class="mb-3">
                            <label for="content" class="form-label">Nội dung</label>
                            <textarea class="form-control @error('content') is-invalid @enderror" 
                                      id="content" name="content" rows="10" required>{{ old('content') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Trạng thái công khai -->
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_public" 
                                   name="is_public" value="1" {{ old('is_public', '1') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_public">Công khai bài viết</label>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('posts.my_posts') }}" class="btn btn-secondary me-md-2">Hủy</a>
                            <button type="submit" class="btn btn-primary">Đăng bài</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 