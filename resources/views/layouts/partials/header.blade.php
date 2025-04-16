<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="{{ url('/') }}">
            {{ config('app.name', 'MXH') }}
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                @auth
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('posts.index') ? 'active text-primary fw-semibold' : '' }}" href="{{ route('posts.index') }}">Bảng tin</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('posts.my_posts') ? 'active text-primary fw-semibold' : '' }}" href="{{ route('posts.my_posts') }}">Bài viết của tôi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link position-relative {{ request()->routeIs('messages.index') ? 'active text-primary fw-semibold' : '' }}" href="{{ route('messages.index') }}">
                            Tin nhắn
                            @if(auth()->check() && auth()->user()->unreadMessages()->count() > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65em; line-height: 1;">
                                    {{ auth()->user()->unreadMessages()->count() }}
                                    <span class="visually-hidden">unread messages</span>
                                </span>
                            @endif
                        </a>
                    </li>
                @endauth
            </ul>

            <ul class="navbar-nav ms-auto">
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Đăng nhập</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">Đăng ký</a>
                    </li>
                @else
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ Auth::user()->avatar_url }}" 
                                 class="rounded-circle me-2" 
                                 style="width: 32px; height: 32px; object-fit: cover;"
                                 alt="{{ Auth::user()->name }}'s avatar">
                            {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="fas fa-user-edit me-2"></i>Hồ sơ</a></li>
                            <li><a class="dropdown-item" href="{{ route('password.change') }}"><i class="fas fa-key me-2"></i>Đổi mật khẩu</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="d-grid px-2">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger w-100"><i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>

{{-- ======= CSS Bổ Sung Cho Header ======= --}}
<style>
    /* Đảm bảo body có padding-top bằng chiều cao header để nội dung không bị che */
    body {
        padding-top: 70px; /* Điều chỉnh giá trị này nếu chiều cao header khác */
    }

    .navbar {
        transition: background-color 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        border-bottom: 1px solid #e9ecef;
    }

    /* Style cho header khi cuộn trang (sẽ được thêm/xóa bằng JS) */
    .navbar-scrolled {
        background-color: rgba(255, 255, 255, 0.95) !important; /* Nền trắng mờ nhẹ */
        backdrop-filter: blur(5px); /* Hiệu ứng blur */
        box-shadow: 0 2px 4px rgba(0,0,0,0.05) !important; /* Bóng đổ rõ hơn chút */
    }

    .nav-link {
        transition: color 0.2s ease-in-out, border-color 0.2s ease-in-out;
    }

    .nav-link.active:not(.dropdown-toggle) {
        border-bottom: 2px solid #0d6efd;
        margin-bottom: -1px; /* Giữ nguyên */
        font-weight: 600; /* Đậm hơn một chút */
    }

    .nav-link:not(.active):not(.dropdown-toggle):hover {
        color: #0d6efd !important; /* Màu xanh khi hover link không active */
    }

    .navbar-brand {
        transition: color 0.2s ease-in-out;
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
    }
    .navbar-brand:hover {
        color: #0b5ed7 !important; /* Đậm hơn khi hover */
    }

    .dropdown-menu {
        margin-top: 0.5rem !important;
        animation: fadeIn 0.2s ease-out; /* Animation nhỏ khi xổ xuống */
    }

    .dropdown-item {
         transition: background-color 0.15s ease-in-out, color 0.15s ease-in-out;
         padding-top: 0.5rem;
         padding-bottom: 0.5rem;
    }
    
    .dropdown-item:hover {
        background-color: #f8f9fa; /* Nền xám nhạt khi hover */
    }


    /* Keyframes cho animation dropdown */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-5px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style> 