<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\User;

/**
 * Controller quản lý các chức năng liên quan đến hồ sơ người dùng (Profile)
 * Bao gồm hiển thị form chỉnh sửa và cập nhật thông tin
 */
class ProfileController extends Controller
{
    /**
     * Hiển thị form chỉnh sửa thông tin cá nhân của người dùng đang đăng nhập.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        // Trả về view 'profile.edit'
        // Truyền vào view biến 'user' chứa thông tin của người dùng đang được xác thực (đăng nhập)
        return view('profile.edit', [
            'user' => auth()->user() // Lấy thông tin người dùng hiện tại
        ]);
    }

    /**
     * Cập nhật thông tin cơ bản (tên, email, điện thoại, bio, ngày sinh, avatar)
     * của người dùng đang đăng nhập.
     * Xử lý validate dữ liệu và upload avatar mới (nếu có).
     *
     * @param  \Illuminate\Http\Request  $request Dữ liệu gửi lên từ form chỉnh sửa
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        // Validate dữ liệu nhận được từ request
        // Định nghĩa các quy tắc xác thực cho từng trường
        $validated = $request->validate([
            'name' => 'required|string|max:255', // Tên là bắt buộc, dạng chuỗi, tối đa 255 ký tự
            // Email là bắt buộc, dạng email, phải là duy nhất trong bảng 'users' NGOẠI TRỪ chính user hiện tại (id = auth()->id())
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'phone' => 'nullable|string|max:20', // Số điện thoại có thể null, nếu có phải là chuỗi, tối đa 20 ký tự
            'bio' => 'nullable|string|max:1000', // Giới thiệu có thể null, nếu có phải là chuỗi, tối đa 1000 ký tự
            'birthday' => 'nullable|date', // Ngày sinh có thể null, nếu có phải là định dạng ngày hợp lệ
            // Avatar có thể null, nếu có phải là file ảnh, định dạng jpeg/png/jpg/gif, tối đa 2048 KB (2MB)
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Lấy đối tượng User của người dùng đang đăng nhập
        $user = auth()->user();

        // Xử lý việc upload avatar mới nếu người dùng có chọn file avatar trong form
        if ($request->hasFile('avatar')) {
            // Kiểm tra xem người dùng hiện tại đã có avatar cũ chưa
            if ($user->avatar) {
                // Nếu có avatar cũ, xóa file avatar đó khỏi storage disk 'public'
                // Đường dẫn file cũ được lưu trong $user->avatar
                Storage::disk('public')->delete($user->avatar);
            }

            // Upload file avatar mới lên thư mục 'avatars' trong storage disk 'public'
            // Hàm store() trả về đường dẫn tương đối của file đã lưu (ví dụ: 'avatars/ten_file_moi.jpg')
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            // Thêm hoặc cập nhật đường dẫn avatar mới vào mảng dữ liệu $validated để chuẩn bị update vào DB
            $validated['avatar'] = $avatarPath;
        }

        // Cập nhật thông tin người dùng trong database với dữ liệu đã được validate (và có thể đã thêm avatar)
        // Phương thức update() của Eloquent model sẽ tự động lưu các thay đổi
        $user->update($validated);

        // Chuyển hướng người dùng quay lại trang chỉnh sửa profile
        return redirect()
            ->route('profile.edit') // Sử dụng route name 'profile.edit'
            ->with('success', 'Cập nhật thông tin thành công!'); // Gửi kèm thông báo thành công (flash message) vào session
    }

    // Phương thức updatePassword đã được chuyển sang PasswordController

    /**
     * Hiển thị form xóa tài khoản
     */
    public function showDeleteAccount()
    {
        return view('profile.delete-account');
    }

    /**
     * Xử lý yêu cầu xóa tài khoản
     */
    public function deleteAccount(Request $request)
    {
        // Validate mật khẩu
        $request->validate([
            'password' => 'required|current_password',
            'delete_type' => 'required|in:disable,delete'
        ]);

        $user = auth()->user();

        if ($request->delete_type === 'disable') {
            $user->disable();
            $message = 'Tài khoản của bạn đã được vô hiệu hóa.';
        } else {
            $user->deletePermanently();
            $message = 'Tài khoản của bạn đã được xóa vĩnh viễn.';
        }

        // Đăng xuất người dùng
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', $message);
    }

    /**
     * Hiển thị danh sách bạn bè của người dùng
     *
     * @return \Illuminate\View\View
     */
    public function friends()
    {
        $user = auth()->user();
        $friends = $user->friends()->paginate(12);

        return view('profile.friends', [
            'friends' => $friends
        ]);
    }
}