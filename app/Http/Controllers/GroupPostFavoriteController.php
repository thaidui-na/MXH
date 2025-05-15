<?php

namespace App\Http\Controllers;

use App\Models\GroupPost;
use App\Models\GroupPostFavorite;
use Illuminate\Http\Request;

class GroupPostFavoriteController extends Controller
{
    public function toggle(GroupPost $post)
    {
        $user = auth()->user();
        
        if ($post->isFavoritedBy($user->id)) {
            // Nếu đã yêu thích thì bỏ yêu thích
            GroupPostFavorite::where('user_id', $user->id)
                ->where('group_post_id', $post->id)
                ->delete();
            $message = 'Đã bỏ khỏi danh sách yêu thích';
            $isFavorited = false;
        } else {
            // Nếu chưa yêu thích thì thêm vào yêu thích
            GroupPostFavorite::create([
                'user_id' => $user->id,
                'group_post_id' => $post->id
            ]);
            $message = 'Đã thêm vào danh sách yêu thích';
            $isFavorited = true;
        }

        if (request()->ajax()) {
            return response()->json([
                'message' => $message,
                'isFavorited' => $isFavorited
            ]);
        }

        return back()->with('success', $message);
    }
}