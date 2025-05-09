<?php

namespace App\Http\Controllers;

use App\Models\ChatGroup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ChatGroupController extends Controller
{
    /**
     * Hiển thị danh sách nhóm chat
     */
    public function index()
    {
        // Lấy danh sách nhóm chat của user hiện tại với các relationship
        $groups = auth()->user()->chatGroups()
            ->with(['members', 'messages' => function($query) {
                $query->latest()->take(1)->with('sender');
            }])
            ->get();
        
        return view('chat_groups.index', compact('groups'));
    }

    /**
     * Hiển thị form tạo nhóm mới
     */
    public function create()
    {
        // Lấy danh sách user để chọn thành viên
        $users = User::where('id', '!=', auth()->id())->get();
        return view('chat_groups.create', compact('users'));
    }

    /**
     * Lưu nhóm chat mới
     */
    public function store(Request $request)
    {
        // Validate dữ liệu
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'members' => 'required|array|min:2',
            'members.*' => 'exists:users,id',
            'avatar' => 'nullable|image|max:2048'
        ]);

        DB::beginTransaction();
        try {
            // Upload avatar nếu có
            $avatarPath = null;
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('group-avatars', 'public');
            }

            // Tạo nhóm mới
            $group = ChatGroup::create([
                'name' => $request->name,
                'description' => $request->description,
                'created_by' => auth()->id(),
                'avatar' => $avatarPath
            ]);

            // Tạo mảng thành viên bao gồm cả người tạo
            $memberData = collect($request->members)
                ->map(function($memberId) {
                    return [
                        'user_id' => $memberId,
                        'is_admin' => false,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                })
                ->push([
                    'user_id' => auth()->id(),
                    'is_admin' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

            // Thêm tất cả thành viên vào nhóm
            $group->members()->attach($memberData->pluck('user_id')->toArray());

            // Cập nhật quyền admin cho người tạo
            DB::table('chat_group_members')
                ->where('group_id', $group->id)
                ->where('user_id', auth()->id())
                ->update(['is_admin' => true]);

            DB::commit();

            \Log::info('Tạo nhóm chat thành công:', [
                'group_id' => $group->id,
                'creator_id' => auth()->id(),
                'members' => $memberData->pluck('user_id')
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'groupHtml' => view('messages.partials.group-item', compact('group'))->render()
                ]);
            }

            return redirect()->route('chat-groups.show', $group)
                ->with('success', 'Tạo nhóm chat thành công!');

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Lỗi tạo nhóm chat: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Có lỗi xảy ra khi tạo nhóm chat'
                ]);
            }

            return back()->with('error', 'Có lỗi xảy ra khi tạo nhóm chat');
        }
    }

    /**
     * Hiển thị nhóm chat
     */
    public function show($id)
    {
        // Lấy thông tin nhóm
        $group = ChatGroup::findOrFail($id);
        
        // Debug log
        \Log::info('Đang truy cập nhóm chat:', [
            'group_id' => $id,
            'user_id' => auth()->id(),
            'is_member' => DB::table('chat_group_members')->where([
                'group_id' => $id,
                'user_id' => auth()->id()
            ])->exists()
        ]);

        // Kiểm tra quyền truy cập
        $isMember = DB::table('chat_group_members')
            ->where([
                'group_id' => $id,
                'user_id' => auth()->id()
            ])
            ->exists();

        if (!$isMember) {
            \Log::warning('Người dùng không có quyền truy cập:', [
                'user_id' => auth()->id(),
                'group_id' => $id
            ]);
            abort(403, 'Bạn không có quyền truy cập nhóm này');
        }

        // Load messages với sender
        $messages = $group->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();
        
        // Load thông tin nhóm với members và creator
        $group->load(['members', 'creator']);

        return view('chat_groups.show', compact('group', 'messages'));
    }

    /**
     * Hiển thị form chỉnh sửa nhóm
     */
    public function edit(ChatGroup $group)
    {
        // Kiểm tra quyền admin
        if (!$group->members()->where('user_id', auth()->id())->where('is_admin', true)->exists()) {
            abort(403, 'Bạn không có quyền chỉnh sửa nhóm này');
        }

        $users = User::where('id', '!=', auth()->id())->get();
        return view('chat_groups.edit', compact('group', 'users'));
    }

    /**
     * Cập nhật thông tin nhóm
     */
    public function update(Request $request, ChatGroup $group)
    {
        // Kiểm tra quyền admin
        if (!$group->members()->where('user_id', auth()->id())->where('is_admin', true)->exists()) {
            abort(403, 'Bạn không có quyền chỉnh sửa nhóm này');
        }

        // Validate dữ liệu
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'members' => 'required|array|min:2',
            'members.*' => 'exists:users,id',
            'avatar' => 'nullable|image|max:2048'
        ]);

        // Cập nhật avatar nếu có
        if ($request->hasFile('avatar')) {
            // Xóa avatar cũ
            if ($group->avatar) {
                Storage::disk('public')->delete($group->avatar);
            }
            $avatarPath = $request->file('avatar')->store('group-avatars', 'public');
            $group->avatar = $avatarPath;
        }

        // Cập nhật thông tin nhóm
        $group->update([
            'name' => $request->name,
            'description' => $request->description
        ]);

        // Cập nhật danh sách thành viên
        $currentMembers = $group->members()->where('user_id', '!=', auth()->id())->pluck('user_id');
        
        // Xóa thành viên không còn trong danh sách
        $removedMembers = $currentMembers->diff($request->members);
        $group->members()->detach($removedMembers);

        // Thêm thành viên mới
        $newMembers = collect($request->members)->diff($currentMembers);
        foreach ($newMembers as $memberId) {
            $group->members()->attach($memberId);
        }

        return redirect()->route('chat_groups.show', $group)
            ->with('success', 'Cập nhật nhóm thành công!');
    }

    /**
     * Xóa nhóm chat
     */
    public function destroy(ChatGroup $group)
    {
        // Kiểm tra quyền admin
        if (!$group->members()->where('user_id', auth()->id())->where('is_admin', true)->exists()) {
            abort(403, 'Bạn không có quyền xóa nhóm này');
        }

        // Xóa avatar nếu có
        if ($group->avatar) {
            Storage::disk('public')->delete($group->avatar);
        }

        // Xóa nhóm
        $group->delete();

        return redirect()->route('chat_groups.index')
            ->with('success', 'Đã xóa nhóm thành công!');
    }
}
