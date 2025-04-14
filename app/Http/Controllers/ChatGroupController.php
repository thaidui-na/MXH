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
    public function edit($id)
    {
        try {
            // Tìm nhóm bằng ID
            $group = ChatGroup::findOrFail($id); 

            // Kiểm tra quyền admin của nhóm
            if (!$group->members()->where('user_id', auth()->id())->where('is_admin', true)->exists()) {
                return redirect()->route('chat-groups.show', $group)
                               ->with('error', 'Bạn không có quyền chỉnh sửa nhóm này');
            }

            // Lấy danh sách user để chọn thành viên
            $users = User::where('id', '!=', auth()->id())->get();
            $currentMembers = $group->members()->pluck('user_id')->toArray();

            return view('chat_groups.edit', compact('group', 'users', 'currentMembers'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Xử lý trường hợp không tìm thấy nhóm
           \Log::error('Lỗi edit nhóm chat: Không tìm thấy nhóm', ['group_id' => $id]);
           abort(404, 'Không tìm thấy nhóm chat.');
       }
    }

    /**
     * Cập nhật thông tin nhóm và thành viên
     */
    public function update(Request $request, $id)
    {
        try {
            // Tìm nhóm bằng ID
            $group = ChatGroup::findOrFail($id);

            // Kiểm tra quyền admin
            $isAdmin = $group->members()->where('user_id', auth()->id())->where('is_admin', true)->exists();
            if (!$isAdmin) {
                // Nếu không phải admin, chuyển hướng về trang xem nhóm với lỗi
                return redirect()->route('chat-groups.show', $group)
                               ->with('error', 'Bạn không có quyền chỉnh sửa nhóm này');
            }

            // Validate dữ liệu chung
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'members' => 'nullable|array', // Cho phép mảng rỗng nếu không chọn ai khác
                'members.*' => 'exists:users,id', // Kiểm tra từng ID thành viên tồn tại
                'avatar' => 'nullable|image|max:2048' // Validate avatar
            ]);

            // Bắt đầu transaction để đảm bảo toàn vẹn dữ liệu
            DB::beginTransaction();
            
            // Cập nhật avatar nếu có file mới được tải lên
            if ($request->hasFile('avatar')) {
                // Xóa avatar cũ nếu tồn tại
                if ($group->avatar) {
                    Storage::disk('public')->delete($group->avatar);
                }
                // Lưu avatar mới và cập nhật đường dẫn
                $avatarPath = $request->file('avatar')->store('group-avatars', 'public');
                $group->avatar = $avatarPath;
            }

            // Cập nhật thông tin cơ bản của nhóm
            $group->name = $validatedData['name'];
            $group->description = $validatedData['description'];
            $group->save(); // Lưu thay đổi tên, mô tả, avatar

            // --- Xử lý cập nhật thành viên ---
            
            // Lấy danh sách ID thành viên mong muốn từ form
            // Đảm bảo admin hiện tại luôn có trong danh sách
            $desiredMemberIds = collect($request->input('members', []))->map(fn($id) => (int)$id);
            $desiredMemberIds->push(auth()->id())->unique(); // Thêm admin và loại bỏ trùng lặp

            // Lấy danh sách ID thành viên hiện tại của nhóm
            $currentMemberIds = $group->members()->pluck('user_id');

            // Xác định thành viên cần thêm mới
            $membersToAdd = $desiredMemberIds->diff($currentMemberIds);

            // Xác định thành viên cần xóa (không bao gồm admin hiện tại)
            $membersToRemove = $currentMemberIds->diff($desiredMemberIds)->reject(fn($id) => $id === auth()->id());

            // Thêm thành viên mới (không set admin)
            if ($membersToAdd->isNotEmpty()) {
                $group->members()->attach($membersToAdd->all(), ['is_admin' => false]);
                \Log::info('Đã thêm thành viên mới:', ['group_id' => $group->id, 'added_ids' => $membersToAdd->all()]);
            }

            // Xóa thành viên cũ
            if ($membersToRemove->isNotEmpty()) {
                $group->members()->detach($membersToRemove->all());
                 \Log::info('Đã xóa thành viên:', ['group_id' => $group->id, 'removed_ids' => $membersToRemove->all()]);
            }

            // Commit transaction sau khi mọi thứ thành công
            DB::commit();

            // Chuyển hướng về trang xem nhóm với thông báo thành công
            return redirect()->route('chat-groups.show', $group)
                           ->with('success', 'Cập nhật nhóm thành công!');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Xử lý khi không tìm thấy nhóm
            \Log::error('Lỗi update nhóm chat: Không tìm thấy nhóm', ['group_id' => $id]);
            abort(404, 'Không tìm thấy nhóm chat.'); // Trả về lỗi 404
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Nếu validation thất bại, quay lại form với lỗi
            DB::rollback(); // Hoàn tác transaction nếu có lỗi validation
            return back()->withErrors($e->validator)->withInput();
            
        } catch (\Exception $e) {
            // Nếu có lỗi khác xảy ra, hoàn tác transaction và báo lỗi
            DB::rollback();
            \Log::error('Lỗi cập nhật nhóm chat:', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            
            // Quay lại trang trước đó với thông báo lỗi chung
            return back()->with('error', 'Có lỗi xảy ra khi cập nhật nhóm chat. Vui lòng thử lại.');
        }
    }

    /**
     * Xóa nhóm chat
     */
    public function destroy($id)
    {
        try {
             // Tìm nhóm bằng ID, nếu không tìm thấy sẽ trả về 404
            $chat_group = ChatGroup::findOrFail($id); 

            // Debug log để kiểm tra
            \Log::info('=== BẮT ĐẦU XÓA NHÓM ===');
            \Log::info('Thông tin người dùng:', [
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name
            ]);
            
            \Log::info('Thông tin nhóm (sau khi tìm thấy):', [ // Log sau khi đã tìm thấy nhóm
                'group_id' => $chat_group->id,
                'group_name' => $chat_group->name,
                'created_by' => $chat_group->created_by
            ]);

            // Kiểm tra chi tiết quyền admin
            $memberCheck = DB::table('chat_group_members')
                ->where('group_id', $chat_group->id)
                ->where('user_id', auth()->id())
                ->first();

            \Log::info('Thông tin member:', [
                'member_exists' => !is_null($memberCheck),
                'is_admin' => $memberCheck ? $memberCheck->is_admin : false,
                'user_id' => auth()->id()
            ]);

            // Kiểm tra quyền admin hoặc người tạo nhóm
            $isAdmin = DB::table('chat_group_members')
                ->where('group_id', $chat_group->id)
                ->where('user_id', auth()->id())
                ->where('is_admin', true)
                ->exists();

            $isCreator = $chat_group->created_by === auth()->id();

            \Log::info('Kiểm tra quyền:', [
                'is_admin' => $isAdmin,
                'is_creator' => $isCreator
            ]);

            if (!$isAdmin && !$isCreator) {
                \Log::warning('Từ chối quyền xóa nhóm:', [
                    'reason' => 'Không phải admin hoặc người tạo'
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xóa nhóm này'
                ], 403);
            }

            // Bắt đầu transaction để đảm bảo tính toàn vẹn
            DB::beginTransaction();

            \Log::info('Bắt đầu xóa dữ liệu');

            // Xóa avatar nếu có
            if ($chat_group->avatar) {
                Storage::disk('public')->delete($chat_group->avatar);
                \Log::info('Đã xóa avatar');
            }

            // Xóa tất cả tin nhắn trong nhóm
            $messageCount = DB::table('group_messages')
                ->where('group_id', $chat_group->id)
                ->delete();
            \Log::info("Đã xóa {$messageCount} tin nhắn");

            // Xóa tất cả thành viên
            $memberCount = DB::table('chat_group_members')
                ->where('group_id', $chat_group->id)
                ->delete();
            \Log::info("Đã xóa {$memberCount} thành viên");

            // Xóa nhóm
            $chat_group->delete();
            \Log::info('Đã xóa nhóm thành công');

            DB::commit();
            \Log::info('=== KẾT THÚC XÓA NHÓM ===');

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa nhóm thành công!',
                'redirect' => route('messages.index')
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
             // Xử lý trường hợp không tìm thấy nhóm
            \Log::error('Lỗi xóa nhóm chat: Không tìm thấy nhóm', ['group_id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy nhóm chat để xóa.'
            ], 404);
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Lỗi xóa nhóm chat:', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa nhóm chat: ' . $e->getMessage()
            ], 500);
        }
    }
}
