<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mime\Email;

class SettingController extends Controller
{
    // ─── General Website Settings ──────────────────────────────────────────────
    
    public function general()
    {
        if (Gate::has('setting.manage')) {
            Gate::authorize('setting.manage');
        }

        $settings = Setting::where('group', 'general')->get()->groupBy('group');
        // If we want more granular control, we can just pass all and handle in view
        $settings = Setting::where('group', 'general')->get();
        
        return view('admin.settings.general', compact('settings'));
    }

    // ─── Media Settings ────────────────────────────────────────────────────────

    public function index()
    {
        if (Gate::has('setting.manage')) {
            Gate::authorize('setting.manage');
        }

        $settings = Setting::where('group', 'media')->get();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        if (Gate::has('setting.manage')) {
            Gate::authorize('setting.manage');
        }

        $data = $request->except(['_token', '_method']);

        foreach ($data as $key => $value) {
            Setting::where('key', $key)->update(['value' => $value]);
        }

        Cache::forget('app_settings');

        return back()->with('success', 'Cập nhật cấu hình thành công!');
    }

    // ─── Mail Settings ─────────────────────────────────────────────────────────

    public function mailSettings()
    {
        if (Gate::has('setting.manage')) {
            Gate::authorize('setting.manage');
        }

        // Load current mail settings (decrypted via model)
        $mailSettings = Setting::getGroup('mail');

        return view('admin.settings.mail', compact('mailSettings'));
    }

    public function mailSettingsUpdate(Request $request): RedirectResponse
    {
        if (Gate::has('setting.manage')) {
            Gate::authorize('setting.manage');
        }

        $request->validate([
            'mail_host'         => 'required|string',
            'mail_port'         => 'required|integer|in:25,465,587,2525',
            'mail_username'     => 'required|email',
            'mail_encryption'   => 'required|in:tls,ssl,starttls',
            'mail_from_address' => 'required|email',
            'mail_from_name'    => 'required|string|max:100',
        ]);

        $plainFields = [
            'mail_host', 'mail_port', 'mail_username',
            'mail_encryption', 'mail_from_address', 'mail_from_name', 'mail_driver',
        ];

        foreach ($plainFields as $key) {
            if ($request->has($key)) {
                Setting::set($key, $request->input($key), 'mail', false);
            }
        }

        // Only update password if provided (avoid overwriting with empty)
        if ($request->filled('mail_password')) {
            Setting::set('mail_password', $request->input('mail_password'), 'mail', true);
        }

        // Invalidate cache so MailConfigServiceProvider picks up new values
        Cache::forget('mail_settings');

        return back()->with('success', 'Cấu hình email đã được cập nhật thành công!');
    }

    public function testMailConnection(Request $request): JsonResponse
    {
        if (Gate::has('setting.manage')) {
            Gate::authorize('setting.manage');
        }

        $request->validate([
            'mail_host'         => 'required|string',
            'mail_port'         => 'required|integer',
            'mail_username'     => 'required|email',
            'mail_password'     => 'nullable|string', // Changed from required to nullable
            'mail_from_address' => 'required|email',
            'mail_from_name'    => 'required|string',
            'mail_encryption'   => 'required|string',
        ]);

        try {
            $password = $request->mail_password;
            
            // If password is not provided in test form, pull from encrypted DB
            if (empty($password)) {
                $password = Setting::get('mail_password');
                if (empty($password)) {
                    throw new \Exception('Chưa có mật khẩu nào được lưu trong hệ thống.');
                }
            }
            // Create an isolated transport — does NOT affect global mail config
            // Senior Note: Port 465 uses SSL wrapper (Implicit TLS), 
            // while Port 587 uses STARTTLS (Explicit TLS).
            $port = (int) $request->mail_port;
            $encryption = strtolower($request->mail_encryption);
            
            // In Symfony Mailer, the 3rd param 'tls' = true means SSL wrapper (for port 465)
            $useSslWrapper = ($port === 465 || $encryption === 'ssl');

            $transport = new EsmtpTransport(
                $request->mail_host,
                $port,
                $useSslWrapper
            );
            $transport->setUsername($request->mail_username);
            $transport->setPassword($password);

            $mailer = new Mailer($transport);
            $email = (new Email())
                ->from(sprintf('%s <%s>', $request->mail_from_name, $request->mail_from_address))
                ->to(auth()->user()->email)
                ->subject('[EduQuiz] Kiểm tra kết nối SMTP thành công')
                ->text("Xin chào!\n\nHệ thống EduQuiz đã gửi email thử nghiệm thành công.\n\nThông số SMTP đang hoạt động bình thường.\n\nTrân trọng,\nHệ thống EduQuiz");

            $mailer->send($email);

            return response()->json([
                'success' => true,
                'message' => 'Email thử nghiệm đã được gửi đến ' . auth()->user()->email . ' thành công!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kết nối thất bại: ' . $e->getMessage(),
            ], 422);
        }
    }
}
