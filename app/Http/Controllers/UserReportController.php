<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Post;
use App\Http\Controllers\Controller;

class UserReportController extends Controller
{
    /**
     * Store a newly created report in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Post $post)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ], [
            'reason.required' => 'Vui lòng nhập lý do báo cáo.',
            'reason.max' => 'Lý do báo cáo không được vượt quá 500 ký tự.',
        ]);

        try {
            $existingReport = Report::where('user_id', auth()->id())
                                    ->where('post_id', $post->id)
                                    ->first();

            if ($existingReport) {
                return back()->with('error', 'Bạn đã báo cáo bài viết này rồi.');
            }

            Report::create([
                'user_id' => auth()->id(),
                'post_id' => $post->id,
                'reason' => $request->reason,
            ]);

            return back()->with('success', 'Báo cáo của bạn đã được gửi đi.');

        } catch (\Exception $e) {
            return back()->with('error', 'Đã xảy ra lỗi khi gửi báo cáo: ' . $e->getMessage());
        }
    }
}
