<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GroupComment;

class GroupCommentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'group_post_id' => 'required|exists:group_posts,id',
            'content' => 'required|string|max:1000',
        ]);

        GroupComment::create([
            'group_post_id' => $request->group_post_id,
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);

        return back();
    }
}
