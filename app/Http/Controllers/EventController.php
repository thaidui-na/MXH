<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = Event::with('user')->latest()->paginate(10);
        return view('events.index', compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('events.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'event_time' => 'required|date',
                'location' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $data = $request->all();
            $data['user_id'] = auth()->id();

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('events', 'public');
                $data['image_path'] = $path;
            }

            Event::create($data);

            return redirect()->route('events.index')
                ->with('success', 'Sự kiện đã được tạo thành công.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi tạo sự kiện: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $event = Event::withTrashed()->findOrFail($id);
        
        // Nếu sự kiện đã bị xóa
        if ($event->trashed()) {
            return back()->with('error', 'Sự kiện này đã bị xóa.');
        }

        $event->load(['user', 'activeParticipants']);
        return view('events.show', compact('event'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        if (auth()->id() !== $event->user_id) {
            return redirect()->route('events.index')
                ->with('error', 'Bạn không có quyền chỉnh sửa sự kiện này.');
        }
        return view('events.edit', compact('event'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        try {
            if (auth()->id() !== $event->user_id) {
                return redirect()->route('events.index')
                    ->with('error', 'Bạn không có quyền cập nhật sự kiện này.');
            }

            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'event_time' => 'required|date',
                'location' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $data = $request->all();

            if ($request->hasFile('image')) {
                // Delete old image
                if ($event->image_path) {
                    Storage::disk('public')->delete($event->image_path);
                }
                
                $path = $request->file('image')->store('events', 'public');
                $data['image_path'] = $path;
            }

            $event->update($data);

            return redirect()->route('events.index')
                ->with('success', 'Sự kiện đã được cập nhật thành công.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi cập nhật sự kiện: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        try {
            if (auth()->id() !== $event->user_id) {
                return redirect()->route('events.index')
                    ->with('error', 'Bạn không có quyền xóa sự kiện này.');
            }

            if ($event->image_path) {
                Storage::disk('public')->delete($event->image_path);
            }

            $event->delete();

            return redirect()->route('events.index')
                ->with('success', 'Sự kiện đã được xóa thành công.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi xóa sự kiện: ' . $e->getMessage());
        }
    }

    public function join($id)
    {
        $event = Event::withTrashed()->findOrFail($id);
        
        // Nếu sự kiện đã bị xóa
        if ($event->trashed()) {
            return back()->with('error', 'Sự kiện này đã bị xóa.');
        }

        // Kiểm tra xem người dùng đã tham gia chưa
        $existingParticipation = $event->participants()
            ->where('user_id', auth()->id())
            ->first();

        if ($existingParticipation) {
            // Nếu đã tham gia và đang ở trạng thái 'joined'
            if ($existingParticipation->pivot->status === 'joined') {
                return back()->with('error', 'Bạn đã tham gia sự kiện này.');
            }
            
            // Nếu đã tham gia nhưng đã rời đi, cập nhật lại trạng thái
            $event->participants()->updateExistingPivot(auth()->id(), [
                'status' => 'joined',
                'joined_at' => now(),
                'left_at' => null
            ]);
        } else {
            // Nếu chưa tham gia, tạo bản ghi mới
            $event->participants()->attach(auth()->id(), [
                'status' => 'joined',
                'joined_at' => now()
            ]);
        }

        return back()->with('success', 'Bạn đã tham gia sự kiện thành công.');
    }

    public function leave(Event $event)
    {
        if (!$event->isParticipant(auth()->user())) {
            return back()->with('error', 'Bạn chưa tham gia sự kiện này.');
        }

        $event->participants()->updateExistingPivot(auth()->id(), [
            'status' => 'left',
            'left_at' => now()
        ]);

        return back()->with('success', 'Bạn đã rời khỏi sự kiện thành công.');
    }
}
