<?php
namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function toggle(Post $post)
    {
        $user = auth()->user();
    
        $like = $post->likes()->where('user_id', $user->id)->first();
    
        if ($like) {
            $like->delete();
        } else {
            $post->likes()->create(['user_id' => $user->id]);
        }
    
        // ❌ Đừng return response()->json(...) nữa
        // ✅ Thay bằng:
        return back(); // hoặc redirect()->route('posts.index'); tùy bạn
    }
    
}
