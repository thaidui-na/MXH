@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>My Groups</h2>
        <a href="{{ route('groups.create') }}" class="btn btn-primary">Tạo Nhóm Mới</a>
    </div>

    <div class="row">
        @foreach($groups as $group)
            <div class="col-md-4">
                <div class="card shadow mb-4">
                    @if($group->image)
                        <img src="{{ asset($group->image) }}" class="card-img-top" alt="Group Image">
                    @else
                        <img src="https://via.placeholder.com/300x150" class="card-img-top" alt="Default Image">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $group->name }}</h5>
                        <p class="card-text">{{ Str::limit($group->description, 100) }}</p>
                        <a href="{{ route('groups.show', $group) }}" class="btn btn-outline-primary">Xem Nhóm</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
