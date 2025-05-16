@if(auth()->check() && auth()->id() !== $user->id)
    <div class="dropdown">
        <button class="btn btn-light btn-sm rounded-circle shadow-sm d-flex align-items-center justify-content-center" 
                type="button" 
                data-bs-toggle="dropdown" 
                aria-expanded="false" 
                style="width: 32px; height: 32px; padding: 0;">
            <i class="fas fa-ellipsis-v" style="font-size: 16px;"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
            <li>
                <button class="dropdown-item text-danger report-user" 
                        data-user-id="{{ $user->id }}" 
                        data-user-name="{{ $user->name }}">
                    <i class="fas fa-flag me-2"></i>Báo cáo vi phạm
                </button>
            </li>
            <li>
                <button class="dropdown-item {{ auth()->user()->hasBlocked($user->id) ? 'text-success unblock-user' : 'text-danger block-user' }}"
                        data-user-id="{{ $user->id }}"
                        data-user-name="{{ $user->name }}">
                    <i class="fas {{ auth()->user()->hasBlocked($user->id) ? 'fa-unlock me-2' : 'fa-ban me-2' }}"></i>
                    {{ auth()->user()->hasBlocked($user->id) ? 'Bỏ chặn người dùng' : 'Chặn người dùng' }}
                </button>
            </li>
        </ul>
    </div>

    <!-- Modal Báo cáo vi phạm -->
    <div class="modal fade" id="reportModal-{{ $user->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Báo cáo vi phạm</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Bạn đang báo cáo người dùng <strong>{{ $user->name }}</strong></p>
                    <div class="mb-3">
                        <label for="reportReason-{{ $user->id }}" class="form-label">Lý do báo cáo:</label>
                        <textarea class="form-control" id="reportReason-{{ $user->id }}" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-danger submit-report" data-user-id="{{ $user->id }}">Gửi báo cáo</button>
                </div>
            </div>
        </div>
    </div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý sự kiện báo cáo
    document.querySelectorAll('.report-user').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.userId;
            const modal = new bootstrap.Modal(document.getElementById(`reportModal-${userId}`));
            modal.show();
        });
    });

    // Xử lý sự kiện gửi báo cáo
    document.querySelectorAll('.submit-report').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.userId;
            const reason = document.getElementById(`reportReason-${userId}`).value;
            
            if (!reason.trim()) {
                alert('Vui lòng nhập lý do báo cáo');
                return;
            }

            fetch(`/users/${userId}/report`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ reason: reason })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById(`reportModal-${userId}`)).hide();
                    alert('Đã gửi báo cáo thành công');
                } else {
                    alert(data.error || 'Có lỗi xảy ra');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra');
            });
        });
    });

    // Xử lý sự kiện chặn người dùng
    document.querySelectorAll('.block-user').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.userId;
            const userName = this.dataset.userName;
            
            if (confirm(`Bạn có chắc chắn muốn chặn người dùng ${userName}?`)) {
                fetch(`/users/${userId}/block`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Đã chặn người dùng thành công');
                        location.reload();
                    } else {
                        alert(data.error || 'Có lỗi xảy ra');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra');
                });
            }
        });
    });

    // Xử lý sự kiện bỏ chặn người dùng
    document.querySelectorAll('.unblock-user').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.userId;
            const userName = this.dataset.userName;
            
            if (confirm(`Bạn có chắc chắn muốn bỏ chặn người dùng ${userName}?`)) {
                fetch(`/users/${userId}/unblock`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Đã bỏ chặn người dùng thành công');
                        location.reload();
                    } else {
                        alert(data.error || 'Có lỗi xảy ra');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra');
                });
            }
        });
    });
});
</script>
@endpush 