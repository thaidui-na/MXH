@extends('layouts.app')

@section('title', $post->title)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success mb-3">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    <h3 class="card-title">{{ $post->title }}</h3>
                    <div class="d-flex justify-content-between my-3">
                        <p class="card-text text-muted">
                            <i class="fas fa-user"></i> {{ $post->user->name }} <br>
                            <i class="fas fa-calendar"></i> {{ $post->created_at->format('d/m/Y H:i') }}
                        </p>
                        @if($post->user_id !== auth()->id())
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#reportModal">
                                <i class="fas fa-flag"></i> Báo cáo
                            </button>
                        @endif
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
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reportModalLabel">Báo cáo bài viết</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('posts.report', $post) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Lý do báo cáo:</label>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="reason" id="reason1" value="Nội dung không phù hợp">
                            <label class="form-check-label" for="reason1">
                                Nội dung không phù hợp
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="reason" id="reason2" value="Spam">
                            <label class="form-check-label" for="reason2">
                                Spam
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="reason" id="reason3" value="Vi phạm bản quyền">
                            <label class="form-check-label" for="reason3">
                                Vi phạm bản quyền
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="reason" id="reason4" value="other">
                            <label class="form-check-label" for="reason4">
                                Khác
                            </label>
                        </div>
                        <div class="mt-3" id="otherReasonDiv" style="display: none;">
                            <textarea class="form-control" name="other_reason" rows="3" placeholder="Nhập lý do khác"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-danger">Gửi báo cáo</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const otherRadio = document.getElementById('reason4');
        const otherReasonDiv = document.getElementById('otherReasonDiv');
        
        document.querySelectorAll('input[name="reason"]').forEach(radio => {
            radio.addEventListener('change', function() {
                otherReasonDiv.style.display = otherRadio.checked ? 'block' : 'none';
            });
        });
    });
</script>
@endpush

@endsection 
