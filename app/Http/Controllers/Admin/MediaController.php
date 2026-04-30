<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaFile;
use App\Models\MediaFolder;
use App\Services\MediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MediaController extends Controller
{
    public function __construct(private MediaService $mediaService) {}

    /**
     * Trang Media Manager chính
     */
    public function index(Request $request)
    {
        Gate::authorize('media.view');

        $folderId = $request->query('folder_id');
        $search   = $request->query('search');
        $query    = MediaFile::with('folder')->latest();

        if ($folderId) {
            $query->where('folder_id', $folderId);
        }

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $files   = $query->paginate(30);
        $folders = MediaFolder::whereNull('parent_id')->with('children')->get();
        $currentFolder = $folderId ? MediaFolder::find($folderId) : null;

        return view('admin.media.index', compact('files', 'folders', 'currentFolder', 'folderId'));
    }

    /**
     * API: Upload file — trả về JSON
     */
    public function upload(Request $request)
    {
        Gate::authorize('media.upload');

        // Tăng tài nguyên để xử lý ảnh lớn
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        // Lấy cấu hình max upload size từ db (mặc định 20MB = 20480 KB)
        $maxSizeMB = \App\Models\Setting::get('max_upload_size', 20);
        $maxSizeKB = $maxSizeMB * 1024;

        $request->validate([
            'file'      => 'required|file|max:' . $maxSizeKB,
            'folder_id' => 'nullable|exists:media_folders,id',
        ]);

        $result = $this->mediaService->handleUpload(
            $request->file('file'),
            $request->input('folder_id')
        );

        if ($result['error']) {
            return response()->json(['error' => true, 'message' => $result['message']], 422);
        }

        return response()->json([
            'error' => false,
            'file'  => $result['data'],
            'url'   => $result['url'],
        ]);
    }

    /**
     * API: Lấy danh sách files để dùng trong Modal Picker
     */
    public function files(Request $request)
    {
        Gate::authorize('media.view');

        $files = MediaFile::with('folder')
            ->when($request->folder_id, fn($q) => $q->where('folder_id', $request->folder_id))
            ->when($request->search, fn($q) => $q->where('name', 'like', '%' . $request->search . '%'))
            ->latest()
            ->paginate(30);

        return response()->json($files);
    }

    /**
     * Xóa file khỏi Storage và Database
     */
    public function destroy(MediaFile $file)
    {
        Gate::authorize('media.delete');

        $this->mediaService->deleteFile($file);
        $file->delete();

        if (request()->expectsJson()) {
            return response()->json(['error' => false, 'message' => 'Đã xóa file thành công']);
        }

        return redirect()->route('admin.media.index')->with('success', 'Xóa file thành công');
    }

    /**
     * API: Xóa nhiều file cùng lúc
     */
    public function bulkDestroy(Request $request)
    {
        Gate::authorize('media.delete');

        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['error' => true, 'message' => 'Không có tệp nào được chọn'], 400);
        }

        $files = MediaFile::whereIn('id', $ids)->get();
        foreach ($files as $file) {
            $this->mediaService->deleteFile($file);
            $file->delete();
        }

        return response()->json(['error' => false, 'message' => 'Đã xóa ' . count($files) . ' tệp thành công']);
    }

    /**
     * Tạo thư mục mới
     */
    public function createFolder(Request $request)
    {
        Gate::authorize('media.upload');

        $request->validate([
            'name'      => 'required|string|max:100',
            'parent_id' => 'nullable|exists:media_folders,id',
        ]);

        $folder = MediaFolder::create([
            'user_id'   => auth()->id(),
            'name'      => $request->name,
            'slug'      => MediaFolder::generateSlug($request->name),
            'parent_id' => $request->parent_id,
        ]);

        return response()->json(['error' => false, 'folder' => $folder]);
    }
}
