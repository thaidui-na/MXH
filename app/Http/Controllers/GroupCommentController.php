<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GroupComment;
use App\Models\GroupPost;

class GroupCommentController extends Controller
{
    public function store(Request $request, GroupPost $post)
    {
        $comment = $post->comments()->create([
            'user_id' => auth()->id(),
            'content' => $request->content
        ]);

        return response()->json([
            'success' => true,
            'comment' => $comment->load('user')
        ]);
    }
}
