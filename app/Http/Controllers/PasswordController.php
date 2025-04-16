<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller; // Đảm bảo Controller được import
// Bỏ use Illuminate\Validation\Rules\Password; nếu không dùng nữa

/**
 * Controller xử lý các chức năng liên quan đến mật khẩu người dùng
 */
class PasswordController extends Controller
{
    /**
     * Hiển thị form đổi mật khẩu.
     *
     * @return \Illuminate\View\View
     */
    public function showChangePasswordForm()
    {
        // Trả về view chứa form đổi mật khẩu
        return view('profile.password');
    }

    /**
     * Cập nhật mật khẩu người dùng.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        // Validate dữ liệu đầu vào
        $validated = $request->validate([
            // Mật khẩu hiện tại là bắt buộc và phải khớp với mật khẩu trong DB
            'current_password' => ['required', 'current_password'],
            // Mật khẩu mới chỉ cần required và confirmed, bỏ các quy tắc phức tạp
            'password' => ['required', 'confirmed'], // <--- **THAY ĐỔI Ở ĐÂY**
        ], [
            // Custom thông báo lỗi tiếng Việt
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'current_password.current_password' => 'Mật khẩu hiện tại không chính xác.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            // Bỏ các thông báo lỗi về độ dài, ký tự
            // 'password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            // 'password' => 'Mật khẩu mới phải bao gồm chữ hoa, chữ thường và số.',
        ]);

        // Lấy người dùng hiện tại
        $user = auth()->user();

        // Cập nhật mật khẩu mới đã được hash
        $user->update([
            'password' => Hash::make($validated['password'])
        ]);

        // Chuyển hướng về trang đổi mật khẩu với thông báo thành công
        return redirect()
            ->route('password.change') // Chuyển hướng về chính trang đổi mật khẩu
            ->with('success', 'Cập nhật mật khẩu thành công!');
    }
}
