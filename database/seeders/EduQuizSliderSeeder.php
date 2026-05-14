<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Slider;
use App\Models\SliderItem;
use App\Models\MediaFolder;
use App\Models\MediaFile;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class EduQuizSliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Tìm hoặc tạo Slider Group "Home"
        $slider = Slider::firstOrCreate(
            ['key' => 'home'],
            [
                'name' => 'Home Slider',
                'description' => 'Slider chính ở trang chủ',
                'status' => 'active',
                'settings' => [
                    'autoplay' => true,
                    'delay' => 5000,
                    'loop' => true
                ]
            ]
        );

        // Xóa các slide cũ
        $slider->items()->delete();

        // 2. Tìm User Admin
        $admin = User::first();
        $authorId = $admin ? $admin->id : 1;

        // 3. Tạo thư mục Media cho Slider
        $mediaFolder = MediaFolder::firstOrCreate(
            ['slug' => 'slider'],
            ['name' => 'Slider', 'user_id' => $authorId]
        );

        // 4. Data 3 Slides chuẩn
        $slidesData = [
            [
                'title' => 'Nền tảng đánh giá năng lực toàn diện',
                'description' => 'EduQuiz giúp bạn dễ dàng tổ chức các kỳ thi trực tuyến, trắc nghiệm nhanh với hàng ngàn mẫu câu hỏi đa dạng và tính năng tự động chấm điểm thông minh.',
                'link' => '/about',
                'url' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?q=80&w=1920',
            ],
            [
                'title' => 'Thống kê và phân tích kết quả chuyên sâu',
                'description' => 'Hệ thống biểu đồ trực quan giúp giáo viên dễ dàng theo dõi tiến độ, phát hiện điểm yếu của học viên và điều chỉnh phương pháp giảng dạy kịp thời.',
                'link' => '/features',
                'url' => 'https://images.unsplash.com/photo-1531482615713-2afd69097998?q=80&w=1920',
            ],
            [
                'title' => 'Trải nghiệm thi trực tuyến mượt mà',
                'description' => 'Giao diện thân thiện, tương thích với mọi thiết bị di động. Công nghệ chống gian lận hiện đại đảm bảo tính công bằng và chính xác cao nhất cho mỗi kỳ thi.',
                'link' => '/contact',
                'url' => 'https://images.unsplash.com/photo-1501504905252-473c47e087f8?q=80&w=1920',
            ]
        ];

        echo "Đang tải 3 ảnh Slider cực đẹp từ Unsplash...\n";

        foreach ($slidesData as $index => $data) {
            $localFilename = null;
            try {
                sleep(1); // Tránh rate limit
                $response = Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0 Safari/537.36'
                ])->timeout(15)->get($data['url']);

                if ($response->successful()) {
                    $fileContent = $response->body();
                    $filename = 'slider/slide_' . Str::random(6) . '_' . time() . '.jpg';
                    Storage::disk('public')->put($filename, $fileContent);

                    // Lưu vào Media Library
                    $mediaFile = MediaFile::create([
                        'user_id' => $authorId,
                        'folder_id' => $mediaFolder->id,
                        'name' => 'home_slider_' . ($index + 1) . '.jpg',
                        'alt' => $data['title'],
                        'url' => $filename,
                        'mime_type' => 'image/jpeg',
                        'size' => strlen($fileContent),
                        'type' => 'image',
                        'visibility' => 'public',
                    ]);

                    $localFilename = $filename;
                    echo "Tải Slide " . ($index + 1) . " thành công: " . $filename . "\n";
                } else {
                    echo "Tải Slide " . ($index + 1) . " thất bại. Mã lỗi: " . $response->status() . "\n";
                }
            } catch (\Exception $e) {
                echo "Lỗi khi tải Slide " . ($index + 1) . ": " . $e->getMessage() . "\n";
            }

            // Tạo Slider Item
            SliderItem::create([
                'slider_id' => $slider->id,
                'title' => $data['title'],
                'description' => $data['description'],
                'link' => $data['link'],
                'image' => $localFilename ?: $data['url'], // Fallback nếu lỗi
                'order' => $index + 1,
                'status' => 'active',
            ]);
        }

        echo "Đã tạo xong 3 Slides!\n";
    }
}
