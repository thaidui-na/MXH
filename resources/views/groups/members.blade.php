@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Quản lý thành viên - {{ $group->name }}</h5>
                    <a href="{{ route('groups.show', $group) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Thành viên</th>
                                    <th>Vai trò</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày tham gia</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($members as $member)
                                    <tr>
                                        <td class="align-middle">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $member->user->avatar ? Storage::url($member->user->avatar) : asset('images/default-avatar.jpg') }}" 
                                                     class="rounded-circle me-2" 
                                                     style="width: 40px; height: 40px; object-fit: cover;">
                                                <div>
                                                    <div>{{ $member->user->name }}</div>
                                                    <small class="text-muted">{{ $member->user->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            @if($group->hasAdmin(auth()->id()))
                                                <form method="POST" action="{{ route('groups.update-member', ['group' => $group->id, 'member' => $member->id]) }}" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <select name="role" class="form-select form-select-sm" onchange="this.form.submit()" {{ $member->user_id == $group->created_by ? 'disabled' : '' }}>
                                                        <option value="member" {{ $member->role == 'member' ? 'selected' : '' }}>Thành viên</option>
                                                        <option value="moderator" {{ $member->role == 'moderator' ? 'selected' : '' }}>Điều hành viên</option>
                                                        <option value="admin" {{ $member->role == 'admin' ? 'selected' : '' }}>Quản trị viên</option>
                                                    </select>
                                                </form>
                                            @else
                                                <span class="badge bg-{{ $member->role == 'admin' ? 'danger' : ($member->role == 'moderator' ? 'success' : 'secondary') }}">
                                                    {{ $member->role == 'admin' ? 'Quản trị viên' : ($member->role == 'moderator' ? 'Điều hành viên' : 'Thành viên') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            @if($group->hasAdmin(auth()->id()))
                                                <form method="POST" action="{{ route('groups.update-member', ['group' => $group->id, 'member' => $member->id]) }}" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="form-check form-switch">
                                                        <input type="checkbox" 
                                                               class="form-check-input" 
                                                               name="is_approved" 
                                                               value="1" 
                                                               {{ $member->is_approved ? 'checked' : '' }}
                                                               onchange="this.form.submit()"
                                                               {{ $member->user_id == $group->created_by ? 'disabled' : '' }}>
                                                    </div>
                                                </form>
                                            @else
                                                <span class="badge bg-{{ $member->is_approved ? 'success' : 'warning' }}">
                                                    {{ $member->is_approved ? 'Đã duyệt' : 'Chờ duyệt' }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            {{ $member->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="align-middle">
                                            @if($group->hasAdmin(auth()->id()) && $member->user_id != $group->created_by && $member->user_id != auth()->id())
                                                <form method="POST" action="{{ route('groups.remove-member', ['group' => $group->id, 'member' => $member->id]) }}" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa thành viên này?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $members->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 