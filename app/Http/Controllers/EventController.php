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
    public function index(Request $request)
    {
        $query = Event::with('user');

        // Tìm kiếm theo từ khóa
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Lọc theo thời gian
        if ($request->has('time_filter')) {
            $now = now();
            switch ($request->time_filter) {
                case 'today':
                    $query->whereDate('event_time', $now->toDateString());
                    break;
                case 'this_week':
                    $query->whereBetween('event_time', [
                        $now->startOfWeek(),
                        $now->endOfWeek()
                    ]);
                    break;
                case 'this_month':
                    $query->whereMonth('event_time', $now->month)
                          ->whereYear('event_time', $now->year);
                    break;
                case 'next_month':
                    $nextMonth = $now->addMonth();
                    $query->whereMonth('event_time', $nextMonth->month)
                          ->whereYear('event_time', $nextMonth->year);
                    break;
                case 'future':
                    $query->where('event_time', '>', $now);
                    break;
            }
        }

        // Lọc theo loại sự kiện (online/offline)
        if ($request->has('location_filter')) {
            switch ($request->location_filter) {
                case 'online':
                    $query->where('event_type', 'online');
                    break;
                case 'offline':
                    $query->where('event_type', 'offline');
                    break;
            }
        }

        $events = $query->latest()->paginate(10);
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
        $request->validate([
            'title' => 'required|string|max:255|regex:/^[\p{L}\s]+$/u', // Chỉ cho phép chữ cái và khoảng trắng
            'description' => 'required|string|max:1000', // Giới hạn độ dài mô tả
            'event_time' => 'required|date|after:now', // Phải là thời gian trong tương lai
            'location' => 'required|string|max:255',
            'event_type' => 'required|in:online,offline',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048|dimensions:min_width=100,min_height=100'
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề sự kiện',
            'title.regex' => 'Tiêu đề chỉ được chứa chữ cái và khoảng trắng',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự',
            'description.required' => 'Vui lòng nhập mô tả sự kiện',
            'description.max' => 'Mô tả không được vượt quá 1000 ký tự',
            'event_time.required' => 'Vui lòng chọn thời gian sự kiện',
            'event_time.after' => 'Thời gian sự kiện phải là thời gian trong tương lai',
            'location.required' => 'Vui lòng nhập địa điểm sự kiện',
            'location.max' => 'Địa điểm không được vượt quá 255 ký tự',
            'event_type.required' => 'Vui lòng chọn loại sự kiện',
            'event_type.in' => 'Loại sự kiện không hợp lệ',
            'image.image' => 'File phải là hình ảnh',
            'image.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif',
            'image.max' => 'Kích thước hình ảnh không được vượt quá 2MB',
            'image.dimensions' => 'Hình ảnh phải có kích thước tối thiểu 100x100 pixels'
        ]);

        // Kiểm tra sự kiện trùng lặp
        $duplicateEvent = Event::where('title', $request->title)
            ->where('event_time', $request->event_time)
            ->where('user_id', auth()->id())
            ->first();

        if ($duplicateEvent) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Bạn đã tạo một sự kiện trùng lặp với cùng tiêu đề và thời gian.');
        }

        $data = $request->all();
        $data['user_id'] = auth()->id();

        // Thêm tiền tố cho địa điểm dựa vào loại sự kiện
        if ($data['event_type'] === 'online') {
            $data['location'] = 'Online: ' . $data['location'];
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('events', 'public');
            $data['image_path'] = $path;
        }

        Event::create($data);

        return redirect()->route('events.index')
            ->with('success', 'Sự kiện đã được tạo thành công.');
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
                'title' => 'required|string|max:255|regex:/^[\p{L}\s]+$/u', // Chỉ cho phép chữ cái và khoảng trắng
                'description' => 'required|string|max:1000', // Giới hạn độ dài mô tả
                'event_time' => 'required|date|after:now', // Phải là thời gian trong tương lai
                'location' => 'required|string|max:255',
                'event_type' => 'required|in:online,offline',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048|dimensions:min_width=100,min_height=100'
            ], [
                'title.required' => 'Vui lòng nhập tiêu đề sự kiện',
                'title.regex' => 'Tiêu đề chỉ được chứa chữ cái và khoảng trắng',
                'title.max' => 'Tiêu đề không được vượt quá 255 ký tự',
                'description.required' => 'Vui lòng nhập mô tả sự kiện',
                'description.max' => 'Mô tả không được vượt quá 1000 ký tự',
                'event_time.required' => 'Vui lòng chọn thời gian sự kiện',
                'event_time.after' => 'Thời gian sự kiện phải là thời gian trong tương lai',
                'location.required' => 'Vui lòng nhập địa điểm sự kiện',
                'location.max' => 'Địa điểm không được vượt quá 255 ký tự',
                'event_type.required' => 'Vui lòng chọn loại sự kiện',
                'event_type.in' => 'Loại sự kiện không hợp lệ',
                'image.image' => 'File phải là hình ảnh',
                'image.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif',
                'image.max' => 'Kích thước hình ảnh không được vượt quá 2MB',
                'image.dimensions' => 'Hình ảnh phải có kích thước tối thiểu 100x100 pixels'
            ]);

            // Kiểm tra sự kiện trùng lặp (trừ sự kiện hiện tại)
            $duplicateEvent = Event::where('title', $request->title)
                ->where('event_time', $request->event_time)
                ->where('user_id', auth()->id())
                ->where('id', '!=', $event->id)
                ->first();

            if ($duplicateEvent) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Bạn đã tạo một sự kiện trùng lặp với cùng tiêu đề và thời gian.');
            }

            $data = $request->all();

            // Thêm tiền tố cho địa điểm dựa vào loại sự kiện
            if ($data['event_type'] === 'online') {
                $data['location'] = 'Online: ' . $data['location'];
            }

            if ($request->hasFile('image')) {
                // Xóa ảnh cũ nếu tồn tại
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
            if (!$event) {
                return redirect()->route('events.index')
                    ->with('error', 'Sự kiện không tồn tại.');
            }

            if (auth()->id() !== $event->user_id) {
                return redirect()->route('events.index')
                    ->with('error', 'Bạn không có quyền xóa sự kiện này.');
            }

            // Kiểm tra xem sự kiện có người tham gia không
            if ($event->activeParticipants()->count() > 0) {
                return redirect()->route('events.index')
                    ->with('error', 'Không thể xóa sự kiện vì đã có người tham gia.');
            }

            // Xóa ảnh nếu tồn tại
            if ($event->image_path) {
                if (!Storage::disk('public')->exists($event->image_path)) {
                    return redirect()->route('events.index')
                        ->with('error', 'Không thể xóa hình ảnh sự kiện vì file không tồn tại.');
                }
                Storage::disk('public')->delete($event->image_path);
            }

            // Xóa sự kiện
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
