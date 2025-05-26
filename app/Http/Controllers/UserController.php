<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Tìm kiếm người dùng bằng AJAX.
     */
    public function searchAjax(Request $request)
    {
        $q = $request->q;
        $users = User::query()
            ->when($q, function($query) use ($q) {
                $query->where(function($sub) use ($q) {
                    $sub->where('name', 'like', "%$q%")
                         ->orWhere('email', 'like', "%$q%");
                });
            })
            ->excludeBlocked() // Loại bỏ người dùng đã bị chặn
            ->where('id', '!=', auth()->id()) // Loại bỏ người dùng hiện tại
            ->limit(10)
            ->get(['id', 'name', 'email']); // Chỉ lấy các cột cần thiết

        return response()->json($users);
    }

    /**
     * Báo cáo một người dùng.
     */
    public function report(Request $request, User $user)
    {
        // Kiểm tra nếu người dùng báo cáo chính mình
        if (Auth::id() === $user->id) {
            Log::warning("User tried to report themselves.", ['user_id' => Auth::id()]);
            return response()->json(['success' => false, 'error' => 'Không thể báo cáo chính mình.'], 400);
        }

        // Validate dữ liệu báo cáo
        $validated = $request->validate([
            'reason' => 'required|string',
            'other_reason' => 'nullable|string|max:500' // Lý do khác có thể null nhưng nếu có thì validate
        ]);

        // Lấy lý do báo cáo, nếu là 'other' thì lấy nội dung từ other_reason
        $reason = $validated['reason'];
        if ($reason === 'other' && isset($validated['other_reason'])) {
             $reason = $validated['other_reason'];
        } elseif ($reason === 'other' && !isset($validated['other_reason'])) {
            // Nếu chọn lý do khác nhưng không nhập nội dung
            return response()->json(['success' => false, 'error' => 'Vui lòng nhập lý do báo cáo chi tiết.'], 400);
        }

        try {
            $reporter = Auth::user();
            // Gọi phương thức report từ User model
            // Phương thức report trong model cần trả về true nếu báo cáo mới được tạo, false nếu đã tồn tại
            $reported = $reporter->report($user->id, $reason);

            if ($reported) {
                 Log::info("User {$reporter->id} reported user {$user->id} for reason: {$reason}");
                 return response()->json(['success' => true, 'message' => 'Đã gửi báo cáo thành công']);
            } else {
                 // Đây là trường hợp người dùng đã báo cáo trước đó
                 Log::info("User {$reporter->id} already reported user {$user->id}.");
                 return response()->json(['success' => false, 'error' => 'Bạn đã báo cáo người dùng này trước đó.']);
            }

        } catch (\Exception $e) {
            Log::error("Error reporting user {$user->id} by user " . Auth::id() . ": " . $e->getMessage() . "\nTrace: " . $e->getTraceAsString());
            return response()->json(['success' => false, 'error' => 'Có lỗi xảy ra khi gửi báo cáo. Vui lòng thử lại.'], 500);
        }
    }

    /**
     * Chặn một người dùng.
     */
    public function block(User $user)
    {
        // Kiểm tra nếu người dùng chặn chính mình
         if (Auth::id() === $user->id) {
            return response()->json(['success' => false, 'error' => 'Không thể chặn chính mình.'], 400);
        }

        try {
            Auth::user()->block($user->id);
            Log::info("User " . Auth::id() . " blocked user " . $user->id);
            return response()->json(['success' => true, 'message' => 'Đã chặn người dùng thành công']);
        } catch (\Exception $e) {
             Log::error("Error blocking user {$user->id} by user " . Auth::id() . ": " . $e->getMessage());
             return response()->json(['success' => false, 'error' => 'Có lỗi xảy ra khi chặn người dùng.'], 500);
        }
    }

    /**
     * Bỏ chặn một người dùng.
     */
    public function unblock(User $user)
    {
        try {
            Auth::user()->unblock($user->id);
            Log::info("User " . Auth::id() . " unblocked user " . $user->id);
            return response()->json(['success' => true, 'message' => 'Đã bỏ chặn người dùng thành công']);
        } catch (\Exception $e) {
            Log::error("Error unblocking user {$user->id} by user " . Auth::id() . ": " . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Có lỗi xảy ra khi bỏ chặn người dùng.'], 500);
        }
    }

    /**
     * Hiển thị danh sách người dùng đã bị chặn bởi user hiện tại.
     */
    public function blocked()
    {
        $blockedUsers = Auth::user()->blockedUsers()->get();
        return view('profile.edit', compact('blockedUsers')); // Giả định view hiển thị danh sách chặn là profile.edit
    }


    /**
     * Gửi yêu cầu kết bạn.
     */
    public function addFriend(User $user)
    {
        $currentUser = auth()->user();

        // Kiểm tra nếu người dùng kết bạn với chính mình
        if ($currentUser->id === $user->id) {
            return response()->json([
                'success' => false,
                'error' => 'Không thể kết bạn với chính mình'
            ]);
        }

        // Kiểm tra xem đã là bạn bè chưa
        if ($currentUser->isFriendWith($user)) {
            return response()->json([
                'success' => false,
                'error' => 'Bạn đã là bạn bè với người dùng này'
            ]);
        }

        // Kiểm tra xem đã gửi lời mời kết bạn chưa
        if (DB::table('friends')->where('user_id', $currentUser->id)->where('friend_id', $user->id)->where('status', 'pending')->exists()) {
            return response()->json([
                'success' => false,
                'error' => 'Bạn đã gửi lời mời kết bạn cho người dùng này'
            ]);
        }

        try {
            // Thêm vào danh sách bạn bè
            $currentUser->friends()->attach($user->id, [
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Gửi thông báo cho người nhận
            $user->notify(new \App\Notifications\FriendRequestNotification($currentUser));

            return response()->json([
                'success' => true,
                'message' => 'Đã gửi lời mời kết bạn thành công',
                'isFriend' => false
            ]);
        } catch (\Exception $e) {
            \Log::error('Error adding friend: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Có lỗi xảy ra khi gửi lời mời kết bạn'
            ]);
        }
    }

    /**
     * Hủy kết bạn.
     */
    public function removeFriend(User $user)
    {
        // Kiểm tra nếu họ thực sự là bạn bè trước khi xóa
        if (!Auth::user()->isFriendWith($user)) {
             return response()->json(['success' => false, 'error' => 'Hai người không phải là bạn bè.'], 400);
        }

        try {
            Auth::user()->removeFriend($user); // Gọi phương thức trong User model
            return response()->json(['success' => true, 'message' => 'Đã hủy kết bạn thành công']);
        } catch (\Exception $e) {
            Log::error("Error removing friend {$user->id} by user " . Auth::id() . ": " . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Có lỗi xảy ra khi hủy kết bạn.'], 500);
        }
    }

    /**
     * Hiển thị danh sách bạn bè của một người dùng.
     */
    public function friends(User $user)
    {
        $friends = $user->friends()->get();
        return view('users.friends', compact('user', 'friends')); // Giả định view hiển thị danh sách bạn bè là users.friends
    }

    /**
     * Theo dõi một người dùng.
     */
     public function follow(User $user)
    {
        // Kiểm tra nếu người dùng theo dõi chính mình
        if (Auth::id() === $user->id) {
            return response()->json(['success' => false, 'error' => 'Không thể tự theo dõi chính mình.'], 400);
        }
         // Kiểm tra nếu đã theo dõi
         if (Auth::user()->isFollowing($user)) {
             return response()->json(['success' => false, 'error' => 'Bạn đã theo dõi người dùng này.'], 400);
         }

        try {
            Auth::user()->follow($user); // Gọi phương thức trong User model
            return response()->json(['success' => true, 'message' => 'Đã theo dõi người dùng']);
        } catch (\Exception $e) {
            Log::error("Error following user {$user->id} by user " . Auth::id() . ": " . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Có lỗi xảy ra khi theo dõi người dùng.'], 500);
        }
    }

    /**
     * Bỏ theo dõi một người dùng.
     */
    public function unfollow(User $user)
    {
        // Kiểm tra nếu đang theo dõi
         if (!Auth::user()->isFollowing($user)) {
             return response()->json(['success' => false, 'error' => 'Bạn không theo dõi người dùng này.'], 400);
         }
        try {
            Auth::user()->unfollow($user); // Gọi phương thức trong User model
            return response()->json(['success' => true, 'message' => 'Đã bỏ theo dõi người dùng']);
        } catch (\Exception $e) {
             Log::error("Error unfollowing user {$user->id} by user " . Auth::id() . ": " . $e->getMessage());
             return response()->json(['success' => false, 'error' => 'Có lỗi xảy ra khi bỏ theo dõi người dùng.'], 500);
        }
    }

    /**
     * Chấp nhận lời mời kết bạn
     */
    public function acceptFriendRequest(User $user)
    {
        $currentUser = auth()->user();
        // Cập nhật trạng thái kết bạn (chiều nhận)
        $currentUser->pendingFriendRequests()->updateExistingPivot($user->id, ['status' => 'accepted']);
        // Đảm bảo cả 2 chiều là bạn bè
        $user->friends()->syncWithoutDetaching([$currentUser->id => ['status' => 'accepted']]);
        return response()->json(['success' => true, 'message' => 'Đã chấp nhận lời mời kết bạn!']);
    }

    /**
     * Từ chối lời mời kết bạn
     */
    public function rejectFriendRequest(User $user)
    {
        $currentUser = auth()->user();
        // Xóa lời mời kết bạn (chiều nhận)
        $currentUser->pendingFriendRequests()->detach($user->id);
        return response()->json(['success' => true, 'message' => 'Đã từ chối lời mời kết bạn.']);
    }
}