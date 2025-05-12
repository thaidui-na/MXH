@extends('layouts.app')

@section('title', 'Tìm kiếm người dùng')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Tìm kiếm người dùng</h4>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('users.search') }}" class="mb-4">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control" placeholder="Nhập tên hoặc email người dùng..." value="{{ $query }}">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i> Tìm kiếm
                            </button>
                        </div>
                    </form>

                    @if($query)
                        <h5 class="mb-3">Kết quả tìm kiếm cho "{{ $query }}"</h5>
                        
                        @if($users->count() > 0)
                            <div class="list-group">
                                @foreach($users as $user)
                                    <div class="list-group-item">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $user->avatar ? asset('images/' . $user->avatar) : asset('images/default-avatar.jpg') }}"
                                                 onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.jpg') }}';"
                                                 class="rounded-circle me-3"
                                                 style="width: 50px; height: 50px; object-fit: cover;"
                                                 alt="{{ $user->name }}'s avatar">
                                            <div>
                                                <h5 class="mb-1">{{ $user->name }}</h5>
                                                <p class="text-muted mb-0">{{ $user->email }}</p>
                                            </div>
                                            <div class="ms-auto">
                                                <a href="{{ route('posts.user_posts', $user) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> Xem bài viết
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="d-flex justify-content-center mt-4">
                                {{ $users->appends(['q' => $query])->links() }}
                            </div>
                        @else
                            <div class="alert alert-info">
                                Không tìm thấy người dùng nào phù hợp với từ khóa "{{ $query }}"
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 