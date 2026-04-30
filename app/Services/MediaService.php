<?php

namespace App\Services;

use App\Models\MediaFile;
use App\Models\MediaFolder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\Encoders\GifEncoder;
use Intervention\Image\Encoders\WebpEncoder;

class MediaService
{
    /** Loại file được phép upload */
    const ALLOWED_MIMES = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'zip'];

    /** Các kích thước thumbnail tự động tạo */
    const THUMBNAIL_SIZES = [
        'thumb'  => [150, 150],
        'medium' => [400, 300],
    ];

    /**
     * Hàm upload file chính — xử lý toàn bộ từ validate đến lưu DB
     */
    public function handleUpload(
        UploadedFile $file,
        ?int $folderId = null
    ): array {
        // 1. Validate extension
        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, self::ALLOWED_MIMES)) {
            return ['error' => true, 'message' => 'Loại file không được phép.'];
        }

        // 2. Xác định thư mục và tên file
        $folderPath = $this->resolveFolderPath($folderId);
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeName     = Str::slug($originalName);
        $fileName     = $safeName . '-' . time() . '-' . Str::random(4) . '.' . $ext;
        $filePath     = $folderPath . '/' . $fileName;

        $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);

        // 3. Xử lý lưu trữ
        if ($isImage) {
            // Nén và resize ảnh
            $imageContent = $this->processImage($file, $ext);
            Storage::disk('public')->put($filePath, $imageContent);
            // Tạo thumbnails
            $this->generateThumbnails($file, $filePath, $ext);
        } else {
            // Lưu tệp tin thông thường
            Storage::disk('public')->putFileAs($folderPath, $file, $fileName);
        }

        // 4. Lưu metadata vào Database
        $mediaFile = MediaFile::create([
            'user_id'   => auth()->id(),
            'folder_id' => $folderId,
            'name'      => $originalName,
            'alt'       => $originalName,
            'url'       => $filePath,
            'mime_type' => $file->getMimeType(),
            'size'      => $file->getSize(),
            'type'      => $isImage ? 'image' : 'file',
            'visibility' => 'public',
        ]);

        return [
            'error' => false,
            'data'  => $mediaFile->load('folder'),
            'url'   => Storage::disk('public')->url($filePath),
        ];
    }

    /**
     * Xóa file và toàn bộ thumbnails liên quan khỏi Storage
     */
    public function deleteFile(MediaFile $mediaFile): void
    {
        // Xóa file gốc
        Storage::disk('public')->delete($mediaFile->url);

        // Xóa thumbnails
        $dir  = dirname($mediaFile->url);
        $base = pathinfo($mediaFile->url, PATHINFO_FILENAME);
        $ext  = pathinfo($mediaFile->url, PATHINFO_EXTENSION);

        foreach (array_keys(self::THUMBNAIL_SIZES) as $size) {
            $thumbPath = $dir . '/' . $base . '-' . $size . '.' . $ext;
            Storage::disk('public')->delete($thumbPath);
        }
    }

    /**
     * Xử lý ảnh: nén chất lượng 85%, giới hạn max-width 1920px
     */
    private function processImage(UploadedFile $file, string $ext): string
    {
        $manager = new ImageManager(new Driver());
        $image   = $manager->decode($file->getRealPath());

        if ($image->width() > 1920) {
            $image->scale(width: 1920);
        }

        $encoded = match ($ext) {
            'png'  => $image->encode(new PngEncoder()),
            'gif'  => $image->encode(new GifEncoder()),
            'webp' => $image->encode(new WebpEncoder(quality: 85)),
            default => $image->encode(new JpegEncoder(quality: 85)),
        };

        return (string) $encoded;
    }

    /**
     * Tạo thumbnails nhiều kích thước bằng cách crop & resize
     */
    private function generateThumbnails(UploadedFile $file, string $filePath, string $ext): void
    {
        $manager  = new ImageManager(new Driver());
        $dir      = dirname($filePath);
        $baseName = pathinfo($filePath, PATHINFO_FILENAME);

        foreach (self::THUMBNAIL_SIZES as $sizeName => [$width, $height]) {
            $image    = $manager->decode($file->getRealPath());
            $image->cover($width, $height);

            $thumbFileName = $baseName . '-' . $sizeName . '.' . $ext;
            $thumbPath     = $dir . '/' . $thumbFileName;

            $encoded = match ($ext) {
                'png'  => $image->encode(new PngEncoder()),
                'gif'  => $image->encode(new GifEncoder()),
                'webp' => $image->encode(new WebpEncoder(quality: 80)),
                default => $image->encode(new JpegEncoder(quality: 80)),
            };

            Storage::disk('public')->put($thumbPath, (string) $encoded);
        }
    }

    /**
     * Xác định đường dẫn thư mục lưu file
     */
    private function resolveFolderPath(?int $folderId): string
    {
        if (!$folderId) {
            return date('Y/m'); // VD: 2026/04
        }

        $folder = MediaFolder::find($folderId);
        return $folder ? $folder->slug : date('Y/m');
    }
}
