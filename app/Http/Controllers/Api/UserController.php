<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function search(Request $request)
    {
        try {
            $query = $request->input('query');
            Log::info('Search query:', ['query' => $query]);
            
            if (empty($query)) {
                return response()->json(['users' => []]);
            }

            // Log tất cả users trong database để debug
            $allUsers = User::select('id', 'name')->get();
            Log::info('All users in database:', ['users' => $allUsers->toArray()]);

            // Tìm kiếm với điều kiện đơn giản
            $users = User::where('name', 'like', '%' . $query . '%')
                ->where('id', '!=', auth()->id())
                ->select('id', 'name', 'avatar')
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'avatar' => $user->avatar ? asset('storage/' . $user->avatar) : asset('images/default-avatar.png')
                    ];
                });

            // Log câu query SQL để debug
            Log::info('SQL Query:', [
                'sql' => DB::getQueryLog(),
                'bindings' => DB::getQueryLog()
            ]);

            Log::info('Search results:', [
                'count' => $users->count(),
                'users' => $users->toArray()
            ]);

            return response()->json(['users' => $users]);
        } catch (\Exception $e) {
            Log::error('Search error: ' . $e->getMessage(), [
                'exception' => $e,
                'query' => $request->input('query')
            ]);
            return response()->json(['error' => 'Có lỗi xảy ra khi tìm kiếm'], 500);
        }
    }
} 