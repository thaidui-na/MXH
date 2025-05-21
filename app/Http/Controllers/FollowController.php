<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    /**
     * Theo dõi một người dùng
     */
    public function follow(User $user)
    {
        if (auth()->id() === $user->id) {
            return response()->json(['message' => 'Không thể tự theo dõi chính mình'], 400);
        }
        if (auth()->user()->isFollowing($user)) {
            return response()->json(['message' => 'Đã theo dõi rồi'], 400);
        }
        auth()->user()->following()->attach($user->id);
        return response()->json(['message' => 'Đã theo dõi thành công']);
    }

    /**
     * Bỏ theo dõi một người dùng
     */
    public function unfollow(User $user)
    {
        if (auth()->id() === $user->id) {
            return response()->json(['message' => 'Không thể tự bỏ theo dõi chính mình'], 400);
        }
        auth()->user()->following()->detach($user->id);
        return response()->json(['message' => 'Đã bỏ theo dõi thành công']);
    }

    /**
     * Lấy danh sách người theo dõi
     */
    public function getFollowers(User $user)
    {
        $followers = $user->followers;
        return response()->json($followers);
    }

    /**
     * Lấy danh sách đang theo dõi
     */
    public function getFollowing(User $user)
    {
        $following = $user->following;
        return response()->json($following);
    }

    /**
     * Kiểm tra trạng thái theo dõi
     */
    public function checkFollowStatus(User $user)
    {
        $isFollowing = Auth::user()->isFollowing($user);
        return response()->json(['is_following' => $isFollowing]);
    }

    /**
     * Lấy danh sách đang theo dõi dạng HTML
     */
    public function followingList(User $user)
    {
        $following = $user->following;
        $html = '';

        foreach ($following as $followingUser) {
            $html .= '
            <div class="col-md-4 mb-3 following-item" data-user-id="'.$followingUser->id.'">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <img src="'.($followingUser->avatar ? asset('images/' . $followingUser->avatar) : asset('images/default-avatar.jpg')).'"
                                 class="rounded-circle me-3"
                                 style="width: 50px; height: 50px; object-fit: cover;"
                                 alt="'.$followingUser->name.'\'s avatar">
                            <div>
                                <h5 class="mb-0">'.$followingUser->name.'</h5>
                                <p class="text-muted mb-0">'.$followingUser->email.'</p>
                            </div>
                        </div>';
            if (auth()->id() !== $followingUser->id) {
                $isFollowing = auth()->user()->isFollowing($followingUser);
                $html .= '
                        <div class="mt-3">
                            <button class="btn '.($isFollowing ? 'btn-primary' : 'btn-outline-primary').' btn-sm follow-button"
                                    data-user-id="'.$followingUser->id.'">
                                <i class="fas fa-user-plus"></i>
                                <span class="follow-text">'.($isFollowing ? 'Hủy theo dõi' : 'Theo dõi').'</span>
                            </button>
                        </div>';
            }
            $html .= '
                    </div>
                </div>
            </div>';
        }

        if (empty($html)) {
            $html = '<div class="col-12"><div class="alert alert-info">Chưa theo dõi ai.</div></div>';
        }

        return $html;
    }
}
