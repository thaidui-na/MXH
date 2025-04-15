@extends('admin.layout')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5>Tổng số người dùng: {{ $users }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5>Tổng số bài viết: {{ $posts }}</h5>
            </div>
        </div>
    </div>
</div>
@endsection
