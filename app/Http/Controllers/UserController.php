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
            ->select(['id', 'name', 'email', 'avatar'])
            ->limit(10)
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar,
                    'isFriend' => auth()->user()->isFollowing($user)
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
}
