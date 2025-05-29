<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ReadPost;

class ReadPostController extends Controller
{
    public function markAsRead(Request $request, $postId)
    {
        $user = $request->user();
        $readPost = ReadPost::firstOrCreate(
            [
                'user_id' => $user->id,
                'post_id' => $postId
            ],
            [
                'read_at' => now()
            ]
        );
        return response()->json(['success' => true, 'message' => 'Đã đánh dấu là đã đọc']);
    }
}
