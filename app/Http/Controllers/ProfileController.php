<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

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
        $user = auth()->user();
        $blockedUsers = $user->blockedUsers()->paginate(10, ['*'], 'blocked_page');

        return view('profile.edit', [
            'user' => $user,
            'blockedUsers' => $blockedUsers
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
        // Validate dữ liệu đầu vào
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . auth()->id(),
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:1000',
            'birthday' => 'nullable|date',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user = auth()->user();

        // Xử lý upload avatar nếu có
        if ($request->hasFile('avatar')) {
            // Xóa avatar cũ nếu có
            if ($user->avatar) {
                Storage::delete($user->avatar);
            }

            // Upload và lưu đường dẫn avatar mới
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validatedData['avatar'] = $avatarPath;
        }

        // Cập nhật thông tin người dùng
        $user->update($validatedData);

        return redirect()->back()->with('success', 'Cập nhật thông tin thành công!');
    }

    // Phương thức updatePassword đã được chuyển sang PasswordController
}