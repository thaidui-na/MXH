<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function follow(User $user)
    {
        if (auth()->id() === $user->id) {
            return response()->json(['message' => 'Bạn không thể theo dõi chính mình'], 422);
        }

        if (!auth()->user()->isFollowing($user)) {
            auth()->user()->following()->attach($user->id);
            return response()->json(['message' => 'Đã theo dõi người dùng thành công', 'following' => true]);
        }

        return response()->json(['message' => 'Bạn đã theo dõi người dùng này rồi'], 422);
    }

    public function unfollow(User $user)
    {
        if (auth()->id() === $user->id) {
            return response()->json(['message' => 'Bạn không thể hủy theo dõi chính mình'], 422);
        }

        if (auth()->user()->isFollowing($user)) {
            auth()->user()->following()->detach($user->id);
            return response()->json(['message' => 'Đã hủy theo dõi người dùng thành công', 'following' => false]);
        }

        return response()->json(['message' => 'Bạn chưa theo dõi người dùng này'], 422);
    }
}
