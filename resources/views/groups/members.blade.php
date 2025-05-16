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

                    @if($group->hasAdmin(auth()->id()))
                        <div class="mb-4 p-3 bg-light rounded">
                            <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-user-plus"></i> Thêm thành viên mới</h6>
                            <form method="POST" action="{{ route('groups.add-members', $group) }}" id="addMembersForm">
                                @csrf
                                <div class="mb-3">
                                    <label for="user_ids" class="form-label">Chọn thành viên:</label>
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control" id="searchInput" placeholder="Tìm kiếm thành viên...">
                                        <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <select name="user_ids[]" id="user_ids" class="form-select" multiple size="8" style="min-height: 200px;">
                                        @foreach(\App\Models\User::whereNotIn('id', $group->members->pluck('user_id'))->orderBy('name')->get() as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle"></i> Giữ Ctrl để chọn nhiều thành viên cùng lúc
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus-circle"></i> Thêm thành viên
                                </button>
                            </form>
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
                                            @if($group->hasAdmin(auth()->id()) && $member->user_id != $group->created_by)
                                                <form method="POST" action="{{ route('groups.remove-member', ['group' => $group->id, 'member' => $member->id]) }}" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('Bạn có chắc chắn muốn xóa thành viên này?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-user-minus"></i>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const userSelect = document.getElementById('user_ids');
    const options = Array.from(userSelect.options);

    searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const filteredOptions = options.filter(option => 
            option.text.toLowerCase().includes(searchTerm)
        );

        // Lưu lại các option đã chọn
        const selectedValues = Array.from(userSelect.selectedOptions).map(opt => opt.value);

        // Xóa tất cả options hiện tại
        userSelect.innerHTML = '';

        // Thêm lại các options phù hợp với tìm kiếm
        filteredOptions.forEach(option => {
            userSelect.add(option);
            // Khôi phục lại trạng thái đã chọn
            if (selectedValues.includes(option.value)) {
                option.selected = true;
            }
        });
    });

    // Form validation
    document.getElementById('addMembersForm').addEventListener('submit', function(e) {
        if (userSelect.selectedOptions.length === 0) {
            e.preventDefault();
            alert('Vui lòng chọn ít nhất một thành viên để thêm vào nhóm');
        }
    });
});

function clearSearch() {
    document.getElementById('searchInput').value = '';
    document.getElementById('searchInput').dispatchEvent(new Event('input'));
}
</script>
@endpush

@push('styles')
<style>
#user_ids option {
    padding: 8px;
    margin-bottom: 2px;
    border-radius: 4px;
}
#user_ids option:hover {
    background-color: #f8f9fa;
}
#user_ids option:checked {
    background-color: #0d6efd;
    color: white;
}
</style>
@endpush 