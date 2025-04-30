@extends('layouts.app')

@push('styles')
<style>
    #search-results {
        max-height: 300px;
        overflow-y: auto;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        width: 100%;
        z-index: 1000;
    }

    #search-results a {
        padding: 10px 15px;
        transition: background 0.2s ease;
    }

    #search-results a:hover {
        background-color: #f5f5f5;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <h2 class="mb-3 mb-md-0">My Groups</h2>

        <div class="w-100 position-relative me-md-3 mb-3 mb-md-0" style="max-width: 400px;">
            <input type="text" id="group-search" class="form-control" placeholder="Tìm kiếm nhóm...">
            <div id="search-results" class="list-group position-absolute mt-1" style="display: none;"></div>
        </div>

        <a href="{{ route('groups.create') }}" class="btn btn-primary">Tạo Nhóm Mới</a>
    </div>

    <div class="row">
        @foreach($groups as $group)
            <div class="col-md-4">
                <div class="card shadow mb-4">
                    @if($group->image)
                        <img src="{{ asset($group->image) }}" class="card-img-top" alt="Group Image">
                    @else
                        <img src="https://via.placeholder.com/300x150" class="card-img-top" alt="Default Image">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $group->name }}</h5>
                        <p class="card-text">{{ Str::limit($group->description, 100) }}</p>
                        <a href="{{ route('groups.show', $group) }}" class="btn btn-outline-primary">Xem Nhóm</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script>
    const searchInput = document.getElementById('group-search');
    const resultBox = document.getElementById('search-results');

    searchInput.addEventListener('keyup', function () {
        const query = this.value;

        if (query.length > 0) {
            fetch(`/search-groups?query=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    let html = '';
                    data.forEach(group => {
                        html += `
                            <a href="/groups/${group.id}" class="list-group-item list-group-item-action">
                                <div class="d-flex align-items-center">
                                    <img src="${group.image ? '/' + group.image : 'https://via.placeholder.com/40'}" alt="Group" style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%; margin-right: 10px;">
                                    <div>
                                        <strong>${group.name}</strong><br>
                                        <small>${group.description ?? ''}</small>
                                    </div>
                                </div>
                            </a>
                        `;
                    });

                    resultBox.innerHTML = html;
                    resultBox.style.display = 'block';
                });
        } else {
            resultBox.innerHTML = '';
            resultBox.style.display = 'none';
        }
    });

    document.addEventListener('click', function (e) {
        if (!searchInput.contains(e.target) && !resultBox.contains(e.target)) {
            resultBox.style.display = 'none';
        }
    });
</script>
@endpush
