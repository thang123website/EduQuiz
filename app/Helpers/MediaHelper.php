<?php

if (!function_exists('get_image_url')) {
    /**
     * Chuyển đổi đường dẫn ảnh lưu trong DB thành URL tuyệt đối để hiển thị
     * Hỗ trợ: URL tuyệt đối, đường dẫn bắt đầu bằng /storage/, và đường dẫn thô của disk
     *
     * @param string|null $path
     * @return string|null
     */
    function get_image_url($path)
    {
        if (empty($path)) return null;
        
        // 1. Nếu đã là URL tuyệt đối (http...)
        if (str_starts_with($path, 'http')) {
            return $path;
        }

        // 2. Nếu đã bắt đầu bằng /storage/ hoặc storage/
        if (str_starts_with($path, '/storage/') || str_starts_with($path, 'storage/')) {
            $normalizedPath = str_starts_with($path, '/') ? $path : '/' . $path;
            return asset($normalizedPath);
        }
        
        // 3. Nếu là đường dẫn thô trong disk (ví dụ 2026/04/...)
        return \Illuminate\Support\Facades\Storage::disk('public')->url($path);
    }
}
