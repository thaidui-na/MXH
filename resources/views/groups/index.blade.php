@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Danh sách nhóm</h5>
                    <a href="{{ route('groups.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tạo nhóm mới
                    </a>
                </div>

                <div class="card-body">
                    {{-- Form tìm kiếm nhóm --}}
                    <form method="GET" action="{{ route('groups.index') }}" class="mb-4">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control" placeholder="Tìm kiếm nhóm..." value="{{ request('q') }}">
                            <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i> Tìm kiếm</button>
                        </div>
                    </form>
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if($groups->count() > 0)
                        <div class="row">
                            @foreach($groups as $group)
                                <div class="col-md-4 mb-4">
                                    <div class="card h-100">
                                        <div class="position-relative">
                                            <img src="{{ $group->cover_image ? asset('storage/' . $group->cover_image) : asset('images/default-cover.jpg') }}"
                                                 onerror="this.onerror=null;this.src='{{ asset('images/default-cover.jpg') }}';"
                                                 class="card-img-top" alt="Cover" style="height: 150px; object-fit: cover;">
                                            <img src="{{ $group->avatar ? asset('storage/' . $group->avatar) : asset('images/default-avatar.jpg') }}"
                                                 onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.jpg') }}';"
                                                 class="rounded-circle position-absolute"
                                                 style="width: 60px; height: 60px; bottom: -30px; left: 20px; border: 3px solid white;">
                                        </div>
                                        <div class="card-body pt-4">
                                            <h5 class="card-title">{{ $group->name }}</h5>
                                            <p class="card-text text-muted small">
                                                {{ Str::limit($group->description, 100) }}
                                            </p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="text-muted small">
                                                    <i class="fas fa-users"></i> {{ $group->members_count }} thành viên
                                                </span>
                                                <span class="badge {{ $group->is_private ? 'bg-secondary' : 'bg-success' }}">
                                                    {{ $group->is_private ? 'Riêng tư' : 'Công khai' }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-transparent">
                                            <div class="d-grid gap-2">
                                                <a href="{{ route('groups.show', $group) }}" class="btn btn-outline-primary mb-2">
                                                    Xem chi tiết
                                                </a>
                                                @if(!$group->members->contains('user_id', auth()->id()))
                                                    <form method="POST" action="{{ route('groups.join', $group->id) }}">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success">Tham gia nhóm</button>
                                                    </form>
                                                @else
                                                    <button class="btn btn-secondary" disabled>Đã tham gia</button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="d-flex justify-content-center mt-4">
                            {{ $groups->appends(['q' => request('q')])->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5>Không tìm thấy nhóm nào phù hợp</h5>
                            <p class="text-muted">Hãy thử từ khóa khác hoặc tạo nhóm mới!</p>
                            <a href="{{ route('groups.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Tạo nhóm mới
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 