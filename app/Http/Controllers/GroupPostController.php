<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GroupPostController extends Controller
{
    public function store(Request $request, Group $group)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'title' => 'nullable|string|max:255',
        ]);

        $post = new Post();
        $post->group_id = $group->id;
        $post->user_id = auth()->id();
        $post->content = $request->content;
        $post->title = $request->title ?? '';
        $post->save();
        

        return redirect()->back()->with('success', 'Đăng bài thành công!');
    }
}
