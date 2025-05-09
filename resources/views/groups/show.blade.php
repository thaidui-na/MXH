@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            {{-- Cover và avatar nhóm --}}
            <div class="position-relative mb-4">
                <img src="{{ $group->cover_image ? asset('storage/' . $group->cover_image) : asset('images/default-cover.jpg') }}"
                     onerror="this.onerror=null;this.src='{{ asset('images/default-cover.jpg') }}';"
                     class="w-100 rounded" style="height: 220px; object-fit: cover;">
                <img src="{{ $group->avatar ? asset('storage/' . $group->avatar) : asset('images/default-avatar.jpg') }}"
                     onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.jpg') }}';"
                     class="rounded-circle border border-3 border-white position-absolute" style="width: 110px; height: 110px; left: 40px; bottom: -55px; object-fit: cover; background: #fff;">
            </div>
            <div class="d-flex align-items-center mb-4" style="margin-top: 40px;">
                <div class="flex-grow-1">
                    <h3 class="mb-1">{{ $group->name }}</h3>
                    <div class="text-muted mb-1">{{ $group->description }}</div>
                    <div class="d-flex align-items-center gap-3">
                        <span><i class="fas fa-users"></i> {{ $group->members->count() }} thành viên</span>
                        <span class="badge {{ $group->is_private ? 'bg-secondary' : 'bg-success' }}">{{ $group->is_private ? 'Riêng tư' : 'Công khai' }}</span>
                    </div>
                </div>
                <div>
                    @if($group->created_by == auth()->id())
                        <a href="{{ route('groups.edit', $group->id) }}" class="btn btn-outline-primary me-2">Chỉnh sửa nhóm</a>
                        <form action="{{ route('groups.destroy', $group->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa nhóm này? Hành động này không thể hoàn tác!');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">Xóa nhóm</button>
                        </form>
                    @endif
                    @if($group->members->where('user_id', auth()->id())->count() == 0)
                        <form method="POST" action="{{ route('groups.join', $group->id) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success">Tham gia nhóm</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('groups.leave', $group->id) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-danger">Rời nhóm</button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- Tabs: Thảo luận, Thành viên --}}
            <ul class="nav nav-tabs mb-4" id="groupTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="discussion-tab" data-bs-toggle="tab" data-bs-target="#discussion-pane" type="button" role="tab" aria-controls="discussion-pane" aria-selected="true">
                        <i class="fas fa-comments"></i> Thảo luận
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="members-tab" data-bs-toggle="tab" data-bs-target="#members-pane" type="button" role="tab" aria-controls="members-pane" aria-selected="false">
                        <i class="fas fa-user-friends"></i> Thành viên
                    </button>
                </li>
            </ul>
            <div class="tab-content" id="groupTabContent">
                {{-- Tab Thảo luận --}}
                <div class="tab-pane fade show active" id="discussion-pane" role="tabpanel" aria-labelledby="discussion-tab">
                    @if($group->members->where('user_id', auth()->id())->count() > 0)
                        <form method="POST" action="{{ route('groups.post', $group->id) }}" class="mb-4">
                            @csrf
                            <div class="mb-3">
                                <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" placeholder="Tiêu đề bài viết" value="{{ old('title') }}" required>
                                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <textarea class="form-control @error('content') is-invalid @enderror" name="content" rows="3" placeholder="Bạn muốn chia sẻ điều gì?" required>{{ old('content') }}</textarea>
                                @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Đăng bài</button>
                            </div>
                        </form>
                    @endif
                    @if($group->posts->count() > 0)
                        @foreach($group->posts as $post)
                            <div class="card mb-3 post-card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <img src="{{ $post->user->avatar_url }}" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                        <div>
                                            <strong>{{ $post->user->name }}</strong><br>
                                            <small class="text-muted">{{ $post->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                    <h5 class="card-title">{{ $post->title }}</h5>
                                    <p class="card-text">{{ $post->content }}</p>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-center">Chưa có bài viết nào.</p>
                    @endif
                </div>
                {{-- Tab Thành viên --}}
                <div class="tab-pane fade" id="members-pane" role="tabpanel" aria-labelledby="members-tab">
                    <div class="row">
                        @foreach($group->members as $member)
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-body d-flex align-items-center">
                                        <img src="{{ $member->user->avatar_url }}" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                        <div>
                                            <strong>{{ $member->user->name }}</strong><br>
                                            <small class="text-muted">{{ $member->role }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 