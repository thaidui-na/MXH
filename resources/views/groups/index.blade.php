@extends('layouts.app')

@section('title', 'Danh sách nhóm')

@section('content')
<div class="container py-4">
    <h3 class="mb-4">Tất cả nhóm</h3>

    <div class="row">
        @foreach($groups as $group)
        <div class="col-md-4 mb-4">
            <div class="card">
                @if($group->image)
                    <img src="{{ asset('storage/' . $group->image) }}" class="card-img-top" style="height: 180px; object-fit: cover;">
                @endif
                <div class="card-body">
                    <h5 class="card-title">{{ $group->name }}</h5>
                    <p class="card-text text-muted">{{ Str::limit($group->description, 100) }}</p>
                    <a href="{{ route('groups.show', $group) }}" class="btn btn-sm btn-primary">Xem nhóm</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-center mt-3">
        {{ $groups->links() }}
    </div>
</div>
@endsection
