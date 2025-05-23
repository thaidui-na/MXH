@extends('admin.layout')

@section('content')
<h2>Quản lý bài viết</h2>
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Tiêu đề</th>
            <th>Tác giả</th>
            <th>Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @foreach($posts as $post)
        <tr>
            <td>{{ $post->id }}</td>
            <td>{{ $post->title }}</td>
            <td>{{ $post->user->name }}</td>
            <td>
                <a href="{{ route('admin.posts.edit', $post->id) }}" class="btn btn-primary btn-sm">Sửa</a>
                <form action="{{ route('admin.posts.delete', $post->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa?')">Xóa</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
{{ $posts->links('pagination::bootstrap-5') }}
@endsection
