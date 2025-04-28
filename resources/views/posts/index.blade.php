@extends('layouts.app')

@section('title', 'Bảng tin')

{{-- Thêm CSS tùy chỉnh --}}
@push('styles')
<style>
/* Style cho card bài viết */
.post-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    margin-bottom: 1.5rem;
    background: #fff;
    overflow: hidden;
}

.post-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.12);
}

/* Style cho tiêu đề trang */
.page-title {
    font-size: 2rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #eee;
}

/* Style cho tiêu đề bài viết */
.post-title {
    color: #2c3e50;
    font-weight: 600;
    font-size: 1.25rem;
    margin-bottom: 0.5rem;
    transition: color 0.2s ease;
}

.post-title:hover {
    color: #3498db;
    text-decoration: none;
}

/* Style cho meta info */
.post-meta {
    font-size: 0.875rem;
    color: #6c757d;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.post-meta i {
    margin-right: 0.25rem;
}

/* Style cho nội dung bài viết */
.post-excerpt {
    color: #505965;
    font-size: 0.95rem;
    line-height: 1.6;
    margin-bottom: 1rem;
}

/* Style cho nút xem chi tiết */
.btn-view-post {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.875rem;
    transition: all 0.3s ease;
    background-color: #3498db;
    border-color: #3498db;
    color: white;
}

.btn-view-post:hover {
    background-color: #2980b9;
    border-color: #2980b9;
    transform: translateX(5px);
}

.btn-view-post i {
    margin-right: 0.5rem;
    transition: transform 0.3s ease;
}

.btn-view-post:hover i {
    transform: translateX(3px);
}

/* Style cho phân trang */
.pagination {
    margin-top: 2rem;
}

.page-link {
    border-radius: 5px;
    margin: 0 3px;
    color: #3498db;
    border: 1px solid #e9ecef;
}

.page-item.active .page-link {
    background-color: #3498db;
    border-color: #3498db;
}

/* Style cho alert */
.custom-alert {
    border-radius: 10px;
    border-left: 4px solid #3498db;
    background-color: #f8f9fa;
    padding: 1rem;
}

.custom-alert a {
    color: #3498db;
    font-weight: 500;
    text-decoration: none;
}

.custom-alert a:hover {
    text-decoration: underline;
}

/* Thêm animation cho cards */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.post-card {
    animation: fadeInUp 0.5s ease forwards;
}

/* Đảm bảo nút like và nút xem chi tiết nằm cùng một hàng */
.d-flex {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* Điều chỉnh nút like */
.btn-like {
    background: none;
    border: none;
    padding: 0;
    cursor: pointer;
    color: #1877f2;
    /* Màu sắc của biểu tượng like */
    font-size: 1.25rem;
    /* Kích thước font phù hợp */
    display: flex;
    align-items: center;
    gap: 5px;
    /* Khoảng cách giữa biểu tượng và số like */
}

/* Hover effect khi di chuột vào nút like */
.btn-like:hover {
    color: #1562a1;
    /* Màu sắc khi hover */
}

/* Style cho biểu tượng like */
.btn-like i {
    margin-right: 5px;
}

/* Chỉnh màu số like */
.btn-like span {
    font-weight: bold;
}

/* Style cho nút xem chi tiết */
.btn-view-post {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.875rem;
    transition: all 0.3s ease;
    background-color: #3498db;
    border-color: #3498db;
    color: white;
}

.btn-view-post:hover {
    background-color: #2980b9;
    border-color: #2980b9;
    transform: translateX(5px);
}

.btn-view-post i {
    margin-right: 0.5rem;
    transition: transform 0.3s ease;
}

.btn-view-post:hover i {
    transform: translateX(3px);
}
</style>
@endpush

@section('content')

<div class="container py-4">
    {{-- Thêm class page-title --}}
    
    <h4 class="page-title mb-4">Bảng tin</h4>
    <div class="text-end " style="margin-bottom: 15px;">
    <a href="{{ route('groups.index') }}" class="btn btn-primary">
        <i class="bi bi-people-fill"></i> Nhóm của bạn
    </a>
</div>

</a>
    @if($posts->count() > 0)
    <div class="row">
        @foreach($posts as $index => $post)
        {{-- Thêm animation-delay cho từng card --}}
        <div class="col-md-12" style="animation-delay: {{ $index * 0.1 }}s;">
            <div class="card post-card">
                <div class="card-body">
                    {{-- Thêm link cho tiêu đề --}}
                    <a href="{{ route('posts.show', $post) }}" class="text-decoration-none">
                        <h5 class="post-title">{{ $post->title }}</h5>
                    </a>

                    {{-- Cải thiện hiển thị meta info --}}
                    <div class="post-meta">
                        <span>
                            <i class="fas fa-user-circle"></i>
                            {{ $post->user->name }}
                        </span>
                        <span>
                            <i class="fas fa-clock"></i>
                            {{ $post->created_at->format('d/m/Y H:i') }}
                        </span>
                    </div>

                    {{-- Thêm class cho phần nội dung --}}
                    <p class="post-excerpt">{{ Str::limit($post->content, 200) }}</p>

                    {{-- Cải thiện nút xem chi tiết --}}
                    <div class="d-flex justify-content-between align-items-center">
                        {{-- Nút Like --}}
                        <form action="{{ route('posts.like', $post) }}" method="POST" style="margin: 0;">
                            @csrf
                            <button type="submit" class="btn-like">
                                <i class="fas fa-thumbs-up"></i>
                                {{ $post->likes->count() }}
                            </button>
                        </form>

                        {{-- Nút Xem chi tiết --}}
                        <a href="{{ route('posts.show', $post) }}" class="btn btn-view-post">
                            <i class="fas fa-eye"></i> Xem chi tiết
                        </a>
                    </div>

                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Phân trang đã được style tự động qua CSS ở trên --}}
    <div class="d-flex justify-content-center mt-4">
        {{ $posts->links() }}
    </div>
    @else
    {{-- Thêm class custom-alert --}}
    <div class="alert custom-alert">
        Chưa có bài viết nào được đăng. <a href="{{ route('posts.create') }}">Hãy là người đầu tiên đăng bài</a>
    </div>
    @endif
</div>

{{-- Thêm JavaScript cho hiệu ứng --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Thêm hiệu ứng hover cho các card
    const cards = document.querySelectorAll('.post-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});
</script>
@endpush
@endsection