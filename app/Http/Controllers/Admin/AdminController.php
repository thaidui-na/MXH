<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::count();
        $posts = Post::count();
        return view('admin.dashboard', compact('users', 'posts'));
    }

    public function users()
    {
        $users = User::paginate(10);
        return view('admin.users', compact('users'));
    }

    public function posts()
    {
        $posts = Post::with('user')->paginate(10);
        return view('admin.posts', compact('posts'));
    }

    public function deleteUser($id)
    {
        User::find($id)->delete();
        return back()->with('success', 'Đã xóa người dùng');
    }

    public function deletePost($id)
    {
        Post::find($id)->delete();
        return back()->with('success', 'Đã xóa bài viết');
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$id,
            'is_admin' => 'boolean',
            'password' => 'nullable|min:6'
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->is_admin = $request->has('is_admin');
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();
        return redirect()->route('admin.users')->with('success', 'Cập nhật người dùng thành công');
    }

    public function editPost($id)
    {
        $post = Post::findOrFail($id);
        return view('admin.posts.edit', compact('post'));
    }

    public function updatePost(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required'
        ]);

        $post->title = $request->title;
        $post->content = $request->content;
        $post->save();

        return redirect()->route('admin.posts')->with('success', 'Cập nhật bài viết thành công');
    }
}
