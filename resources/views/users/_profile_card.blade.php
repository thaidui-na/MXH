<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <img src="{{ $user->avatar_url ?? asset('images/default-avatar.jpg') }}" 
                     class="rounded-circle me-3" 
                     style="width: 64px; height: 64px; object-fit: cover;"
                     alt="{{ $user->name }}'s avatar">
                <div>
                    <h4 class="mb-0">
                        <a href="{{ route('users.show', $user->id) }}" class="text-decoration-none text-dark">
                            {{ $user->name }}
                        </a>
                    </h4>
                    <p class="text-muted mb-0">{{ $user->email }}</p>
                </div>
            </div>

            <div class="d-flex align-items-center">
                <div class="text-center mx-3">
                    <h4 class="mb-0">{{ $totalPosts ?? 0 }}</h4>
                    <small class="text-muted">Tổng bài viết</small>
                </div>
                <div class="text-center me-4">
                    <h4 class="mb-0">{{ $user->created_at->format('d/m/Y') }}</h4>
                    <small class="text-muted">Ngày tham gia</small>
                </div>

                @if(auth()->check() && auth()->id() !== $user->id)
                    <div class="dropdown">
                        <button class="btn btn-light btn-sm rounded-circle shadow-sm d-flex align-items-center justify-content-center" 
                                type="button" 
                                data-bs-toggle="dropdown" 
                                aria-expanded="false" 
                                style="width: 32px; height: 32px; padding: 0;">
                            <i class="fas fa-ellipsis-v" style="font-size: 16px;"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            <li>
                                <button type="button" class="dropdown-item text-danger" onclick="showReportModal({{ $user->id }})">
                                    <i class="fas fa-flag me-2"></i>Báo cáo
                                </button>
                            </li>
                            <li>
                                @if(auth()->user()->hasBlocked($user->id))
                                    <form action="{{ route('users.unblock', $user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-user-check me-2"></i>Bỏ chặn
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('users.block', $user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-user-slash me-2"></i>Chặn
                                        </button>
                                    </form>
                                @endif
                            </li>
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .dropdown .btn-light {
        background-color: white;
        border: 1px solid #dee2e6;
    }
    .dropdown .btn-light:hover {
        background-color: #f8f9fa;
    }
    .dropdown-menu {
        min-width: 200px;
        margin-top: 5px;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        border: none;
    }
    .dropdown-item {
        padding: 8px 16px;
    }
    .dropdown-item:hover {
        background-color: #f8f9fa;
    }
    .dropdown-item.text-danger:hover {
        background-color: #dc3545;
        color: white !important;
    }
    .dropdown-item i {
        width: 20px;
    }
</style>
@endpush 