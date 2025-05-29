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
        body.dark-mode {
            background: #18191a !important;
            color: #e4e6eb !important;
        }
        .dark-mode .navbar, .dark-mode .card, .dark-mode .dropdown-menu {
            background: #242526 !important;
            color: #e4e6eb !important;
            border-color: #3a3b3c !important;
        }
        .dark-mode .card-header, .dark-mode .card-footer {
            background: #242526 !important;
            color: #e4e6eb !important;
        }
        .dark-mode .form-control, .dark-mode .form-select {
            background: #3a3b3c !important;
            color: #e4e6eb !important;
            border-color: #555;
        }
        .dark-mode .form-control:focus, .dark-mode .form-select:focus {
            background: #242526 !important;
            color: #e4e6eb !important;
        }
        .dark-mode .btn-outline-secondary {
            color: #e4e6eb;
            border-color: #555;
        }
        .dark-mode .btn-outline-secondary:hover {
            background: #3a3b3c;
            color: #fff;
        }
        .dark-mode .list-group-item {
            background: #242526 !important;
            color: #e4e6eb !important;
            border-color: #3a3b3c !important;
        }
        .dark-mode .bg-light {
            background: #3a3b3c !important;
            color: #e4e6eb !important;
        }
        .dark-mode .dropdown-item {
            color: #e4e6eb !important;
        }
        .dark-mode .dropdown-item:hover {
            background: #3a3b3c !important;
            color: #fff !important;
        }
        .dark-mode .modal-content {
            background: #242526 !important;
            color: #e4e6eb !important;
        }
        .dark-mode .border, .dark-mode .border-bottom, .dark-mode .border-top {
            border-color: #3a3b3c !important;
        }
        .dark-mode .text-muted {
            color: #b0b3b8 !important;
        }
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

        /* Search Results Styles */
        .hidden {
            display: none !important;
        }

        #searchResults {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            z-index: 1000;
            margin-top: 5px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .search-result-item {
            padding: 10px 15px;
            display: flex;
            align-items: center;
            text-decoration: none;
            color: inherit;
            transition: background-color 0.2s;
            border-bottom: 1px solid #eee;
            flex-wrap: nowrap;
        }

        .search-result-item .flex-grow-1 {
            min-width: 0;
            margin-right: 10px;
        }

        .search-result-item .fw-bold {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .search-result-item .friend-button {
            flex-shrink: 0;
            width: 100px;
            text-align: center;
        }

        .search-result-item:last-child {
            border-bottom: none;
        }

        .search-result-item:hover {
            background-color: #f8f9fa;
            text-decoration: none;
        }

        .search-result-item img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
            object-fit: cover;
        }

        .search-result-info {
            flex-grow: 1;
        }

        .search-result-name {
            font-weight: 500;
            margin-bottom: 2px;
        }

        .search-result-email {
            font-size: 0.875rem;
            color: #6c757d;
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

    <!-- Global Like Functionality -->
    <!-- XÓA TOÀN BỘ JS LIKE -->

    <!-- Search Functionality -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const searchResults = document.getElementById('searchResults');
        let searchTimeout;

        if (!searchInput || !searchResults) {
            console.error('Search elements not found');
            return;
        }

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();

            if (query.length < 2) {
                searchResults.innerHTML = '';
                searchResults.classList.add('hidden');
                return;
            }

            searchTimeout = setTimeout(() => {
                fetch(`/api/users/search?q=${encodeURIComponent(query)}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    searchResults.innerHTML = '';
                    
                    if (!data.users || data.users.length === 0) {
                        searchResults.innerHTML = `
                            <div class="p-4 text-center">
                                <div class="text-muted mb-2">Không tìm thấy kết quả</div>
                                <div class="text-muted small">Từ khóa tìm kiếm: "${query}"</div>
                            </div>
                        `;
                    } else {
                        data.users.forEach(user => {
                            const userElement = document.createElement('a');
                            userElement.href = `/posts/my_posts/${user.id}`;
                            userElement.className = 'search-result-item d-flex align-items-center text-decoration-none';
                            userElement.innerHTML = `
                                <img src="${user.avatar || '/images/default-avatar.jpg'}" 
                                     alt="${user.name}" 
                                     class="rounded-circle me-2"
                                     style="width: 40px; height: 40px; object-fit: cover;"
                                     onerror="this.src='/images/default-avatar.jpg'">
                                <div class="flex-grow-1">
                                    <div class="fw-bold text-dark">${user.name}</div>
                                    ${user.email ? `<div class="text-muted small">${user.email}</div>` : ''}
                                </div>
                                <button class="btn btn-sm ${user.isFriend ? 'btn-primary' : (user.hasPendingRequest ? 'btn-secondary' : (user.hasReceivedRequest ? 'btn-success' : 'btn-outline-primary'))} rounded-pill friend-button ms-2" 
                                        data-user-id="${user.id}"
                                        onclick="event.preventDefault(); ${user.isFriend ? 'toggleFriend' : (user.hasPendingRequest ? 'toggleFriend' : (user.hasReceivedRequest ? 'acceptFriendRequest' : 'toggleFriend'))}(${user.id}, this);">
                                    <i class="fas fa-${user.isFriend ? 'user-friends' : (user.hasPendingRequest ? 'clock' : (user.hasReceivedRequest ? 'user-check' : 'user-plus'))}"></i> 
                                    ${user.isFriend ? 'Bạn bè' : (user.hasPendingRequest ? 'Đã gửi lời mời' : (user.hasReceivedRequest ? 'Chấp nhận' : 'Kết bạn'))}
                                </button>
                            `;
                            searchResults.appendChild(userElement);
                        });
                    }
                    
                    searchResults.classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Search error:', error);
                    searchResults.innerHTML = `
                        <div class="p-4 text-center">
                            <div class="text-danger mb-2">Có lỗi xảy ra khi tìm kiếm</div>
                            <div class="text-muted small">Từ khóa tìm kiếm: "${query}"</div>
                        </div>
                    `;
                    searchResults.classList.remove('hidden');
                });
            }, 300);
        });

        // Đóng kết quả tìm kiếm khi click ra ngoài
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.classList.add('hidden');
            }
        });
    });

    // Follow/Unfollow functionality
    function toggleFriend(userId, button) {
        const isFriend = button.classList.contains('btn-primary');
        const hasPendingRequest = button.classList.contains('btn-secondary');
        const url = isFriend || hasPendingRequest ? `/users/${userId}/remove-friend` : `/users/${userId}/add-friend`;
        const method = isFriend || hasPendingRequest ? 'DELETE' : 'POST';

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.isFriend) {
                    button.classList.remove('btn-outline-primary', 'btn-secondary');
                    button.classList.add('btn-primary');
                    button.innerHTML = '<i class="fas fa-user-friends"></i> Bạn bè';
                } else if (data.hasPendingRequest) {
                    button.classList.remove('btn-outline-primary', 'btn-primary');
                    button.classList.add('btn-secondary');
                    button.innerHTML = '<i class="fas fa-clock"></i> Đã gửi lời mời';
                } else {
                    button.classList.remove('btn-primary', 'btn-secondary');
                    button.classList.add('btn-outline-primary');
                    button.innerHTML = '<i class="fas fa-user-plus"></i> Kết bạn';
                }
                // Hiển thị thông báo thành công
                alert(data.message || 'Thao tác thành công');
            } else {
                // Hiển thị thông báo lỗi
                alert(data.error || 'Có lỗi xảy ra');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi thực hiện thao tác');
        });
    }

    // Hàm xử lý chấp nhận lời mời kết bạn
    function acceptFriendRequest(userId, button) {
        fetch(`/users/${userId}/accept-friend-request`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Cập nhật giao diện nút
                button.classList.remove('btn-outline-primary', 'btn-secondary');
                button.classList.add('btn-primary');
                button.innerHTML = '<i class="fas fa-user-friends"></i> Bạn bè';
                
                // Hiển thị thông báo thành công
                alert(data.message || 'Đã chấp nhận lời mời kết bạn thành công');
            } else {
                // Hiển thị thông báo lỗi
                alert(data.error || 'Có lỗi xảy ra');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi thực hiện thao tác');
        });
    }

    // Hàm xử lý từ chối lời mời kết bạn
    function rejectFriendRequest(userId, button) {
        fetch(`/users/${userId}/reject-friend-request`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Cập nhật giao diện nút
                button.classList.remove('btn-primary', 'btn-secondary');
                button.classList.add('btn-outline-primary');
                button.innerHTML = '<i class="fas fa-user-plus"></i> Kết bạn';
                
                // Hiển thị thông báo thành công
                alert(data.message || 'Đã từ chối lời mời kết bạn');
            } else {
                // Hiển thị thông báo lỗi
                alert(data.error || 'Có lỗi xảy ra');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi thực hiện thao tác');
        });
    }
    </script>

    @stack('scripts')
</body>
</html> 