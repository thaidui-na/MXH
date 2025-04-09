<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                @auth
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('posts.index') }}">Bảng tin</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('posts.my_posts') }}">Bài viết của tôi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('messages.index') }}">
                            Tin nhắn
                            @if(auth()->user()->unreadMessages()->count() > 0)
                                <span class="badge bg-danger">
                                    {{ auth()->user()->unreadMessages()->count() }}
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
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Hồ sơ</a></li>
                            <li><a class="dropdown-item" href="#">Cài đặt</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Đăng xuất</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav> 