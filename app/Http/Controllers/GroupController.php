<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\GroupPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GroupController extends Controller
{
    public function index(Request $request)
    {
        $query = Group::withCount('members');
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($sub) use ($q) {
                $sub->where('name', 'like', "%$q%")
                     ->orWhere('description', 'like', "%$q%") ;
            });
        }
        $groups = $query->with(['members'])->latest()->paginate(10);
        return view('groups.index', compact('groups'));
    }

    public function create()
    {
        return view('groups.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|max:2048',
            'avatar' => 'nullable|image|max:2048',
            'is_private' => 'boolean'
        ]);

        $data = $request->only(['name', 'description', 'is_private']);
        $data['created_by'] = auth()->id();

        // Upload cover image
        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('group-covers', 'public');
        }

        // Upload avatar
        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('group-avatars', 'public');
        }

        $group = Group::create($data);

        // Tự động thêm người tạo làm admin
        GroupMember::create([
            'group_id' => $group->id,
            'user_id' => auth()->id(),
            'role' => 'admin',
            'is_approved' => true
        ]);

        return redirect()->route('groups.show', $group)
            ->with('success', 'Tạo nhóm thành công!');
    }

    public function show(Group $group)
    {
        $group->load(['members.user', 'posts.user']);
        $posts = $group->posts()->with('user')->latest()->paginate(10);
        return view('groups.show', compact('group', 'posts'));
    }

    public function edit(Group $group)
    {
        if (!$group->hasAdmin(auth()->id())) {
            return redirect()->route('groups.show', $group)
                ->with('error', 'Bạn không có quyền chỉnh sửa nhóm này!');
        }
        return view('groups.edit', compact('group'));
    }

    public function update(Request $request, Group $group)
    {
        if (!$group->hasAdmin(auth()->id())) {
            return redirect()->route('groups.show', $group)
                ->with('error', 'Bạn không có quyền chỉnh sửa nhóm này!');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|max:2048',
            'avatar' => 'nullable|image|max:2048',
            'is_private' => 'boolean'
        ]);

        $data = $request->only(['name', 'description', 'is_private']);

        // Upload cover image
        if ($request->hasFile('cover_image')) {
            if ($group->cover_image) {
                Storage::disk('public')->delete($group->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('group-covers', 'public');
        }

        // Upload avatar
        if ($request->hasFile('avatar')) {
            if ($group->avatar) {
                Storage::disk('public')->delete($group->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('group-avatars', 'public');
        }

        $group->update($data);

        return redirect()->route('groups.show', $group)
            ->with('success', 'Cập nhật nhóm thành công!');
    }

    public function destroy(Group $group)
    {
        if (!$group->hasAdmin(auth()->id())) {
            return redirect()->route('groups.show', $group)
                ->with('error', 'Bạn không có quyền xóa nhóm này!');
        }

        // Xóa ảnh
        if ($group->cover_image) {
            Storage::disk('public')->delete($group->cover_image);
        }
        if ($group->avatar) {
            Storage::disk('public')->delete($group->avatar);
        }

        $group->delete();

        return redirect()->route('groups.index')
            ->with('success', 'Xóa nhóm thành công!');
    }

    public function join(Group $group)
    {
        if ($group->hasMember(auth()->id())) {
            return redirect()->route('groups.show', $group)
                ->with('error', 'Bạn đã là thành viên của nhóm này!');
        }

        GroupMember::create([
            'group_id' => $group->id,
            'user_id' => auth()->id(),
            'role' => 'member',
            'is_approved' => !$group->is_private
        ]);

        return redirect()->route('groups.show', $group)
            ->with('success', $group->is_private ? 'Đã gửi yêu cầu tham gia nhóm!' : 'Tham gia nhóm thành công!');
    }

    public function leave(Group $group)
    {
        if (!$group->hasMember(auth()->id())) {
            return redirect()->route('groups.show', $group)
                ->with('error', 'Bạn không phải thành viên của nhóm này!');
        }

        if ($group->hasAdmin(auth()->id()) && $group->members()->where('role', 'admin')->count() === 1) {
            return redirect()->route('groups.show', $group)
                ->with('error', 'Bạn là admin duy nhất của nhóm. Hãy chỉ định admin khác trước khi rời nhóm!');
        }

        $group->members()->where('user_id', auth()->id())->delete();

        return redirect()->route('groups.index')
            ->with('success', 'Rời nhóm thành công!');
    }

    public function members(Group $group)
    {
        $members = $group->members()->with('user')->paginate(20);
        return view('groups.members', compact('group', 'members'));
    }

    public function updateMember(Request $request, Group $group, GroupMember $member)
    {
        if (!$group->hasAdmin(auth()->id())) {
            return redirect()->route('groups.members', $group)
                ->with('error', 'Bạn không có quyền quản lý thành viên!');
        }

        $request->validate([
            'role' => 'required|in:admin,moderator,member',
            'is_approved' => 'boolean'
        ]);

        $member->update($request->only(['role', 'is_approved']));

        return redirect()->route('groups.members', $group)
            ->with('success', 'Cập nhật thành viên thành công!');
    }

    public function removeMember(Group $group, GroupMember $member)
    {
        if (!$group->hasAdmin(auth()->id())) {
            return redirect()->route('groups.members', $group)
                ->with('error', 'Bạn không có quyền quản lý thành viên!');
        }

        if ($member->isAdmin() && $group->members()->where('role', 'admin')->count() === 1) {
            return redirect()->route('groups.members', $group)
                ->with('error', 'Không thể xóa admin duy nhất của nhóm!');
        }

        $member->delete();

        return redirect()->route('groups.members', $group)
            ->with('success', 'Xóa thành viên thành công!');
    }

    public function addMembers(Request $request, Group $group)
    {
        if (!$group->hasAdmin(auth()->id())) {
            return redirect()->route('groups.members', $group)
                ->with('error', 'Bạn không có quyền thêm thành viên vào nhóm!');
        }

        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ], [
            'user_ids.required' => 'Vui lòng chọn thành viên để thêm vào nhóm',
            'user_ids.array' => 'Dữ liệu không hợp lệ',
            'user_ids.*.exists' => 'Một số thành viên được chọn không tồn tại'
        ]);

        $userIds = $request->user_ids;
        $existingMembers = $group->members()->whereIn('user_id', $userIds)->pluck('user_id')->toArray();
        $newMembers = array_diff($userIds, $existingMembers);

        if (empty($newMembers)) {
            return redirect()->route('groups.members', $group)
                ->with('error', 'Những thành viên được chọn đã là thành viên của nhóm!');
        }

        foreach ($newMembers as $userId) {
            GroupMember::create([
                'group_id' => $group->id,
                'user_id' => $userId,
                'role' => 'member',
                'is_approved' => !$group->is_private
            ]);
        }

        $count = count($newMembers);
        return redirect()->route('groups.members', $group)
            ->with('success', "Đã thêm thành công $count thành viên mới vào nhóm!");
    }

    public function post(Request $request, Group $group)
    {
        if (!$group->hasMember(auth()->id())) {
            return redirect()->route('groups.show', $group)
                ->with('error', 'Bạn cần là thành viên của nhóm để đăng bài!');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|max:2048'
        ]);

        $data = $request->only(['title', 'content']);
        $data['user_id'] = auth()->id();
        $data['group_id'] = $group->id;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('group-posts', 'public');
        }

        GroupPost::create($data);

        return redirect()->route('groups.show', $group)
            ->with('success', 'Đăng bài thành công!');
    }

    /**
     * API tìm kiếm nhóm cho autocomplete
     */
    public function searchAjax(Request $request)
    {
        $q = $request->q;
        $groups = Group::query()
            ->when($q, function($query) use ($q) {
                $query->where(function($sub) use ($q) {
                    $sub->where('name', 'like', "%$q%")
                         ->orWhere('description', 'like', "%$q%") ;
                });
            })
            ->limit(10)
            ->get(['id', 'name', 'cover_image', 'avatar']);
        return response()->json($groups);
    }

   
} 