@extends('layouts.app')

@section('title', 'Tìm kiếm người dùng')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title mb-4">Tìm kiếm người dùng</h4>
                    <form action="{{ route('users.search') }}" method="GET" class="mb-4">
                        <div class="input-group">
                            <input type="text" name="query" class="form-control form-control-lg" 
                                   placeholder="Nhập tên hoặc email người dùng..." 
                                   value="{{ request('query') }}"
                                   autofocus>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Tìm kiếm
                            </button>
                        </div>
                    </form>

                    @if(request('query'))
                        <div class="search-results">
                            @if($users->isEmpty())
                                <div class="text-center py-5">
                                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Không tìm thấy kết quả nào</h5>
                                    <p class="text-muted">Hãy thử tìm kiếm với từ khóa khác</p>
                                </div>
                            @else
                                <div class="list-group">
                                    @foreach($users as $user)
                                        <div class="list-group-item list-group-item-action">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" 
                                                     class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                                <div class="flex-grow-1">
                                                    <h5 class="mb-1">{{ $user->name }}</h5>
                                                    <p class="mb-1 text-muted">{{ $user->email }}</p>
                                                    <small class="text-muted">
                                                        Tham gia: {{ $user->created_at->format('d/m/Y') }}
                                                    </small>
                                                </div>
                                                <div class="ms-3">
                                                    @if($user->id !== auth()->id())
                                                        @if(auth()->user()->isFriendWith($user))
                                                            <span class="badge bg-success">Bạn bè</span>
                                                        @elseif(auth()->user()->hasSentFriendRequestTo($user))
                                                            <span class="badge bg-warning">Đã gửi lời mời</span>
                                                        @elseif(auth()->user()->hasReceivedFriendRequestFrom($user))
                                                            <div class="btn-group">
                                                                <form action="{{ route('users.accept-friend-request', $user) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-sm btn-success">
                                                                        <i class="fas fa-check"></i> Chấp nhận
                                                                    </button>
                                                                </form>
                                                                <form action="{{ route('users.reject-friend-request', $user) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                                        <i class="fas fa-times"></i> Từ chối
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        @else
                                                            <form action="{{ route('users.send-friend-request', $user) }}" method="POST">
                                                                @csrf
                                                                <button type="submit" class="btn btn-primary btn-sm">
                                                                    <i class="fas fa-user-plus"></i> Kết bạn
                                                                </button>
                                                            </form>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-4">
                                    {{ $users->appends(['query' => request('query')])->links() }}
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Nhập từ khóa để tìm kiếm</h5>
                            <p class="text-muted">Bạn có thể tìm kiếm theo tên hoặc email</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.search-results {
    margin-top: 1rem;
}

.list-group-item {
    border: none;
    border-bottom: 1px solid #eee;
    padding: 1rem;
    transition: all 0.2s ease;
}

.list-group-item:last-child {
    border-bottom: none;
}

.list-group-item:hover {
    background-color: #f8f9fa;
    transform: translateX(5px);
}

.list-group-item img {
    border: 2px solid #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.btn-group {
    display: flex;
    gap: 0.5rem;
}

.btn-group .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.badge {
    padding: 0.5rem 0.75rem;
    font-weight: 500;
}

.pagination {
    margin-bottom: 0;
}

.page-link {
    color: #007bff;
    border: none;
    padding: 0.5rem 0.75rem;
    margin: 0 0.25rem;
    border-radius: 4px;
}

.page-link:hover {
    background-color: #f8f9fa;
    color: #0056b3;
}

.page-item.active .page-link {
    background-color: #007bff;
    border-color: #007bff;
}
</style>
@endsection 