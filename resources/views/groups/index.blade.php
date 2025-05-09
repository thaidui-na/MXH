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
                                            <img src="{{ $group->cover_image ? Storage::url($group->cover_image) : asset('images/default-cover.jpg') }}" 
                                                class="card-img-top" alt="Cover" style="height: 150px; object-fit: cover;">
                                            <img src="{{ $group->avatar ? Storage::url($group->avatar) : asset('images/default-avatar.jpg') }}" 
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
                                            <div class="d-grid">
                                                <a href="{{ route('groups.show', $group) }}" class="btn btn-outline-primary">
                                                    Xem chi tiết
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            {{ $groups->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5>Chưa có nhóm nào</h5>
                            <p class="text-muted">Hãy tạo nhóm đầu tiên!</p>
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