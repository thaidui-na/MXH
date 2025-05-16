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
            // Bật query logging
            DB::enableQueryLog();
            
            $query = $request->input('q');
            Log::info('Search query:', ['query' => $query]);
            
            if (empty($query)) {
                return response()->json(['users' => []]);
            }

            // Tìm kiếm với điều kiện chính xác hơn
            $users = User::query()
                ->where(function($q) use ($query) {
                    $q->where('name', 'like', '%' . $query . '%')
                      ->orWhere('email', 'like', '%' . $query . '%');
                })
                ->when(auth()->check(), function($q) {
                    $q->where('id', '!=', auth()->id());
                })
                ->select('id', 'name', 'email', 'avatar')
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'avatar' => $user->avatar ? asset('storage/' . $user->avatar) : asset('images/default-avatar.jpg')
                    ];
                });

            // Log câu query SQL để debug
            Log::info('SQL Query:', [
                'queries' => DB::getQueryLog()
            ]);

            Log::info('Search results:', [
                'count' => $users->count(),
                'users' => $users->toArray()
            ]);

            return response()->json(['users' => $users]);
        } catch (\Exception $e) {
            Log::error('Search error: ' . $e->getMessage(), [
                'exception' => $e,
                'query' => $request->input('q')
            ]);
            return response()->json(['error' => 'Có lỗi xảy ra khi tìm kiếm'], 500);
        }
    }
} 