<?php

namespace App\Http\Controllers;

use App\Models\ChatGroup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

/**
 * Controller xử lý các chức năng liên quan đến nhóm chat
 * Bao gồm: tạo, xem, sửa, xóa nhóm và quản lý thành viên
 */
class ChatGroupController extends Controller
{
    /**
     * Hiển thị danh sách nhóm chat
     * Lấy các nhóm mà người dùng hiện tại là thành viên
     * Kèm theo thông tin thành viên và tin nhắn mới nhất để hiển thị tóm tắt
     */
    public function index()
    {
        // Lấy các nhóm chat mà người dùng đang đăng nhập là thành viên
        $groups = auth()->user()->chatGroups()
            ->with(['members', 'messages' => function($query) {
                $query->latest()->take(1)->with('sender');
            }])
            ->get();
        
        return view('chat_groups.index', compact('groups'));
    }

    /**
     * Hiển thị form tạo nhóm mới
     * Cung cấp danh sách người dùng (trừ người dùng hiện tại) để chọn làm thành viên
     */
    public function create()
    {
        // Lấy danh sách tất cả người dùng trừ người dùng đang đăng nhập
        $users = User::where('id', '!=', auth()->id())->get();
        return view('chat_groups.create', compact('users'));
    }

    /**
     * Lưu nhóm chat mới vào database
     * - Validate dữ liệu đầu vào
     * - Upload avatar nếu có
     * - Tạo nhóm với thông tin cơ bản
     * - Thêm các thành viên được chọn và người tạo vào nhóm
     * - Đặt người tạo làm admin nhóm
     * - Sử dụng transaction để đảm bảo toàn vẹn dữ liệu
     */
    public function store(Request $request)
    {
        // Validate dữ liệu gửi lên từ form
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'members' => 'required|array|min:2',
            'members.*' => 'exists:users,id',
            'avatar' => 'nullable|image|max:2048'
        ]);

        DB::beginTransaction();
        try {
            // Xử lý upload avatar nếu người dùng có tải lên file
            $avatarPath = null;
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('group-avatars', 'public');
            }

            // Tạo bản ghi nhóm mới trong bảng 'chat_groups'
            $group = ChatGroup::create([
                'name' => $request->name,
                'description' => $request->description,
                'created_by' => auth()->id(),
                'avatar' => $avatarPath
            ]);

            // Chuẩn bị dữ liệu thành viên để thêm vào bảng pivot 'chat_group_members'
            // Bao gồm các thành viên được chọn từ form và người tạo nhóm
            $memberData = collect($request->members)
                ->map(function($memberId) {
                    return [
                        'user_id' => $memberId,
                        'is_admin_group_chat' => false,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                })
                ->push([
                    'user_id' => auth()->id(),
                    'is_admin_group_chat' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

            // Thêm tất cả thành viên (bao gồm người tạo) vào nhóm thông qua relationship 'members'
            // Sử dụng attach() để thêm nhiều bản ghi vào bảng pivot
            // Chỉ lấy ra các 'user_id' từ collection $memberData
            $group->members()->attach($memberData->pluck('user_id')->toArray());

            // Cập nhật quyền admin cho người tạo
            DB::table('chat_group_members')
                ->where('group_id', $group->id)
                ->where('user_id', auth()->id())
                ->update(['is_admin_group_chat' => true]);

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
     * Hiển thị trang chat của nhóm cụ thể
     * - Kiểm tra quyền truy cập (phải là thành viên)
     * - Load tin nhắn và thông tin thành viên
     * - Sắp xếp tin nhắn theo thời gian tạo
     */
    public function show($id)
    {
        // Tìm nhóm chat theo ID
        $group = ChatGroup::findOrFail($id);
        
        \Log::info('Đang truy cập nhóm chat:', [
            'group_id' => $id,
            'user_id' => auth()->id(),
            'is_member' => DB::table('chat_group_members')->where([
                'group_id' => $id,
                'user_id' => auth()->id()
            ])->exists()
        ]);

        // Kiểm tra xem người dùng hiện tại có phải là thành viên của nhóm này không
        // Sửa chỗ này: Không kiểm tra is_admin_group_chat, chỉ kiểm tra là thành viên
        $isMember = DB::table('chat_group_members')
            ->where([
                'group_id' => $id,
                'user_id' => auth()->id()
            ])
            ->exists();  // Bỏ điều kiện where('is_admin_group_chat', true)

        if (!$isMember) {
            \Log::warning('Người dùng không có quyền truy cập:', [
                'user_id' => auth()->id(),
                'group_id' => $id
            ]);
            abort(403, 'Bạn không có quyền truy cập nhóm này');
        }

        // Lấy danh sách tin nhắn của nhóm
        $messages = $group->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();
        
        // Tải kèm thông tin thành viên và người tạo nhóm
        $group->load(['members', 'creator']);

        return view('chat_groups.show', compact('group', 'messages'));
    }

    /**
     * Hiển thị form chỉnh sửa thông tin nhóm
     * Chỉ admin của nhóm mới có quyền truy cập form này
     */
    public function edit($id)
    {
        try {
            // Tìm nhóm theo ID, nếu không thấy sẽ ném lỗi ModelNotFoundException
            $group = ChatGroup::findOrFail($id); 

            // Kiểm tra xem người dùng hiện tại có phải là admin của nhóm không
            // Truy vấn bảng pivot 'chat_group_members' thông qua relationship 'members'
            if (!$group->members()->where('user_id', auth()->id())->where('is_admin_group_chat', true)->exists()) {
                // Nếu không phải admin, chuyển hướng về trang xem nhóm với thông báo lỗi
                return redirect()->route('chat-groups.show', $group)
                               ->with('error', 'Bạn không có quyền chỉnh sửa nhóm này');
            }

            // Lấy danh sách tất cả người dùng (trừ người dùng hiện tại) để hiển thị trong multi-select
            $users = User::where('id', '!=', auth()->id())->get();
            // Lấy danh sách ID của các thành viên hiện tại trong nhóm
            $currentMembers = $group->members()->pluck('user_id')->toArray();

            // Trả về view 'chat_groups.edit' và truyền các biến cần thiết vào view
            return view('chat_groups.edit', compact('group', 'users', 'currentMembers'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Xử lý trường hợp không tìm thấy nhóm (do ID không đúng)
           \Log::error('Lỗi edit nhóm chat: Không tìm thấy nhóm', ['group_id' => $id]);
           // Dừng và trả về lỗi 404 (Not Found)
           abort(404, 'Không tìm thấy nhóm chat.');
       }
    }

    /**
     * Cập nhật thông tin nhóm và thành viên sau khi submit form edit
     * - Kiểm tra quyền admin
     * - Validate dữ liệu
     * - Cập nhật thông tin cơ bản (tên, mô tả)
     * - Cập nhật avatar (xóa cũ, lưu mới nếu có)
     * - Cập nhật danh sách thành viên (thêm mới, xóa cũ)
     * - Sử dụng transaction
     */
    public function update(Request $request, $id)
    {
        try {
            // Tìm nhóm cần cập nhật bằng ID
            $group = ChatGroup::findOrFail($id);

            // Kiểm tra quyền admin của người dùng hiện tại đối với nhóm này
            $isAdmin = $group->members()->where('user_id', auth()->id())->where('is_admin_group_chat', true)->exists();
            // Nếu không phải admin
            if (!$isAdmin) {
                // Chuyển hướng về trang xem nhóm với thông báo lỗi
                return redirect()->route('chat-groups.show', $group)
                               ->with('error', 'Bạn không có quyền chỉnh sửa nhóm này');
            }

            // Validate dữ liệu chung từ request
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'members' => 'nullable|array',
                'members.*' => 'exists:users,id',
                'avatar' => 'nullable|image|max:2048'
            ]);

            // Bắt đầu transaction
            DB::beginTransaction();
            
            // Cập nhật avatar nếu có file mới được tải lên
            if ($request->hasFile('avatar')) {
                // Xóa avatar cũ nếu nhóm hiện đang có avatar
                if ($group->avatar) {
                    // Xóa file từ disk 'public' (thường là storage/app/public)
                    Storage::disk('public')->delete($group->avatar);
                }
                // Lưu avatar mới vào thư mục 'group-avatars' và lấy đường dẫn
                $avatarPath = $request->file('avatar')->store('group-avatars', 'public');
                // Cập nhật đường dẫn avatar mới cho nhóm
                $group->avatar = $avatarPath;
            }

            // Cập nhật thông tin cơ bản của nhóm từ dữ liệu đã validate
            $group->name = $validatedData['name'];
            $group->description = $validatedData['description'];
            // Lưu các thay đổi (tên, mô tả, avatar) vào database
            $group->save();

            // --- Xử lý cập nhật thành viên ---
            
            // Lấy danh sách ID thành viên mong muốn từ form (input 'members')
            // Nếu không có input 'members', mặc định là mảng rỗng
            // Chuyển đổi các ID thành kiểu integer và đưa vào Collection
            $desiredMemberIds = collect($request->input('members', []))->map(fn($id) => (int)$id);
            // Đảm bảo admin hiện tại (người đang thực hiện thao tác) luôn có trong danh sách
            $desiredMemberIds->push(auth()->id())->unique(); // Thêm ID admin và loại bỏ trùng lặp

            // Lấy danh sách ID thành viên hiện tại của nhóm từ bảng pivot
            $currentMemberIds = $group->members()->pluck('user_id');

            // Xác định thành viên cần thêm mới: những ID có trong $desiredMemberIds nhưng không có trong $currentMemberIds
            $membersToAdd = $desiredMemberIds->diff($currentMemberIds);

            // Xác định thành viên cần xóa: những ID có trong $currentMemberIds nhưng không có trong $desiredMemberIds
            // Loại bỏ admin hiện tại khỏi danh sách cần xóa (admin không thể tự xóa mình)
            $membersToRemove = $currentMemberIds->diff($desiredMemberIds)->reject(fn($id) => $id === auth()->id());

            // Thêm thành viên mới vào bảng pivot nếu có
            if ($membersToAdd->isNotEmpty()) {
                // Sử dụng attach() để thêm nhiều thành viên, set is_admin_group_chat = false cho các thành viên mới này
                $group->members()->attach($membersToAdd->all(), ['is_admin_group_chat' => false]);
                // Ghi log
                \Log::info('Đã thêm thành viên mới:', ['group_id' => $group->id, 'added_ids' => $membersToAdd->all()]);
            }

            // Xóa thành viên cũ khỏi bảng pivot nếu có
            if ($membersToRemove->isNotEmpty()) {
                // Sử dụng detach() để xóa các thành viên khỏi nhóm
                $group->members()->detach($membersToRemove->all());
                 // Ghi log
                 \Log::info('Đã xóa thành viên:', ['group_id' => $group->id, 'removed_ids' => $membersToRemove->all()]);
            }

            // Nếu mọi thứ thành công, commit transaction
            DB::commit();

            // Chuyển hướng về trang xem nhóm với thông báo thành công
            return redirect()->route('chat-groups.show', $group)
                           ->with('success', 'Cập nhật nhóm thành công!');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Xử lý khi không tìm thấy nhóm (ID không đúng)
            \Log::error('Lỗi update nhóm chat: Không tìm thấy nhóm', ['group_id' => $id]);
            // Trả về lỗi 404
            abort(404, 'Không tìm thấy nhóm chat.');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Nếu validation thất bại (dữ liệu gửi lên không hợp lệ)
            DB::rollback(); // Hoàn tác transaction
            // Quay lại trang trước đó (form edit) với các lỗi validation và dữ liệu đã nhập
            return back()->withErrors($e->validator)->withInput();
            
        } catch (\Exception $e) {
            // Nếu có lỗi khác xảy ra (lỗi DB, lỗi logic...)
            DB::rollback(); // Hoàn tác transaction
            // Ghi log chi tiết lỗi
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
     * - Kiểm tra quyền xóa (phải là admin hoặc người tạo nhóm)
     * - Xóa avatar (nếu có) khỏi storage
     * - Xóa tất cả tin nhắn liên quan
     * - Xóa tất cả thành viên liên quan khỏi bảng pivot
     * - Xóa bản ghi nhóm khỏi database
     * - Sử dụng transaction
     * - Thường được gọi qua AJAX nên trả về JSON response
     */
    public function destroy($id)
    {
        try {
            $chat_group = ChatGroup::findOrFail($id);

            \Log::info('=== BẮT ĐẦU XÓA NHÓM ===');
            \Log::info('Thông tin người dùng:', [
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name
            ]);
            
            \Log::info('Thông tin nhóm:', [
                'group_id' => $chat_group->id,
                'group_name' => $chat_group->name,
                'created_by' => $chat_group->created_by
            ]);

            // Kiểm tra quyền admin bằng cách chỉ định rõ bảng cho cột is_admin_group_chat
            $isAdmin = DB::table('chat_group_members')
                ->where('group_id', $chat_group->id)
                ->where('user_id', auth()->id())
                ->where('is_admin_group_chat', true)
                ->exists();

            // Kiểm tra người tạo nhóm
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

            // Bắt đầu transaction
            DB::beginTransaction();

            \Log::info('Bắt đầu xóa dữ liệu liên quan');

            // Xóa avatar nếu nhóm có avatar
            if ($chat_group->avatar) {
                // Xóa file khỏi disk 'public'
                Storage::disk('public')->delete($chat_group->avatar);
                \Log::info('Đã xóa avatar');
            }

            // Xóa tất cả tin nhắn thuộc về nhóm này
            // Sử dụng DB Query Builder để xóa hiệu quả hơn khi xóa nhiều bản ghi
            $messageCount = DB::table('group_messages')
                ->where('group_id', $chat_group->id)
                ->delete();
            \Log::info("Đã xóa {$messageCount} tin nhắn");

            // Xóa tất cả các bản ghi thành viên liên quan đến nhóm này trong bảng pivot
            $memberCount = DB::table('chat_group_members')
                ->where('group_id', $chat_group->id)
                ->delete();
            \Log::info("Đã xóa {$memberCount} thành viên");

            // Xóa bản ghi nhóm khỏi bảng 'chat_groups'
            $chat_group->delete();
            \Log::info('Đã xóa nhóm thành công');

            // Nếu mọi thứ thành công, commit transaction
            DB::commit();
            \Log::info('=== KẾT THÚC XÓA NHÓM ===');

            // Trả về JSON response thành công
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa nhóm thành công!',
                // Cung cấp URL để redirect sau khi xóa thành công (thường là trang danh sách tin nhắn/nhóm)
                'redirect' => route('messages.index') 
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
             // Xử lý trường hợp không tìm thấy nhóm (ID không đúng)
            \Log::error('Lỗi xóa nhóm chat: Không tìm thấy nhóm', ['group_id' => $id]);
            // Trả về JSON response lỗi 404 (Not Found)
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy nhóm chat để xóa.'
            ], 404);
        } catch (\Exception $e) {
            // Nếu có lỗi khác xảy ra
            DB::rollback(); // Hoàn tác transaction
            // Ghi log chi tiết lỗi
            \Log::error('Lỗi xóa nhóm chat:', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            
            // Trả về JSON response lỗi 500 (Internal Server Error)
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa nhóm chat: ' . $e->getMessage() // Kèm theo thông báo lỗi gốc
            ], 500);
        }
    }
}
