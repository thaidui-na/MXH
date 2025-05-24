@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h1 class="text-2xl font-bold mb-6">Danh sách bạn bè</h1>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($friends as $friend)
                    <div class="bg-gray-50 rounded-lg p-4 flex items-center space-x-4 hover:bg-gray-100 transition duration-200">
                        <div class="flex-shrink-0">
                            <img src="{{ $friend->avatar ? asset('storage/' . $friend->avatar) : asset('images/default-avatar.png') }}" 
                                 alt="{{ $friend->name }}" 
                                 class="w-16 h-16 rounded-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-lg font-semibold text-gray-900 truncate">
                                {{ $friend->name }}
                            </p>
                            <p class="text-sm text-gray-500 truncate">
                                {{ $friend->email }}
                            </p>
                            <div class="mt-2">
                                <a href="{{ route('profile.show', $friend->id) }}" 
                                   class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Xem trang cá nhân
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-8">
                        <p class="text-gray-500 text-lg">Bạn chưa có bạn bè nào.</p>
                        <a href="{{ route('users.index') }}" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            Tìm bạn mới
                        </a>
                    </div>
                @endforelse
            </div>

            @if($friends->hasPages())
                <div class="mt-6">
                    {{ $friends->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 