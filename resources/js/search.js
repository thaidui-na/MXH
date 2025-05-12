document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');
    const searchForm = document.getElementById('searchForm');
    let searchTimeout;

    if (!searchInput || !searchResults || !searchForm) {
        console.error('Search elements not found');
        return;
    }

    // Hàm hiển thị kết quả tìm kiếm
    function displaySearchResults(data) {
        searchResults.innerHTML = '';

        if (!data || data.length === 0) {
            searchResults.innerHTML = '<div class="p-4 text-center text-muted">Không tìm thấy kết quả</div>';
            return;
        }

        // Thêm header cho kết quả tìm kiếm
        const header = document.createElement('div');
        header.className = 'search-header';
        header.textContent = 'Kết quả tìm kiếm';
        searchResults.appendChild(header);

        data.forEach(user => {
            const userElement = document.createElement('div');
            userElement.className = 'search-result-item';
            userElement.innerHTML = `
                <div class="d-flex align-items-center flex-grow-1">
                    <img src="${user.avatar_url || '/images/default-avatar.jpg'}" 
                         alt="${user.name}" 
                         class="rounded-circle"
                         style="width: 40px; height: 40px; object-fit: cover;">
                    <div class="user-info ms-2">
                        <h6 class="user-name mb-0">${user.name}</h6>
                        <p class="user-email mb-0">${user.email}</p>
                    </div>
                </div>
                <button class="btn btn-sm btn-outline-primary add-friend-btn" data-user-id="${user.id}">
                    <i class="fas fa-user-plus"></i> Thêm bạn
                </button>
            `;
            searchResults.appendChild(userElement);

            // Thêm sự kiện click cho nút thêm bạn
            const addFriendBtn = userElement.querySelector('.add-friend-btn');
            addFriendBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                const userId = this.dataset.userId;
                addFriend(userId, this);
            });
        });
    }

    // Hàm xử lý thêm bạn
    function addFriend(userId, button) {
        fetch(`/api/friends/add/${userId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin'
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    button.innerHTML = '<i class="fas fa-check"></i> Đã gửi lời mời';
                    button.disabled = true;
                    button.classList.remove('btn-outline-primary');
                    button.classList.add('btn-success');
                } else {
                    alert(data.message || 'Có lỗi xảy ra khi gửi lời mời kết bạn');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi gửi lời mời kết bạn');
            });
    }

    // Hàm tìm kiếm
    function performSearch(query) {
        if (query.length < 1) {
            searchResults.innerHTML = '';
            searchResults.classList.add('hidden');
            return;
        }

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
                displaySearchResults(data);
                searchResults.classList.remove('hidden');
            })
            .catch(error => {
                console.error('Search error:', error);
                searchResults.innerHTML = '<div class="p-4 text-center text-danger">Có lỗi xảy ra khi tìm kiếm</div>';
                searchResults.classList.remove('hidden');
            });
    }

    // Xử lý sự kiện input
    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        performSearch(query);
    });

    // Xử lý submit form
    searchForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const query = searchInput.value.trim();
        performSearch(query);
    });

    // Đóng kết quả tìm kiếm khi click ra ngoài
    document.addEventListener('click', function (e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.classList.add('hidden');
        }
    });
}); 