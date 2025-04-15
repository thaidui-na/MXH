<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #f5f5f5;
        }
        
        .sidebar {
            background-color: #1a1a1a;
            min-height: 100vh;
            padding: 0;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid #333;
        }

        .sidebar-header h5 {
            color: #fff;
            font-size: 1.1rem;
            margin: 0;
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        .sidebar .nav-link {
            color: #a8a8a8;
            padding: 1rem 1.5rem;
            border-radius: 0;
            transition: all 0.3s ease;
            font-size: 0.95rem;
            border-left: 3px solid transparent;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar .nav-link:hover {
            color: #fff;
            background-color: #2d2d2d;
            border-left-color: #0d6efd;
        }

        .sidebar .nav-link.active {
            color: #fff;
            background-color: #2d2d2d;
            border-left-color: #0d6efd;
        }

        .sidebar .nav-link i {
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }

        .content-wrapper {
            padding: 2rem;
            background-color: #fff;
            border-radius: 10px;
            margin: 1rem;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
        }

        .page-title {
            color: #333;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #dee2e6;
        }

        .alert {
            margin-bottom: 1.5rem;
            border-radius: 8px;
        }

        .nav-divider {
            height: 1px;
            background-color: #333;
            margin: 0.5rem 1.5rem;
            opacity: 0.3;
        }

        .home-link {
            margin-top: auto;
            border-top: 1px solid #333;
            padding-top: 0.5rem;
        }

        .nav-section {
            margin-bottom: 1rem;
        }

        .nav-section-title {
            color: #666;
            font-size: 0.75rem;
            text-transform: uppercase;
            padding: 1rem 1.5rem 0.5rem;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <div class="sidebar-header">
                    <h5>Admin Panel</h5>
                </div>
                
                <nav class="nav flex-column">
                    <div class="nav-section">
                        <div class="nav-section-title">TỔNG QUAN</div>
                        <a href="{{ route('admin.dashboard') }}" 
                           class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="bi bi-grid-1x2-fill"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('posts.index') }}" class="nav-link">
                            <i class="bi bi-house-door-fill"></i>
                            <span>Về trang chủ</span>
                        </a>
                    </div>

                    <div class="nav-section">
                        <div class="nav-section-title">QUẢN LÝ</div>
                        <a href="{{ route('admin.users') }}" 
                           class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                            <i class="bi bi-people-fill"></i>
                            <span>Người dùng</span>
                        </a>
                        <a href="{{ route('admin.posts') }}" 
                           class="nav-link {{ request()->routeIs('admin.posts*') ? 'active' : '' }}">
                            <i class="bi bi-file-text-fill"></i>
                            <span>Bài viết</span>
                        </a>
                    </div>
                </nav>
            </div>

            <!-- Content -->
            <div class="col-md-10">
                <div class="content-wrapper">
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
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
</body>
</html>
