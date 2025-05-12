document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');
    let searchTimeout;

    if (!searchInput || !searchResults) {
        console.error('Search elements not found');
        return;
    }

    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimeout);
        const query = this.value.trim();

        if (query.length < 2) {
            searchResults.innerHTML = '';
            searchResults.classList.add('hidden');
            return;
        }

        searchTimeout = setTimeout(() => {
            fetch(`/api/users/search?query=${encodeURIComponent(query)}`, {
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
                        searchResults.innerHTML = '<div class="p-4 text-center text-muted">Không tìm thấy kết quả</div>';
                    } else {
                        data.users.forEach(user => {
                            const userElement = document.createElement('a');
                            userElement.href = `/profile/${user.id}`;
                            userElement.className = 'search-result-item';
                            userElement.innerHTML = `
                            <img src="${user.avatar || '/images/default-avatar.png'}" 
                                 alt="${user.name}" 
                                 class="rounded-circle"
                                 style="width: 40px; height: 40px; object-fit: cover;">
                            <div class="user-info">
                                <h6 class="user-name mb-0">${user.name}</h6>
                            </div>
                        `;
                            searchResults.appendChild(userElement);
                        });
                    }

                    searchResults.classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Search error:', error);
                    searchResults.innerHTML = '<div class="p-4 text-center text-danger">Có lỗi xảy ra khi tìm kiếm</div>';
                    searchResults.classList.remove('hidden');
                });
        }, 300);
    });

    // Đóng kết quả tìm kiếm khi click ra ngoài
    document.addEventListener('click', function (e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.classList.add('hidden');
        }
    });
}); 