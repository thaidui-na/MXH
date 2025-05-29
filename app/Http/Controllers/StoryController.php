<?php

namespace App\Http\Controllers;

use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\Format\Video\X264;

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
        
        if ($mediaType === 'video') {
            // Tạo thư mục tạm để xử lý video
            $tempPath = storage_path('app/temp');
            if (!file_exists($tempPath)) {
                mkdir($tempPath, 0777, true);
            }
            
            // Lưu file video tạm thời
            $tempFile = $file->move($tempPath, uniqid() . '.mp4');
            
            try {
                // Khởi tạo FFmpeg
                $ffmpeg = FFMpeg::create([
                    'ffmpeg.binaries' => '/usr/bin/ffmpeg', // Đường dẫn đến ffmpeg trên server
                    'ffprobe.binaries' => '/usr/bin/ffprobe', // Đường dẫn đến ffprobe trên server
                    'timeout' => 3600, // Timeout 1 giờ
                    'ffmpeg.threads' => 12, // Số thread xử lý
                ]);
                
                // Mở video
                $video = $ffmpeg->open($tempFile->getPathname());
                
                // Lấy thông tin video
                $format = $video->getStreams()->first()->getDimensions();
                $width = $format->getWidth();
                $height = $format->getHeight();
                
                // Tính toán kích thước mới giữ nguyên tỷ lệ
                $maxDimension = 1280; // Kích thước tối đa cho chiều rộng hoặc cao
                if ($width > $height) {
                    $newWidth = min($width, $maxDimension);
                    $newHeight = round(($height / $width) * $newWidth);
                } else {
                    $newHeight = min($height, $maxDimension);
                    $newWidth = round(($width / $height) * $newHeight);
                }
                
                // Tạo format mới với bitrate thấp hơn
                $format = new X264('aac', 'libx264');
                $format->setKiloBitrate(1000); // 1Mbps
                
                // Xử lý video
                $video->resize(new Dimension($newWidth, $newHeight))
                      ->save($format, $tempPath . '/processed_' . $tempFile->getFilename());
                
                // Lưu video đã xử lý vào storage
                $path = Storage::disk('public')->putFile(
                    'stories',
                    new \Illuminate\Http\File($tempPath . '/processed_' . $tempFile->getFilename())
                );
                
                // Xóa file tạm
                unlink($tempFile->getPathname());
                unlink($tempPath . '/processed_' . $tempFile->getFilename());
                
            } catch (\Exception $e) {
                // Nếu có lỗi trong quá trình xử lý, lưu video gốc
                $path = $file->store('stories', 'public');
            }
        } else {
            // Xử lý ảnh như bình thường
            $path = $file->store('stories', 'public');
        }

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
