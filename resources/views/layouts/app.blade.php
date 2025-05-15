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

        /* Search Results Styles */
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
            border-bottom: 1px solid #eee;
            text-decoration: none;
            color: #2c3e50;
            transition: background-color 0.2s ease;
            display: flex;
            align-items: center;
            gap: 10px;
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
            object-fit: cover;
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .search-result-item .user-name {
            margin: 0;
            font-size: 1rem;
            font-weight: 500;
        }

        /* Search Container */
        .search-container {
            position: relative;
            width: 300px;
        }

        /* Style cho nút like */
        .like-button {
            transition: all 0.3s ease;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            border: 1px solid #dc3545;
            background-color: transparent;
            color: #dc3545;
        }

        .like-button i {
            font-size: 1.1rem;
            margin-right: 0.3rem;
            transition: all 0.3s ease;
        }

        .like-button.active {
            background-color: #ffebee;
            border-color: #ffcdd2;
        }

        .like-button.active i {
            color: #e53935 !important;
            transform: scale(1.1);
        }

        .like-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            background-color: #ffebee;
        }

        .like-button:hover i {
            transform: scale(1.1);
        }

        .like-count {
            font-weight: 500;
            margin-left: 0.3rem;
        }

        .search-container {
            width: 300px;
            z-index: 1050;
        }

        .search-results {
            background: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            max-height: 400px;
            overflow-y: auto;
        }

        .search-result-item {
            padding: 10px 15px;
            display: flex;
            align-items: center;
            text-decoration: none;
            color: inherit;
            transition: background-color 0.2s;
            border-bottom: 1px solid #eee;
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
                fetch(`/users/search?query=${encodeURIComponent(query)}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
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
                                <button class="btn btn-sm ${user.isFriend ? 'btn-primary' : 'btn-outline-primary'} friend-button ms-2" 
                                        data-user-id="${user.id}"
                                        onclick="event.preventDefault(); toggleFriend(${user.id}, this);">
                                    <i class="fas fa-${user.isFriend ? 'user-friends' : 'user-plus'}"></i> 
                                    ${user.isFriend ? 'Bạn bè' : 'Kết bạn'}
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
        const url = isFriend ? `/users/${userId}/remove-friend` : `/users/${userId}/add-friend`;
        const method = isFriend ? 'DELETE' : 'POST';

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
                    button.classList.remove('btn-outline-primary');
                    button.classList.add('btn-primary');
                    button.innerHTML = '<i class="fas fa-user-friends"></i> Bạn bè';
                } else {
                    button.classList.remove('btn-primary');
                    button.classList.add('btn-outline-primary');
                    button.innerHTML = '<i class="fas fa-user-plus"></i> Kết bạn';
                }
            } else {
                alert(data.error);
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