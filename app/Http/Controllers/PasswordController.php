<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller; // Đảm bảo Controller được import
// Bỏ use Illuminate\Validation\Rules\Password; nếu không dùng nữa

/**
 * Controller xử lý các chức năng liên quan đến mật khẩu người dùng
 */
class PasswordController extends Controller
{
    /**
     * Hiển thị form quên mật khẩu
     */
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Gửi email chứa link đặt lại mật khẩu
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    /**
     * Hiển thị form đặt lại mật khẩu
     */
    public function showResetForm(Request $request, $token)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    /**
     * Xử lý đặt lại mật khẩu
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    /**
     * Hiển thị form đổi mật khẩu
     */
    public function showChangePasswordForm()
    {
        return view('auth.change-password');
    }

    /**
     * Xử lý đổi mật khẩu
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|min:8|confirmed|different:current_password',
        ]);

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('status', 'Mật khẩu đã được cập nhật thành công.');
    }
}
