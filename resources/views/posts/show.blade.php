@extends('layouts.app')

@section('title', $post->title)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                @if(auth()->check() && $post->user_id !== auth()->id())
                    <button type="button" class="btn btn-outline-danger btn-sm"
                            style="position: absolute; top: 10px; right: 10px; z-index: 10;"
                            data-bs-toggle="modal" data-bs-target="#reportModal"
                            title="Báo cáo bài viết">
                        <i class="fas fa-flag"></i> Báo cáo
                    </button>
                @endif
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success mb-3" id="success-alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    <h3 class="card-title">{{ $post->title }}</h3>
                    <div class="d-flex justify-content-between my-3">
                        <p class="card-text text-muted">
                            <i class="fas fa-user"></i> {{ $post->user->name }} <br>
                            <i class="fas fa-calendar"></i> {{ $post->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    
                    <hr>
                    
                    <div class="card-text mb-4">
                        {!! nl2br(e($post->content)) !!}
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ url()->previous() }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                        <a href="{{ route('comments.index', $post->id) }}" class="btn btn-secondary">
                            <i class="fas fa-comments"></i> Bình luận
                        </a>
                        
                        @if($post->user_id === auth()->id())
                            <div>
                                <a href="{{ route('posts.edit', $post) }}" class="btn btn-warning me-2">
                                    <i class="fas fa-edit"></i> Sửa
                                </a>
                                <form action="{{ route('posts.destroy', $post) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài viết này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash"></i> Xóa
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Báo cáo -->
<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form action="{{ route('reports.store') }}" method="POST">
      @csrf
      <input type="hidden" name="post_id" value="{{ $post->id }}">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="reportModalLabel">Báo cáo bài viết</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
        </div>
        <div class="modal-body">
          <label class="form-label">Chọn lý do báo cáo:</label>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="reason" id="reason1" value="Nội dung không phù hợp" required>
            <label class="form-check-label" for="reason1">Nội dung không phù hợp</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="reason" id="reason2" value="Spam/quảng cáo">
            <label class="form-check-label" for="reason2">Spam/quảng cáo</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="reason" id="reason3" value="Thông tin sai sự thật">
            <label class="form-check-label" for="reason3">Thông tin sai sự thật</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="reason" id="reason4" value="Ngôn từ thù ghét">
            <label class="form-check-label" for="reason4">Ngôn từ thù ghét</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="reason" id="reason5" value="Khác">
            <label class="form-check-label" for="reason5">Khác</label>
          </div>
          <div class="mt-2">
            <textarea class="form-control" name="custom_reason" id="customReason" rows="2" placeholder="Nhập lý do khác..." style="display:none"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
          <button type="submit" class="btn btn-danger">Gửi báo cáo</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Ẩn alert sau 2 giây
    const alert = document.getElementById('success-alert');
    if(alert) {
        setTimeout(() => {
            alert.style.transition = "opacity 0.5s";
            alert.style.opacity = 0;
            setTimeout(() => alert.remove(), 500);
        }, 2000);
    }

    // Đoạn script cho modal báo cáo (nếu có)
    const radios = document.querySelectorAll('input[name="reason"]');
    const customReason = document.getElementById('customReason');
    if(radios && customReason) {
        radios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'Khác') {
                    customReason.style.display = 'block';
                    customReason.required = true;
                } else {
                    customReason.style.display = 'none';
                    customReason.required = false;
                }
            });
        });
    }
});
</script>
@endpush 