@props(['user'])

@if(auth()->check() && auth()->id() !== $user->id)
    <button 
        class="follow-button {{ auth()->user()->isFollowing($user) ? 'following' : '' }}"
        data-user-id="{{ $user->id }}"
        data-following="{{ auth()->user()->isFollowing($user) ? 'true' : 'false' }}"
    >
        <span class="follow-text">{{ auth()->user()->isFollowing($user) ? 'Bỏ theo dõi' : 'Theo dõi' }}</span>
    </button>

    <style>
        .follow-button {
            padding: 8px 16px;
            border-radius: 20px;
            border: 1px solid #1da1f2;
            background: white;
            color: #1da1f2;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .follow-button:hover {
            background: rgba(29, 161, 242, 0.1);
        }

        .follow-button.following {
            background: #1da1f2;
            color: white;
        }

        .follow-button.following:hover {
            background: #1991db;
            border-color: #1991db;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const button = document.querySelector('.follow-button[data-user-id="{{ $user->id }}"]');
            if (button) {
                button.addEventListener('click', async function() {
                    const userId = this.dataset.userId;
                    const isFollowing = this.dataset.following === 'true';
                    const url = isFollowing ? `/users/${userId}/unfollow` : `/users/${userId}/follow`;
                    
                    try {
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        });
                        
                        const data = await response.json();
                        
                        if (response.ok) {
                            this.dataset.following = data.following;
                            this.classList.toggle('following');
                            this.querySelector('.follow-text').textContent = data.following ? 'Bỏ theo dõi' : 'Theo dõi';
                        } else {
                            alert(data.message);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Có lỗi xảy ra');
                    }
                });
            }
        });
    </script>
@endif 