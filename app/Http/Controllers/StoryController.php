<?php

namespace App\Http\Controllers;

use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class StoryController extends Controller
{
    public function index()
    {
        $stories = Story::with('user')
            ->active()
            ->fromFollowing(auth()->id())
            ->orWhere('user_id', auth()->id())
            ->latest()
            ->get()
            ->groupBy('user_id');

        return view('stories.index', compact('stories'));
    }

    public function create()
    {
        return view('stories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'media' => 'required|file|mimes:jpeg,png,jpg,gif,mp4|max:10240', // max 10MB
            'caption' => 'nullable|string|max:500'
        ]);

        $file = $request->file('media');
        $mediaType = str_starts_with($file->getMimeType(), 'video/') ? 'video' : 'image';
        $path = $file->store('stories', 'public');

        $story = Story::create([
            'user_id' => auth()->id(),
            'media_path' => $path,
            'media_type' => $mediaType,
            'caption' => $request->caption,
            'expires_at' => Carbon::now()->addHours(24),
            'is_active' => true
        ]);

        return redirect()->route('stories.index')
            ->with('success', 'Story đã được đăng thành công!');
    }

    public function show(Story $story)
    {
        if (!$story->is_active || $story->expires_at < now()) {
            abort(404);
        }

        return view('stories.show', compact('story'));
    }

    public function destroy(Story $story)
    {
        if ($story->user_id !== auth()->id()) {
            abort(403);
        }

        Storage::disk('public')->delete($story->media_path);
        $story->delete();

        return redirect()->route('stories.index')
            ->with('success', 'Story đã được xóa thành công!');
    }
}
