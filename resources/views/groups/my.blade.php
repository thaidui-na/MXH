@extends('layouts.app')

@section('title', 'Nhóm của bạn')

@section('content')
<div class="container py-4">
    <h3>Nhóm của bạn</h3>

    <div class="row">
        @forelse($groups as $group)
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    @if($group->image)
                        <img src="{{ asset('storage/' . $group->image) }}" class="card-img-top" style="height: 180px; object-fit: cover;">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $group->name }}</h5>
                        <p class="card-text">{{ Str::limit($group->description, 100) }}</p>
                        <p class="text-muted small mb-2">
                            Tạo bởi: {{ $group->user->name }} <br>
                            Ngày tạo: {{ $group->created_at->format('d/m/Y') }}
                        </p>
                        <a href="{{ route('groups.show', $group) }}" class="btn btn-sm btn-primary">Xem nhóm</a>
                    </div>
                </div>
            </div>
        @empty
            <p>Bạn chưa tạo nhóm nào.</p>
        @endforelse
    </div>

    <div class="mt-3 d-flex justify-content-center">
        {{ $groups->links() }}
    </div>
</div>
@endsection
