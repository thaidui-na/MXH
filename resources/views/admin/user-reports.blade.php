@extends('admin.layout')

@section('content')
<h2 class="page-title">Danh sách báo cáo người dùng</h2>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

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
                <th>Người bị báo cáo</th>
                <th>Người báo cáo</th>
                <th>Lý do</th>
                <th>Trạng thái</th>
                <th>Ghi chú Admin</th>
                <th>Thời gian</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse($userReports as $report)
                <tr>
                    <td>{{ $report->id }}</td>
                    <td>
                        {{ $report->reportedUser->name ?? '<span class="text-muted">[Người dùng không tồn tại]</span>' }}
                    </td>
                    <td>
                         {{ $report->reporter->name ?? '<span class="text-muted">[Người dùng không tồn tại]</span>' }}
                    </td>
                    <td>{{ $report->reason }}</td>
                    <td>
                        <span class="badge bg-{{ $report->is_resolved ? 'success' : 'warning' }}">
                            {{ $report->is_resolved ? 'Đã xử lý' : 'Chưa xử lý' }}
                        </span>
                    </td>
                    <td>{{ $report->admin_note ?? '-' }}</td>
                    <td>{{ $report->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                         {{-- Thêm các nút hành động tại đây (ví dụ: đánh dấu đã xử lý, xóa báo cáo) --}}
                        <button class="btn btn-info btn-sm me-1" title="Xem chi tiết"><i class="bi bi-eye-fill"></i></button>
                        @if(!$report->is_resolved)
                            <form action="{{ route('admin.user-reports.mark-resolved', $report->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Đánh dấu báo cáo này là đã xử lý?');">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-success btn-sm me-1" title="Đánh dấu đã xử lý"><i class="bi bi-check-circle-fill"></i></button>
                            </form>
                        @endif
                         <form action="{{ route('admin.user-reports.delete', $report->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa báo cáo này không?');">
                             @csrf
                             @method('DELETE')
                             <button type="submit" class="btn btn-danger btn-sm" title="Xóa báo cáo"><i class="bi bi-trash-fill"></i></button>
                         </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted">Không có báo cáo người dùng nào.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $userReports->links() }}

@endsection

@push('styles')
<style>
    .table th,
    .table td {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 150px; /* Điều chỉnh cho phù hợp */
    }
    .table-responsive {
        overflow-x: auto;
    }
</style>
@endpush 