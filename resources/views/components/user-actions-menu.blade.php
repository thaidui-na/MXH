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
                    {{-- Khu vực hiển thị thông báo --}}
                    <div id="reportMessage-{{ $user->id }}"></div>

                    <p>Bạn đang báo cáo người dùng <strong>{{ $user->name }}</strong></p>
                    <div class="mb-3">
                        <label class="form-label">Lý do báo cáo:</label>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="reason-{{ $user->id }}" id="reason1-{{ $user->id }}" value="Nội dung không phù hợp" required>
                            <label class="form-check-label" for="reason1-{{ $user->id }}">Nội dung không phù hợp</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="reason-{{ $user->id }}" id="reason2-{{ $user->id }}" value="Spam" required>
                            <label class="form-check-label" for="reason2-{{ $user->id }}">Spam</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="reason-{{ $user->id }}" id="reason3-{{ $user->id }}" value="Vi phạm bản quyền" required>
                            <label class="form-check-label" for="reason3-{{ $user->id }}">Vi phạm bản quyền</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="reason-{{ $user->id }}" id="reason4-{{ $user->id }}" value="Quấy rối" required>
                            <label class="form-check-label" for="reason4-{{ $user->id }}">Quấy rối</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="reason-{{ $user->id }}" id="reason5-{{ $user->id }}" value="Bạo lực" required>
                            <label class="form-check-label" for="reason5-{{ $user->id }}">Bạo lực</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="reason-{{ $user->id }}" id="reason6-{{ $user->id }}" value="Lừa đảo" required>
                            <label class="form-check-label" for="reason6-{{ $user->id }}">Lừa đảo</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="reason-{{ $user->id }}" id="reason7-{{ $user->id }}" value="Ngôn ngữ thù địch" required>
                            <label class="form-check-label" for="reason7-{{ $user->id }}">Ngôn ngữ thù địch</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="reason-{{ $user->id }}" id="reason8-{{ $user->id }}" value="Thông tin sai lệch" required>
                            <label class="form-check-label" for="reason8-{{ $user->id }}">Thông tin sai lệch</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="reason-{{ $user->id }}" id="reason9-{{ $user->id }}" value="Nội dung khiêu dâm" required>
                            <label class="form-check-label" for="reason9-{{ $user->id }}">Nội dung khiêu dâm</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="reason-{{ $user->id }}" id="reason10-{{ $user->id }}" value="Tự tử hoặc tự làm hại" required>
                            <label class="form-check-label" for="reason10-{{ $user->id }}">Tự tử hoặc tự làm hại</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="reason-{{ $user->id }}" id="reason11-{{ $user->id }}" value="other" required>
                            <label class="form-check-label" for="reason11-{{ $user->id }}">Lý do khác</label>
                        </div>
                        <div class="mt-2 d-none" id="otherReasonContainer-{{ $user->id }}">
                            <textarea class="form-control" name="other_reason" rows="2" placeholder="Vui lòng mô tả lý do báo cáo..."></textarea>
                        </div>
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

            // Hiện/ẩn textarea khi chọn "Lý do khác"
            const otherRadio = document.getElementById(`reason11-${userId}`);
            const otherReasonContainer = document.getElementById(`otherReasonContainer-${userId}`);
            document.querySelectorAll(`input[name='reason-${userId}']`).forEach(radio => {
                radio.addEventListener('change', function() {
                    if (otherRadio.checked) {
                        otherReasonContainer.classList.remove('d-none');
                    } else {
                        otherReasonContainer.classList.add('d-none');
                    }
                });
            });
        });
    });

    // Xử lý sự kiện gửi báo cáo
    document.querySelectorAll('.submit-report').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.userId;
            const modalId = `reportModal-${userId}`;
            const modalElement = document.getElementById(modalId);
            const reportMessageDiv = document.getElementById(`reportMessage-${userId}`);
            const submitButton = this;
            
            // Xóa thông báo cũ
            reportMessageDiv.innerHTML = '';

            const selectedReason = document.querySelector(`input[name='reason-${userId}']:checked`);
            let reason = '';
            let other_reason = '';
            if (selectedReason) {
                if (selectedReason.value === 'other') {
                    other_reason = document.querySelector(`#otherReasonContainer-${userId} textarea`).value.trim();
                    reason = 'other';
                } else {
                    reason = selectedReason.value;
                }
            }
            
            if (!reason || (reason === 'other' && !other_reason)) {
                reportMessageDiv.innerHTML = '<div class="alert alert-danger">Vui lòng chọn và/hoặc nhập lý do báo cáo.</div>';
                return;
            }

            const formData = new URLSearchParams();
            formData.append('reason', reason);
            if (reason === 'other') {
                formData.append('other_reason', other_reason);
            }

            // Hiển thị trạng thái đang xử lý
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang gửi...';

            fetch(`/users/${userId}/report`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    // Content-Type không cần thiết với URLSearchParams
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    reportMessageDiv.innerHTML = '<div class="alert alert-success">' + data.message + '</div>';
                    // Đóng modal sau 2 giây
                    setTimeout(() => {
                        bootstrap.Modal.getInstance(modalElement).hide();
                        // Tùy chọn: Reset form hoặc reload trang nhỏ
                         // location.reload(); // Nếu muốn reload lại toàn bộ trang
                    }, 2000); 
                } else {
                    reportMessageDiv.innerHTML = '<div class="alert alert-danger">' + (data.error || 'Có lỗi xảy ra khi gửi báo cáo.') + '</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                reportMessageDiv.innerHTML = '<div class="alert alert-danger">Đã xảy ra lỗi mạng hoặc máy chủ. Vui lòng thử lại.</div>';
            })
            .finally(() => {
                // Luôn re-enable nút sau khi hoàn thành
                submitButton.disabled = false;
                submitButton.innerHTML = 'Gửi báo cáo';
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