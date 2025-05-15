<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    // Hàm searchAjax - dùng cho Ajax (JSON response)
    public function searchAjax(Request $request)
    {
        $query = $request->input('query');
        
        $users = User::query()
            ->when($query, function($q) use ($query) {
                $q->where(function($sub) use ($query) {
                    $sub->where('name', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%");
                });
            })
            ->where('id', '!=', auth()->id())
            ->excludeBlocked()
            ->select(['id', 'name', 'email', 'avatar'])
            ->limit(10)
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar,
                    'isFriend' => auth()->user()->isFollowing($user),
                    'isBlocked' => auth()->user()->hasBlocked($user->id),
                    'isReported' => auth()->user()->hasReported($user->id)
                ];
            });

        return response()->json([
            'users' => $users
        ]);
    }

    // Hàm search - dùng để render ra view với Log ghi lại
    public function search(Request $request)
    {
        $query = $request->input('q');
        Log::info('Searching users with query: ' . $query);
        
        $users = User::when($query, function($q) use ($query) {
            $q->where(function($sub) use ($query) {
                $sub->where('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%");
            });
        })
        ->latest()
        ->paginate(10);
        
        Log::info('Found ' . $users->count() . ' users');
        
        return view('users.search', [
            'users' => $users,
            'query' => $query
        ]);
    }

    public function addFriend(User $user)
    {
        if (auth()->id() === $user->id) {
            return response()->json(['error' => 'Bạn không thể kết bạn với chính mình'], 400);
        }

        $isFriend = auth()->user()->isFollowing($user);
        
        if ($isFriend) {
            return response()->json(['error' => 'Bạn đã là bạn bè với người dùng này rồi'], 400);
        }

        auth()->user()->following()->attach($user->id);

        return response()->json([
            'success' => true,
            'message' => 'Đã gửi lời mời kết bạn thành công',
            'isFriend' => true
        ]);
    }

    public function removeFriend(User $user)
    {
        if (auth()->id() === $user->id) {
            return response()->json(['error' => 'Bạn không thể hủy kết bạn với chính mình'], 400);
        }

        $isFriend = auth()->user()->isFollowing($user);
        
        if (!$isFriend) {
            return response()->json(['error' => 'Bạn chưa là bạn bè với người dùng này'], 400);
        }

        auth()->user()->following()->detach($user->id);

        return response()->json([
            'success' => true,
            'message' => 'Đã hủy kết bạn thành công',
            'isFriend' => false
        ]);
    }

    public function followers(User $user)
    {
        $followers = $user->followers()->paginate(20);
        return view('users.followers', compact('user', 'followers'));
    }

    public function following(User $user)
    {
        $following = $user->following()->paginate(20);
        return view('users.following', compact('user', 'following'));
    }

    /**
     * Hiển thị danh sách người dùng đã bị chặn
     */
    public function blocked()
    {
        $blockedUsers = auth()->user()->blockedUsers()->paginate(20);
        return view('users.blocked', compact('blockedUsers'));
    }

    /**
     * Chặn một người dùng
     */
    public function block(User $user)
    {
        if (auth()->id() === $user->id) {
            return response()->json(['error' => 'Bạn không thể chặn chính mình'], 400);
        }

        auth()->user()->block($user->id);

        return response()->json([
            'success' => true,
            'message' => 'Đã chặn người dùng thành công'
        ]);
    }

    /**
     * Bỏ chặn một người dùng
     */
    public function unblock(User $user)
    {
        if (auth()->id() === $user->id) {
            return response()->json(['error' => 'Bạn không thể bỏ chặn chính mình'], 400);
        }

        auth()->user()->unblock($user->id);

        return response()->json([
            'success' => true,
            'message' => 'Đã bỏ chặn người dùng thành công'
        ]);
    }

    /**
     * Báo cáo một người dùng
     */
    public function report(Request $request, User $user)
    {
        if (auth()->id() === $user->id) {
            return response()->json(['error' => 'Bạn không thể báo cáo chính mình'], 400);
        }

        $request->validate([
            'reason' => 'required|string|max:1000'
        ]);

        auth()->user()->report($user->id, $request->reason);

        return response()->json([
            'success' => true,
            'message' => 'Đã báo cáo người dùng thành công'
        ]);
    }

    /**
     * Hủy báo cáo một người dùng
     */
    public function unreport(User $user)
    {
        if (auth()->id() === $user->id) {
            return response()->json(['error' => 'Bạn không thể hủy báo cáo chính mình'], 400);
        }

        auth()->user()->unreport($user->id);

        return response()->json([
            'success' => true,
            'message' => 'Đã hủy báo cáo người dùng thành công'
        ]);
    }

    public function follow(User $user)
    {
        $follower = auth()->user();
        
        if ($follower->isFollowing($user)) {
            $follower->unfollow($user);
            return response()->json(['following' => false]);
        }
        
        $follower->follow($user);
        return response()->json(['following' => true]);
    }
}
