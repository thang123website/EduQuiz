<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\User;
use App\Models\MediaFolder;
use App\Models\MediaFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class EduQuizBlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Tạo Danh Mục
        $categories = [
            'Tin tức EduQuiz',
            'Kinh nghiệm ôn thi',
            'Hướng dẫn sử dụng',
            'Mẹo dành cho giáo viên',
            'Góc học tập',
        ];

        $categoryIds = [];
        foreach ($categories as $cat) {
            $category = BlogCategory::firstOrCreate(
                ['slug' => Str::slug($cat)],
                ['title' => $cat]
            );
            $categoryIds[] = $category->id;
        }

        // Lấy User admin đầu tiên
        $admin = User::first();
        $authorId = $admin ? $admin->id : 1;

        // Nội dung mẫu siêu dài
        $longContentTemplate = "
<h2>1. Giới thiệu tổng quan về phương pháp học tập 4.0</h2>
<p>Trong kỷ nguyên số, việc học tập không chỉ dừng lại ở sách giáo khoa và bảng đen. Hệ thống <strong>EduQuiz</strong> mang đến một cuộc cách mạng trong giáo dục, cho phép giáo viên và học sinh tương tác trực tiếp qua môi trường số hóa. Học sinh có thể làm bài tập mọi lúc, mọi nơi, và giáo viên có thể theo dõi tiến độ một cách tức thì.</p>
<p>Phương pháp này đã được kiểm chứng bởi nhiều chuyên gia giáo dục hàng đầu. Hàng ngàn trường học trên toàn quốc đang dần chuyển dịch sang mô hình thi trắc nghiệm trực tuyến. Điểm nổi bật nhất của EduQuiz chính là khả năng tối ưu hóa thời gian chấm điểm và phân tích kết quả một cách chi tiết nhất.</p>

<h2>2. Tại sao nên chọn nền tảng EduQuiz?</h2>
<p>EduQuiz không chỉ là một công cụ tạo đề thi, mà là một hệ sinh thái học tập toàn diện.</p>
<ul>
    <li><strong>Nhanh chóng và Tiện lợi:</strong> Giáo viên có thể Import hàng trăm câu hỏi chỉ trong 1 giây qua file Excel/CSV.</li>
    <li><strong>Bảo mật cao:</strong> Hệ thống chống gian lận, đảo câu hỏi và đáp án ngẫu nhiên cho từng học sinh.</li>
    <li><strong>Thống kê trực quan:</strong> Hệ thống biểu đồ báo cáo điểm số, tỷ lệ câu đúng/sai giúp giáo viên nắm bắt ngay lỗ hổng kiến thức của học sinh.</li>
    <li><strong>Hỗ trợ đa phương tiện:</strong> Câu hỏi có hình ảnh, âm thanh, video sống động.</li>
</ul>

<h2>3. Phân tích chuyên sâu về hiệu quả học tập</h2>
<p>Theo một nghiên cứu gần đây, những học sinh thường xuyên ôn luyện trên hệ thống EduQuiz có điểm số trung bình cao hơn 25% so với học sinh chỉ học trên giấy. Tại sao lại như vậy? Việc nhận được phản hồi ngay lập tức (instant feedback) giúp não bộ ghi nhớ lỗi sai tốt hơn, từ đó điều chỉnh và ghi nhớ kiến thức sâu sắc hơn.</p>
<p>Bên cạnh đó, áp lực phòng thi được giảm thiểu nhờ giao diện thân thiện, dễ nhìn. Học sinh làm bài như đang tham gia một trò chơi thử thách trí tuệ. Điều này đặc biệt hiệu quả với học sinh tiểu học và trung học cơ sở.</p>
<p>Ngoài ra, EduQuiz còn cung cấp tính năng phân quyền rõ ràng. Quản trị viên có thể kiểm soát toàn bộ ngân hàng câu hỏi, trong khi giáo viên bộ môn chỉ quản lý lớp học của mình. Phụ huynh cũng có thể theo dõi kết quả của con cái thông qua ứng dụng di động.</p>

<h2>4. Những tính năng nổi bật mới cập nhật</h2>
<p>Chúng tôi luôn lắng nghe phản hồi từ cộng đồng giáo viên để liên tục nâng cấp hệ thống.</p>
<ul>
    <li>Chế độ thi TOEIC chuyên nghiệp với cấu trúc 7 phần, âm thanh tích hợp sẵn.</li>
    <li>Hệ thống Tag (Thẻ) giúp phân loại câu hỏi cực kỳ chi tiết đến từng chuyên đề.</li>
    <li>Tính năng Import thông minh, tự động tải ảnh từ Pexels/Unsplash và lưu trữ nội bộ.</li>
</ul>

<h2>5. Tương lai của giáo dục trực tuyến</h2>
<p>EduQuiz cam kết tiếp tục đổi mới và mang đến những trải nghiệm tuyệt vời nhất. Chúng tôi đang tích hợp công nghệ AI (Trí tuệ nhân tạo) để có thể tự động sinh ra các câu hỏi tương tự, giúp ngân hàng đề thi luôn phong phú và không bao giờ cạn kiệt.</p>
<p>Hãy cùng EduQuiz kiến tạo nên một tương lai giáo dục tươi sáng và hiện đại hơn!</p>
        ";

        // Tạo nội dung dài hơn bằng cách lặp lại nhiều đoạn văn
        $superLongContent = $longContentTemplate . "<br><hr><br>" . str_replace("EduQuiz", "nền tảng của chúng tôi", $longContentTemplate);
        for ($i=0; $i<3; $i++) {
            $superLongContent .= "<p>Quá trình tối ưu hóa học tập vẫn đang tiếp diễn. Lợi ích từ việc học trực tuyến... (Đoạn mở rộng thứ " . ($i+1) . " với hàng nghìn chữ để bổ sung chi tiết sâu sắc hơn về tâm lý học hành vi trong quá trình làm bài thi trắc nghiệm...)</p>";
        }

        $posts = [
            ['title' => 'Bí quyết đạt điểm tuyệt đối với hệ thống thi thử EduQuiz'],
            ['title' => 'EduQuiz ra mắt tính năng Import câu hỏi bằng Excel siêu tốc'],
            ['title' => '5 lợi ích tuyệt vời khi sử dụng nền tảng EduQuiz cho trường học'],
            ['title' => 'Hướng dẫn chi tiết cách tạo ngân hàng câu hỏi chuẩn TOEIC'],
            ['title' => 'Giáo dục số hóa: Chuyển đổi từ giấy bút sang thi trắc nghiệm online'],
            ['title' => 'Cách chống gian lận cực đỉnh của hệ thống EduQuiz'],
            ['title' => 'Cập nhật phiên bản mới: Giao diện trực quan cho học sinh Tiểu học'],
            ['title' => 'Phân tích dữ liệu điểm số: Công cụ đắc lực cho Giáo viên'],
            ['title' => 'EduQuiz đồng hành cùng hàng ngàn giáo viên vượt qua đại dịch'],
            ['title' => 'Hướng dẫn sử dụng hệ thống Thẻ (Tag) để phân loại câu hỏi'],
            ['title' => 'Kinh nghiệm luyện thi trắc nghiệm Toán lớp 12 trên EduQuiz'],
            ['title' => 'Tích hợp Âm thanh và Hình ảnh vào câu hỏi thi: Xu hướng mới'],
            ['title' => 'Làm sao để học sinh hứng thú hơn với bài kiểm tra?'],
            ['title' => 'Câu chuyện thành công: Trường THPT A áp dụng EduQuiz toàn diện'],
            ['title' => 'Các lỗi thường gặp khi Import danh sách câu hỏi và cách khắc phục'],
            ['title' => 'EduQuiz hỗ trợ công thức Toán học MathJax chuẩn Quốc tế'],
            ['title' => 'Chia sẻ phương pháp học tập 4.0 dành cho sinh viên Đại học'],
            ['title' => 'Tại sao EduQuiz là lựa chọn số 1 của các Trung tâm Tiếng Anh?'],
            ['title' => 'Tự động chấm điểm và những lợi ích không tưởng'],
            ['title' => 'Kế hoạch phát triển sắp tới: Tích hợp AI sinh câu hỏi tự động'],
        ];

        // Lấy 10 ảnh random xịn từ Unsplash làm ảnh đại diện
        $imageUrls = [
            'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=800',
            'https://images.unsplash.com/photo-1509062522246-3755977927d7?w=800',
            'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?w=800',
            'https://images.unsplash.com/photo-1516321497487-e288fb19713f?w=800',
            'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=800',
            'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=800',
            'https://images.unsplash.com/photo-1427504494785-3a9ca7044f45?w=800',
            'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=800',
            'https://images.unsplash.com/photo-1488190211105-8b0e65b80b4e?w=800',
            'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=800',
        ];

        Blog::truncate();

        // Tạo thư mục Media
        $mediaFolder = MediaFolder::firstOrCreate(
            ['slug' => 'blog'],
            ['name' => 'Blog', 'user_id' => $authorId]
        );

        $localImages = [];
        echo "Đang tải 10 ảnh từ Unsplash về thư viện Media...\n";
        foreach ($imageUrls as $idx => $url) {
            try {
                sleep(1); // Tránh bị block
                $response = Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0 Safari/537.36'
                ])->timeout(15)->get($url);
                
                if ($response->successful()) {
                    $fileContent = $response->body();
                    $filename = 'blog/imported_blog_' . Str::random(6) . '_' . time() . '.jpg';
                    Storage::disk('public')->put($filename, $fileContent);

                    $mediaFile = MediaFile::create([
                        'user_id' => $authorId,
                        'folder_id' => $mediaFolder->id,
                        'name' => 'blog_image_' . ($idx + 1) . '.jpg',
                        'alt' => 'EduQuiz Blog Image ' . ($idx + 1),
                        'url' => $filename,
                        'mime_type' => 'image/jpeg',
                        'size' => strlen($fileContent),
                        'type' => 'image',
                        'visibility' => 'public',
                    ]);
                    $localImages[] = $filename;
                    echo "Tải ảnh " . ($idx + 1) . " thành công: " . $filename . "\n";
                } else {
                    $localImages[] = $url; // Fallback
                    echo "Tải ảnh " . ($idx + 1) . " thất bại (Fallback url).\n";
                }
            } catch (\Exception $e) {
                $localImages[] = $url; // Fallback
                echo "Lỗi ảnh " . ($idx + 1) . ": " . $e->getMessage() . "\n";
            }
        }

        foreach ($posts as $index => $post) {
            $catId = $categoryIds[array_rand($categoryIds)];
            $imgUrl = count($localImages) > 0 ? $localImages[$index % count($localImages)] : null;
            
            Blog::create([
                'category_id' => $catId,
                'author_id' => $authorId,
                'title' => $post['title'],
                'slug' => Str::slug($post['title']) . '-' . rand(100, 999),
                'image' => $imgUrl,
                'description' => 'Bài viết cung cấp những thông tin sâu sắc và giá trị nhất về ' . $post['title'] . '. Đọc ngay để khám phá cách tối ưu hóa việc học tập và giảng dạy cùng EduQuiz.',
                'content' => $superLongContent,
                'visit_count' => rand(100, 5000),
                'enable_comment' => true,
                'status' => 'publish',
            ]);
        }
    }
}
