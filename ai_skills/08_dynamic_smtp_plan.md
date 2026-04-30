# Kế hoạch & Kiến trúc Cấu hình Email Động (Enterprise SMTP Strategy)

**Mục tiêu:** Xây dựng hệ thống cấu hình Email (SMTP) động cho phép Admin thay đổi thông số từ UI mà không cần sửa `.env` hay restart server. Đồng thời, kiến trúc phải đáp ứng được tải trọng lớn (10k+ concurrent users, xử lý hàng triệu email) với độ tin cậy và bảo mật tối đa.

---

## 1. Cơ sở dữ liệu & Bảo mật (Security & Schema Layer)

Bảng `settings` cần được thiết kế để lưu trữ linh hoạt nhưng vẫn an toàn tuyệt đối.

```php
Schema::create('settings', function (Blueprint $table) {
    $table->id();
    $table->string('key')->unique()->index();
    $table->text('value')->nullable();
    $table->boolean('is_encrypted')->default(false); // Dấu hiệu cho Provider biết cần giải mã
    $table->string('group')->default('general')->index(); // vd: 'mail', 'system'
    $table->timestamps();
});
```

**Nguyên tắc Bảo mật:**
Mật khẩu (`mail_password`) BẮT BUỘC phải được mã hóa trước khi lưu bằng `Crypt::encryptString()` và đánh dấu `is_encrypted = true`. Khi Service Provider nạp cấu hình, nó sẽ tự động giải mã. Điều này giúp an toàn ngay cả khi database bị dump.

---

## 2. Lõi Xử lý & Tối ưu (Performance Provider)

Đây là "Trái tim" của hệ thống cấu hình động. Thay vì gọi `Config::set` rải rác, toàn bộ logic được gom vào một `MailConfigServiceProvider` và **CHỈ KÍCH HOẠT khi hệ thống cần gửi mail** (Resolving Pattern).

```php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Mail\MailManager;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use App\Models\Setting;

class MailConfigServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->resolving(MailManager::class, function (MailManager $manager) {
            $settings = Cache::rememberForever('mail_settings', function () {
                return Setting::where('group', 'mail')->pluck('value', 'key')->toArray();
            });

            if (!empty($settings)) {
                $password = $settings['mail_password'] ?? '';
                if (!empty($password)) {
                    try {
                        $password = Crypt::decryptString($password);
                    } catch (\Exception $e) {
                        // Bỏ qua hoặc Log lỗi giải mã
                    }
                }

                Config::set('mail.mailers.smtp', array_merge(config('mail.mailers.smtp'), [
                    'host'       => $settings['mail_host'] ?? config('mail.mailers.smtp.host'),
                    'port'       => $settings['mail_port'] ?? config('mail.mailers.smtp.port'),
                    'username'   => $settings['mail_username'] ?? config('mail.mailers.smtp.username'),
                    'password'   => $password,
                    'encryption' => $settings['mail_encryption'] ?? config('mail.mailers.smtp.encryption'),
                ]));

                Config::set('mail.from', [
                    'address' => $settings['mail_from_address'] ?? config('mail.from.address'),
                    'name'    => $settings['mail_from_name'] ?? config('mail.from.name'),
                ]);
            }
        });
    }
}
```
*Ghi chú:* Cách làm này giúp Queue Worker tự động nhận diện cấu hình mới cho mỗi Job mà không cần lệnh `queue:restart`.

---

## 3. Chiến lược Hàng đợi (Queue Strategy cho 10k+ Users)

Gửi mail đồng bộ (Sync) là điều cấm kỵ với hệ thống lớn vì nó làm treo Request.
- **Tách biệt Queue Connection:** Sử dụng Redis làm Queue Driver.
- **Multiple Queues:** Chia làm các lane (làn đường) khác nhau:
  - `high`: Gửi OTP, Email kích hoạt tài khoản (Phải gửi ngay lập tức).
  - `default` / `low`: Thông báo kết quả thi, Bản tin định kỳ (Gửi dần không cần gấp).
- **Rate Limiting:** Sử dụng Middleware của Queue (vd: `RateLimited::class`) để không vượt quá quota của Google/SendGrid (tránh bị block IP).

---

## 4. API Kiểm tra Kết nối (Fail-Safe Testing)

Tuyệt đối không lưu cấu hình nếu nó bị sai. API Test Connection sẽ tạo một Transport tạm thời (On-the-fly) để gửi thử mà không ảnh hưởng đến Config tổng của hệ thống.

```php
public function testConnection(Request $request)
{
    // 1. Validation...

    try {
        $transport = new \Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport(
            $request->mail_host,
            $request->mail_port,
            $request->mail_encryption === 'tls'
        );
        $transport->setUsername($request->mail_username);
        $transport->setPassword($request->mail_password);

        $mailer = new \Symfony\Component\Mailer\Mailer($transport);
        $email = (new \Symfony\Component\Mime\Email())
            ->from($request->mail_from_address)
            ->to(auth()->user()->email)
            ->subject('EduQuiz SMTP Test')
            ->text('Kết nối SMTP thành công!');

        $mailer->send($email);

        return response()->json(['success' => true, 'message' => 'Gửi mail thử thành công!']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
```

---

## 5. Chiến lược Unit & Feature Test

Để đảm bảo hệ thống chuẩn "Senior", các logic này phải được phủ test.

```php
/** @test */
public function test_it_overrides_mail_config_from_database()
{
    // 1. Arrange: Khởi tạo dữ liệu giả
    \App\Models\Setting::create(['key' => 'mail_host', 'value' => 'smtp.test.com', 'group' => 'mail']);
    Cache::forget('mail_settings');

    // 2. Act: Kích hoạt Resolving
    app(\Illuminate\Mail\MailManager::class);

    // 3. Assert: Kiểm tra Config đã bị ghi đè chưa
    $this->assertEquals('smtp.test.com', config('mail.mailers.smtp.host'));
}
```

---

## 6. Tối ưu Nâng cao (Senior Enterprise Tips)

1. **Fail-over (Dự phòng):** Cấu hình tính năng `failover` của Laravel Mail. Nếu Gmail lỗi/hết quota, tự động chuyển sang cấu hình dự phòng (Mailgun/SendGrid).
2. **Log Monitoring (Theo dõi trạng thái):** Bắt các sự kiện `MessageSent` và `MessageFailed` để ghi vào bảng `mail_logs`. Điều này giúp tra cứu cực nhanh khi học sinh phàn nàn "không nhận được mã".
3. **Job Batching:** Khi cần gửi thông báo cho 5.000 học sinh cùng lúc, dùng `Bus::batch()` để theo dõi phần trăm (Progress bar) hoàn thành trên giao diện Admin.
