<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:posts,id',
            'reason' => 'required|string',
            'custom_reason' => 'nullable|string|max:255'
        ]);

        $reason = $request->reason === 'Khác' && $request->custom_reason
            ? $request->custom_reason
            : $request->reason;

        \App\Models\Report::create([
            'user_id' => auth()->id(),
            'post_id' => $request->post_id,
            'reason' => $reason,
        ]);

        return back()->with('success', 'Báo cáo của bạn đã được gửi.');
    }
}
