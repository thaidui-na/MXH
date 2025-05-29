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
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[^\s　]*[^\s　]+[^\s　]*$/', // Không cho phép toàn khoảng trắng
                'unique:groups,name', // Kiểm tra tên nhóm trùng
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000',
                'regex:/^[^\s　]*[^\s　]+[^\s　]*$/', // Không cho phép toàn khoảng trắng
            ],
            'cover_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_private' => 'boolean'
        ], [
            'name.required' => 'Vui lòng nhập tên nhóm',
            'name.string' => 'Tên nhóm phải là chuỗi ký tự',
            'name.max' => 'Tên nhóm không được vượt quá 255 ký tự',
            'name.regex' => 'Tên nhóm không được chứa toàn khoảng trắng',
            'name.unique' => 'Tên nhóm này đã tồn tại. Vui lòng chọn tên khác',
            'description.max' => 'Mô tả không được vượt quá 1000 ký tự',
            'description.regex' => 'Mô tả không được chứa toàn khoảng trắng',
            'cover_image.required' => 'Vui lòng chọn ảnh bìa cho nhóm',
            'cover_image.image' => 'File ảnh bìa không đúng định dạng',
            'cover_image.mimes' => 'Ảnh bìa phải có định dạng: jpeg, png, jpg, gif',
            'cover_image.max' => 'Ảnh bìa không được vượt quá 2MB',
            'avatar.required' => 'Vui lòng chọn ảnh đại diện cho nhóm',
            'avatar.image' => 'File ảnh đại diện không đúng định dạng',
            'avatar.mimes' => 'Ảnh đại diện phải có định dạng: jpeg, png, jpg, gif',
            'avatar.max' => 'Ảnh đại diện không được vượt quá 2MB',
        ]);

        try {
            $data = $request->only(['name', 'description', 'is_private']);
            $data['created_by'] = auth()->id();

            // Upload cover image
            $data['cover_image'] = $request->file('cover_image')->store('group-covers', 'public');

            // Upload avatar
            $data['avatar'] = $request->file('avatar')->store('group-avatars', 'public');

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
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi tạo nhóm. Vui lòng thử lại sau.');
        }
    }

    public function show(Group $group)
    {
        $group->load(['members.user']);
        
        // Lấy bài viết và sắp xếp theo số lượt yêu thích
        $posts = $group->posts()
            ->with(['user', 'likes'])
            ->orderByFavorites()
            ->get();
            
        return view('groups.show', compact('group', 'posts'));
    }

    public function edit(Group $group)
    {
        if ($group->created_by !== auth()->id() && !$group->hasAdmin(auth()->id())) {
            return redirect()->route('groups.show', $group)
                ->with('error', 'Bạn không có quyền chỉnh sửa nhóm này!');
        }
        return view('groups.edit', compact('group'));
    }

    public function update(Request $request, Group $group)
    {
        if ($group->created_by !== auth()->id() && !$group->hasAdmin(auth()->id())) {
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
        if ($group->created_by !== auth()->id() && !$group->hasAdmin(auth()->id())) {
            return redirect()->route('groups.show', $group)
                ->with('error', 'Bạn không có quyền xóa nhóm này!');
        }

        try {
            // Kiểm tra lại xem nhóm có tồn tại không
            $group = Group::find($group->id);
            if (!$group) {
                return redirect()->route('groups.index')
                    ->with('error', 'Nhóm này đã bị xóa hoặc không tồn tại!');
            }

            // Xóa ảnh
            if ($group->cover_image) {
                Storage::disk('public')->delete($group->cover_image);
            }
            if ($group->avatar) {
                Storage::disk('public')->delete($group->avatar);
            }

            // Xóa tất cả bài viết và ảnh của bài viết
            foreach ($group->posts as $post) {
                if ($post->image) {
                    Storage::disk('public')->delete($post->image);
                }
                $post->delete();
            }

            // Xóa tất cả thành viên
            $group->members()->delete();

            // Xóa nhóm
            $group->delete();

            return redirect()->route('groups.index')
                ->with('success', 'Xóa nhóm thành công!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi xóa nhóm. Vui lòng thử lại sau.');
        }
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

        try {
            // Kiểm tra nếu là admin duy nhất
            if ($group->hasAdmin(auth()->id()) && $group->members()->where('role', 'admin')->count() === 1) {
                return redirect()->route('groups.show', $group)
                    ->with('error', 'Bạn là admin duy nhất của nhóm. Hãy chỉ định admin khác trước khi rời nhóm!');
            }

            // Xóa thành viên
            $group->members()->where('user_id', auth()->id())->delete();

            return redirect()->route('groups.index')
                ->with('success', 'Rời nhóm thành công!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi rời nhóm. Vui lòng thử lại sau.');
        }
    }

    public function members(Group $group)
    {
        // Kiểm tra quyền: người tạo hoặc admin mới được xem trang quản lý thành viên
        if ($group->created_by !== auth()->id() && !$group->hasAdmin(auth()->id())) {
            return redirect()->route('groups.show', $group)
                ->with('error', 'Bạn không có quyền xem trang quản lý thành viên nhóm này!');
        }
        $members = $group->members()->with('user')->paginate(20);
        return view('groups.members', compact('group', 'members'));
    }

    public function updateMember(Request $request, Group $group, GroupMember $member)
    {
        // Kiểm tra quyền: người tạo hoặc admin mới được cập nhật vai trò thành viên
        if ($group->created_by !== auth()->id() && !$group->hasAdmin(auth()->id())) {
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
        // Kiểm tra quyền: người tạo hoặc admin mới được xóa thành viên
         if ($group->created_by !== auth()->id() && !$group->hasAdmin(auth()->id())) {
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
        // Kiểm tra quyền: người tạo hoặc admin mới được thêm thành viên
        if ($group->created_by !== auth()->id() && !$group->hasAdmin(auth()->id())) {
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
                ->with('error', 'Bạn không phải là thành viên của nhóm này.');
        }

        $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
                'regex:/^[^\s　]*[^\s　]+[^\s　]*$/', // Tiêu đề: không cho phép toàn khoảng trắng
            ],
            'content' => [
                'required',
                'string',
                'max:1000', // Giới hạn nội dung tối đa 1000 ký tự
            ],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Tùy chọn upload ảnh
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề bài viết.',
            'title.string' => 'Tiêu đề phải là chuỗi ký tự.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            'title.regex' => 'Tiêu đề không được chỉ chứa toàn khoảng trắng.',
            'content.required' => 'Vui lòng nhập nội dung bài viết.',
            'content.string' => 'Nội dung bài viết phải là chuỗi ký tự.',
            'content.max' => 'Nội dung bài viết không được vượt quá 1000 ký tự.',
            'image.image' => 'File upload không phải là ảnh hợp lệ.',
            'image.mimes' => 'Ảnh chỉ chấp nhận định dạng: jpeg, png, jpg, gif.',
            'image.max' => 'Kích thước ảnh không được vượt quá 2MB.',
        ]);

        try {
            $postData = [
                'group_id' => $group->id,
                'user_id' => auth()->id(),
                'title' => $request->title,
                'content' => $request->content,
            ];

            if ($request->hasFile('image')) {
                $postData['image'] = $request->file('image')->store('group-post-images', 'public');
            }

            $groupPost = GroupPost::create($postData);

            return redirect()->route('groups.show', $group)
                ->with('success', 'Bài viết đã được đăng thành công trong nhóm.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi đăng bài viết. Vui lòng thử lại sau.');
        }
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