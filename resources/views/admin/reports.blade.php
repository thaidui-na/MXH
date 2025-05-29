@extends('admin.layout')

@section('content')
<h2 class="page-title">Danh sách báo cáo bài viết</h2>

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
        <thead>
            <tr>
                <th>ID</th>
                <th>Bài viết bị báo cáo</th>
                <th>Người báo cáo</th>
                <th>Lý do</th>
                <th>Thời gian</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reports as $report)
                <tr>
                    <td>{{ $report->id }}</td>
                    <td>
                        @if($report->post)
                            <a href="{{ route('posts.show', $report->post->id) }}" target="_blank">
                                {{ Str::limit($report->post->title, 50) }}
                            </a>
                        @else
                            <span class="text-muted">[Bài viết đã bị xóa]</span>
                        @endif
                    </td>
                    <td>{{ $report->user->name ?? '<span class="text-muted">[Người dùng không tồn tại]</span>' }}</td>
                    <td>{{ Str::limit($report->reason, 100) }}</td>
                    <td>{{ $report->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        @if($report->post)
                            <form action="{{ route('admin.posts.delete', $report->post->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài viết này không?');" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-warning btn-sm"><i class="bi bi-file-earmark-minus-fill"></i> Xóa bài viết</button>
                            </form>
                        @endif
                        <form action="{{ route('admin.reports.delete', $report->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa báo cáo này không?');" class="d-inline ms-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash-fill"></i> Xóa báo cáo</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">Không có báo cáo bài viết nào.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $reports->links() }}
@endsection

@push('styles')
<style>
    .table th,
    .table td {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 200px;
    }
    .table-responsive {
        overflow-x: auto;
    }
</style>
@endpush
