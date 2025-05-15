<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostFavorite;
use Illuminate\Http\Request;

class PostFavoriteController extends Controller
{
    public function toggle(Post $post)
    {
        $user = auth()->user();
        
        if ($post->isFavoritedBy($user->id)) {
            // Nếu đã yêu thích thì bỏ yêu thích
            PostFavorite::where('user_id', $user->id)
                ->where('post_id', $post->id)
                ->delete();
            $message = 'Đã bỏ khỏi danh sách yêu thích';
            $isFavorited = false;
        } else {
            // Nếu chưa yêu thích thì thêm vào yêu thích
            PostFavorite::create([
                'user_id' => $user->id,
                'post_id' => $post->id
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