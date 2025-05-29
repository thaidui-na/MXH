<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Post;
use App\Models\Report;
use App\Models\UserReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Controller quản lý các chức năng trong khu vực quản trị (Admin Panel)
 * Bao gồm quản lý người dùng, bài viết, và hiển thị dashboard
 */
class AdminController extends Controller
{
    /**
     * Hiển thị trang dashboard chính của admin.
     * Thống kê số lượng người dùng và bài viết.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Đếm tổng số người dùng trong hệ thống
        $users = User::count();
        // Đếm tổng số bài viết trong hệ thống
        $posts = Post::count();
        // Trả về view 'admin.dashboard' và truyền dữ liệu thống kê vào view
        return view('admin.dashboard', compact('users', 'posts'));
    }

    /**
     * Hiển thị danh sách người dùng với phân trang.
     * Mỗi trang hiển thị 10 người dùng.
     *
     * @return \Illuminate\View\View
     */
    public function users()
    {
        // Lấy danh sách người dùng, sắp xếp và phân trang (10 user/trang)
        $users = User::paginate(10);
        // Trả về view 'admin.users' và truyền danh sách người dùng vào view
        return view('admin.users', compact('users'));
    }

    /**
     * Hiển thị danh sách bài viết với phân trang.
     * Lấy kèm thông tin người đăng (user) để tránh N+1 query.
     * Mỗi trang hiển thị 10 bài viết.
     *
     * @return \Illuminate\View\View
     */
    public function posts()
    {
        // Lấy danh sách bài viết, kèm theo thông tin 'user' và phân trang (10 post/trang)
        $posts = Post::with('user')->paginate(10);
        // Trả về view 'admin.posts' và truyền danh sách bài viết vào view
        return view('admin.posts', compact('posts'));
    }

    /**
     * Xóa một người dùng dựa vào ID.
     *
     * @param  int  $id ID của người dùng cần xóa
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteUser($id)
    {
        try {
            $user = User::find($id);
            
            if (!$user) {
                return back()->with('error', 'Người dùng không tồn tại hoặc đã bị xóa.');
            }

            $user->delete();
            return back()->with('success', 'Đã xóa người dùng thành công.');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra khi xóa người dùng. Vui lòng thử lại sau.');
        }
    }

    /**
     * Xóa một bài viết dựa vào ID.
     *
     * @param  int  $id ID của bài viết cần xóa
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deletePost($id)
    {
        try {
            $post = Post::find($id);

            if (!$post) {
                return back()->with('error', 'Bài viết không tồn tại hoặc đã bị xóa.');
            }

            $post->delete();
            return back()->with('success', 'Đã xóa bài viết thành công.');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra khi xóa bài viết. Vui lòng thử lại sau.');
        }
    }

    /**
     * Hiển thị form chỉnh sửa thông tin người dùng.
     *
     * @param  int  $id ID của người dùng cần chỉnh sửa
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function editUser($id)
    {
        // Tìm người dùng bằng ID, nếu không tìm thấy sẽ ném ra lỗi ModelNotFoundException (thường dẫn đến 404)
        $user = User::findOrFail($id);
        // Trả về view 'admin.users.edit' và truyền thông tin người dùng vào view
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Cập nhật thông tin người dùng sau khi chỉnh sửa.
     *
     * @param  \Illuminate\Http\Request  $request Dữ liệu từ form chỉnh sửa
     * @param  int  $id ID của người dùng cần cập nhật
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateUser(Request $request, $id)
    {
        // Tìm người dùng cần cập nhật
        $user = User::findOrFail($id);

        // Validate dữ liệu
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$id,
            'is_admin' => 'nullable|boolean'
        ];

        // Thêm rule cho password nếu có nhập mật khẩu mới
        if ($request->filled('password')) {
            $rules['password'] = 'required|min:6|confirmed';
        }

        $request->validate($rules);

        // Cập nhật thông tin cơ bản
        $user->name = $request->name;
        $user->email = $request->email;
        $user->is_admin = $request->has('is_admin');

        // Cập nhật mật khẩu nếu có
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Lưu thay đổi
        $user->save();

        return redirect()->route('admin.users')
            ->with('success', 'Cập nhật người dùng thành công');
    }

    /**
     * Hiển thị form chỉnh sửa bài viết.
     *
     * @param  int  $id ID của bài viết cần chỉnh sửa
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function editPost($id)
    {
        // Tìm bài viết bằng ID, nếu không tìm thấy sẽ ném lỗi
        $post = Post::findOrFail($id);
        // Trả về view 'admin.posts.edit' và truyền thông tin bài viết vào view
        return view('admin.posts.edit', compact('post'));
    }

    /**
     * Cập nhật thông tin bài viết sau khi chỉnh sửa.
     *
     * @param  \Illuminate\Http\Request  $request Dữ liệu từ form chỉnh sửa
     * @param  int  $id ID của bài viết cần cập nhật
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePost(Request $request, $id)
    {
        // Tìm bài viết cần cập nhật bằng ID
        $post = Post::findOrFail($id);

        // Validate dữ liệu đầu vào từ form
        $request->validate([
            'title' => 'required|string|max:255', // Tiêu đề là bắt buộc, dạng chuỗi, tối đa 255 ký tự
            'content' => 'required' // Nội dung là bắt buộc
        ]);

        // Cập nhật tiêu đề bài viết
        $post->title = $request->title;
        // Cập nhật nội dung bài viết
        $post->content = $request->content;
        // Lưu các thay đổi vào database
        $post->save();

        // Chuyển hướng về trang danh sách bài viết với thông báo thành công
        return redirect()->route('admin.posts')->with('success', 'Cập nhật bài viết thành công');
    }

    /**
     * Hiển thị danh sách báo cáo bài viết.
     *
     * @return \Illuminate\View\View
     */
    public function reports()
    {
        // Lấy danh sách báo cáo, kèm thông tin bài viết và người báo cáo
        $reports = Report::with(['post', 'user'])->latest()->paginate(15);
        return view('admin.reports', compact('reports'));
    }

    /**
     * Xóa một báo cáo bài viết dựa vào ID.
     *
     * @param  int  $id ID của báo cáo cần xóa
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteReport($id)
    {
        $report = Report::find($id);
        if ($report) {
            $report->delete();
            return redirect()->route('admin.reports')->with('success', 'Xóa báo cáo bài viết thành công!');
        }
        return redirect()->route('admin.reports')->with('error', 'Không tìm thấy báo cáo bài viết!');
    }

    /**
     * Hiển thị danh sách báo cáo người dùng.
     *
     * @return \Illuminate\View\View
     */
    public function userReports()
    {
        // Lấy danh sách báo cáo người dùng, kèm thông tin người báo cáo và người bị báo cáo
        $userReports = UserReport::with(['reporter', 'reportedUser'])->latest()->paginate(15);
        return view('admin.user-reports', compact('userReports'));
    }

    /**
     * Đánh dấu một báo cáo người dùng là đã xử lý.
     *
     * @param  \App\Models\UserReport  $userReport
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markUserReportResolved(UserReport $userReport)
    {
        $userReport->is_resolved = true;
        $userReport->save();

        return redirect()->route('admin.user-reports')->with('success', 'Đã đánh dấu báo cáo là đã xử lý!');
    }

    /**
     * Xóa một báo cáo người dùng.
     *
     * @param  \App\Models\UserReport  $userReport
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteUserReport(UserReport $userReport)
    {
        $userReport->delete();

        return redirect()->route('admin.user-reports')->with('success', 'Đã xóa báo cáo người dùng!');
    }
}
