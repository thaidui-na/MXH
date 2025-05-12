<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupPost;
use Illuminate\Http\Request;

class GroupPostLikeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function toggleLike(Group $group, GroupPost $groupPost)
    {
        // Kiểm tra user có phải thành viên nhóm không
        if (!$group->members()->where('user_id', auth()->id())->exists()) {
            return response()->json(['error' => 'Bạn không phải thành viên của nhóm này'], 403);
        }

        $user = auth()->user();
        $like = $groupPost->likes()->where('user_id', $user->id)->first();

        if ($like) {
            $like->delete();
            $isLiked = false;
        } else {
            $groupPost->likes()->create(['user_id' => $user->id]);
            $isLiked = true;
        }

        $likeCount = $groupPost->likes()->count();

        return response()->json([
            'success' => true,
            'isLiked' => $isLiked,
            'likeCount' => $likeCount
        ]);
    }
} 