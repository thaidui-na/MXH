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
            
            // Lấy query từ cả hai tham số có thể có
            $query = $request->input('q') ?? $request->input('query');
            Log::info('Search query:', ['query' => $query]);
            
            if (empty($query)) {
                return response()->json(['users' => []]);
            }

            // Tìm kiếm với điều kiện chính xác hơn và loại trừ người dùng đã bị chặn
            $users = User::query()
                ->where(function($q) use ($query) {
                    $q->where('name', 'like', '%' . $query . '%')
                      ->orWhere('email', 'like', '%' . $query . '%');
                })
                ->where('account_status', 'active') // Chỉ lấy tài khoản đang hoạt động
                ->whereNull('deleted_at') // Loại bỏ tài khoản đã bị xóa mềm
                ->when(auth()->check(), function($q) {
                    $q->where('id', '!=', auth()->id());
                    // Loại trừ người dùng mà người dùng hiện tại đã chặn
                    $q->whereDoesntHave('blockedBy', function ($query) {
                        $query->where('blocker_id', auth()->id());
                    });
                })
                ->select('id', 'name', 'email', 'avatar')
                ->limit(12)
                ->get();

            // Log câu query SQL để debug
            Log::info('SQL Query:', [
                'queries' => DB::getQueryLog()
            ]);

            // Map kết quả và thêm thông tin về trạng thái bạn bè
            $mappedUsers = $users->map(function ($user) {
                try {
                    $currentUser = auth()->user();
                    $isFriend = $currentUser ? $currentUser->isFriendWith($user) : false;
                    $hasPendingRequest = $currentUser ? $currentUser->hasSentFriendRequestTo($user) : false;
                    $hasReceivedRequest = $currentUser ? $currentUser->hasReceivedFriendRequestFrom($user) : false;
                    
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'avatar' => $user->avatar ? asset('storage/' . $user->avatar) : asset('images/default-avatar.jpg'),
                        'isFriend' => $isFriend,
                        'hasPendingRequest' => $hasPendingRequest,
                        'hasReceivedRequest' => $hasReceivedRequest
                    ];
                } catch (\Exception $e) {
                    Log::error('Error mapping user data:', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage()
                    ]);
                    return null;
                }
            })->filter(); // Lọc bỏ các kết quả null

            Log::info('Search results:', [
                'count' => $mappedUsers->count(),
                'users' => $mappedUsers->toArray()
            ]);

            return response()->json(['users' => $mappedUsers]);
        } catch (\Exception $e) {
            Log::error('Search error: ' . $e->getMessage(), [
                'exception' => $e,
                'query' => $request->input('q') ?? $request->input('query'),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Có lỗi xảy ra khi tìm kiếm',
                'message' => $e->getMessage()
            ], 500);
        }
    }
} 