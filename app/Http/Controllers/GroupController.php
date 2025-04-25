<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GroupController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'privacy' => 'required|in:public,private',
            'image' => 'nullable|image|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('group_images', 'public');
        }

        Group::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'description' => $request->description,
            'privacy' => $request->privacy,
            'image' => $imagePath,
        ]);

        return back()->with('success', 'Tạo nhóm thành công!');
    }
    public function index()
{
    $groups = Group::latest()->paginate(10);
    return view('groups.index', compact('groups'));
}
public function show(Group $group)
{
    $posts = $group->posts()->latest()->paginate(5);
    return view('groups.show', compact('group', 'posts'));
}
public function myGroups()
{
    $groups = Group::where('user_id', auth()->id())->latest()->paginate(6);
    return view('groups.my', compact('groups'));
}


}

