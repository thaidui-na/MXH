<?php

namespace App\Http\Controllers;

use App\Models\GroupPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupPostLikeController extends Controller
{
    public function toggleLike(Request $request, $groupId, GroupPost $groupPost)
    {
        $user = Auth::user();
        // Chỉ cho thành viên nhóm mới được like
        if (!$groupPost->group->members->contains('user_id', $user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn phải là thành viên nhóm để like bài viết.'
            ], 403);
        }

        $existingLike = $groupPost->likes()->where('user_id', $user->id)->first();
        if ($existingLike) {
            $existingLike->delete();
            $isLiked = false;
            $message = 'Đã bỏ thích bài viết';
        } else {
            $groupPost->likes()->create(['user_id' => $user->id]);
            $isLiked = true;
            $message = 'Đã thích bài viết';
        }

        return response()->json([
            'success' => true,
            'isLiked' => $isLiked,
            'likesCount' => $groupPost->likes()->count(),
            'message' => $message
        ]);
    }
}
