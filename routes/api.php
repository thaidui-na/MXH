<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'web'])->group(function () {
    Route::get('/users/search', [UserController::class, 'search']);
}); 

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Route tìm kiếm người dùng
    Route::get('/users/search', function (Request $request) {
        try {
            \Log::info('Search request received', [
                'query' => $request->get('q'),
                'user_id' => auth()->id()
            ]);

            $query = $request->get('q');
            
            if (empty($query)) {
                \Log::info('Empty query received');
                return response()->json([]);
            }

            $users = User::where('id', '!=', auth()->id())
                ->where(function($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('email', 'like', "%{$query}%");
                })
                ->where('account_status', 'active')
                ->whereNull('deleted_at')
                ->excludeBlocked()
                ->select('id', 'name', 'email', 'avatar')
                ->limit(12)
                ->get();

            \Log::info('Search results', [
                'count' => $users->count(),
                'query' => $query
            ]);

            $results = $users->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar_url' => $user->avatar ? Storage::url($user->avatar) : asset('images/default-avatar.jpg')
                ];
            });

            return response()->json($results);
        } catch (\Exception $e) {
            \Log::error('User search error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'query' => $request->get('q')
            ]);
            
            return response()->json([
                'error' => 'Có lỗi xảy ra khi tìm kiếm',
                'message' => $e->getMessage()
            ], 500);
        }
    });
}); 