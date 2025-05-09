<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Mạng xã hội') }} - @yield('title')</title>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        .navbar-brand {
            font-weight: bold;
        }
        
        .footer {
            background-color: #f8f9fa;
            padding: 1.5rem 0;
            margin-top: 3rem;
        }
        
        .main-content {
            min-height: calc(100vh - 160px);
        }
    </style>
    
    @stack('styles')
</head>
<body>
    @include('layouts.partials.header')
    
    <div class="main-content">
        @yield('content')
    </div>
    
    @include('layouts.partials.footer')
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')

    {{-- ======= JavaScript Bổ Sung ======= --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // --- Header Scroll Effect ---
            const navbar = document.querySelector('.navbar.fixed-top');
            if (navbar) {
                const scrollThreshold = 50; // Ngưỡng cuộn để thay đổi header

                const handleScroll = () => {
                    if (window.scrollY > scrollThreshold) {
                        navbar.classList.add('navbar-scrolled');
                    } else {
                        navbar.classList.remove('navbar-scrolled');
                    }
                };

                window.addEventListener('scroll', handleScroll);
                handleScroll(); // Kiểm tra trạng thái ban đầu khi tải trang
            }

            // --- Back to Top Button ---
            const backToTopButton = document.getElementById('back-to-top-btn');

            if (backToTopButton) {
                const scrollThresholdBtn = 300; // Ngưỡng cuộn để hiện nút

                const toggleBackToTopButton = () => {
                    if (window.scrollY > scrollThresholdBtn) {
                        backToTopButton.classList.add('show');
                    } else {
                        backToTopButton.classList.remove('show');
                    }
                };

                const scrollToTop = () => {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                };

                window.addEventListener('scroll', toggleBackToTopButton);
                backToTopButton.addEventListener('click', scrollToTop);
                toggleBackToTopButton(); // Kiểm tra trạng thái ban đầu
            }
        });
    </script>
    {{-- ================================ --}}
</body>
</html> 