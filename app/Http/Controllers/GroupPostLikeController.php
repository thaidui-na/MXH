<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupPost;
use App\Models\GroupPostLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class GroupPostLikeController extends Controller
{
    public function toggleLike(Group $group, GroupPost $groupPost)
    {
        try {
            // Log chi tiết về request và các tham số
            Log::info('Toggle like request details', [
                'group' => $group->toArray(),
                'post' => $groupPost->toArray(),
                'user' => auth()->user()->toArray(),
                'request_url' => request()->url(),
                'request_method' => request()->method()
            ]);

            // Kiểm tra user có phải thành viên nhóm không
            if (!$group->members()->where('user_id', auth()->id())->exists()) {
                Log::warning('User is not a member of the group', [
                    'user_id' => auth()->id(),
                    'group_id' => $group->id
                ]);
                return response()->json(['error' => 'Bạn không phải thành viên của nhóm này'], 403);
            }

            // Kiểm tra bài viết có thuộc về nhóm không
            if ($groupPost->group_id !== $group->id) {
                Log::warning('Post does not belong to the group', [
                    'post_group_id' => $groupPost->group_id,
                    'requested_group_id' => $group->id
                ]);
                return response()->json(['error' => 'Bài viết không thuộc về nhóm này'], 400);
            }

            $user = auth()->user();
            $like = GroupPostLike::where('user_id', $user->id)
                                ->where('group_post_id', $groupPost->id)
                                ->first();

            if ($like) {
                Log::info('Deleting existing like', ['like_id' => $like->id]);
                $like->delete();
                $isLiked = false;
            } else {
                Log::info('Creating new like');
                GroupPostLike::create([
                    'user_id' => $user->id,
                    'group_post_id' => $groupPost->id
                ]);
                $isLiked = true;
            }

            $likeCount = GroupPostLike::where('group_post_id', $groupPost->id)->count();
            Log::info('Like operation completed', [
                'is_liked' => $isLiked,
                'like_count' => $likeCount
            ]);

            return response()->json([
                'success' => true,
                'isLiked' => $isLiked,
                'likeCount' => $likeCount
            ]);

        } catch (\Exception $e) {
            Log::error('Error in toggleLike', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'group_id' => $group->id ?? null,
                'post_id' => $groupPost->id ?? null
            ]);

            return response()->json([
                'error' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
} 