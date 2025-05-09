
@extends('layouts.app')

@section('title', $post->title)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success mb-3">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    <h3 class="card-title">{{ $post->title }}</h3>
                    <div class="d-flex justify-content-between my-3">
                        <p class="card-text text-muted">
                            <i class="fas fa-user"></i> {{ $post->user->name }} <br>
                            <i class="fas fa-calendar"></i> {{ $post->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    
                    <hr>
                    
                    <div class="card-text mb-4">
                        {!! nl2br(e($post->content)) !!}
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ url()->previous() }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                        
                        @if($post->user_id === auth()->id())
                            <div>
                                <a href="{{ route('posts.edit', $post) }}" class="btn btn-warning me-2">
                                    <i class="fas fa-edit"></i> Sửa
                                </a>
                                <form action="{{ route('posts.destroy', $post) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài viết này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash"></i> Xóa
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
