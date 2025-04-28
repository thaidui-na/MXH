<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Auth::user()->myGroups()->with('members')->get();
        return view('groups.index', compact('groups'));
    }

    public function create()
    {
        return view('groups.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|max:255',
        'description' => 'nullable',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
        'privacy' => 'required|in:public,private',
    ]);

    $data = $request->only('name', 'description', 'privacy');
    $data['user_id'] = Auth::id();

    if ($request->hasFile('image')) {
        $imageName = time().'_'.Str::slug($request->name).'.'.$request->image->extension();
        $request->image->move(public_path('uploads/groups'), $imageName);
        $data['image'] = 'uploads/groups/'.$imageName;
    }

    $group = Group::create($data);

    // Thêm creator là admin
    $group->members()->attach(Auth::id(), ['role' => 'admin']);

    // Thêm user mẫu vào group
    $sampleUserIds = [2, 3, 4]; // User mẫu

    foreach ($sampleUserIds as $userId) {
        $group->members()->attach($userId, ['role' => 'member']);
    }

    // Tạo bài đăng mẫu
    foreach ($sampleUserIds as $userId) {
        \App\Models\GroupPost::create([
            'group_id' => $group->id,
            'user_id' => $userId,
            'content' => fake()->sentence(), // Nội dung ngẫu nhiên
        ]);
    }

    return redirect()->route('groups.index')->with('success', 'Tạo nhóm thành công!');
}


    public function show(Group $group)
    {
        $group->load('members', 'posts');
        return view('groups.show', compact('group'));
    }
}