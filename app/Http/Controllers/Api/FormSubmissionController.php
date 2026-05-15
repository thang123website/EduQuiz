<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FormSubmission;
use App\Models\User;
use App\Models\Setting;
use App\Mail\AdminFormNotificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class FormSubmissionController extends Controller
{
    public function store(Request $request, $type)
    {
        // Rate limiting should be applied in routes/api.php using middleware 'throttle:3,10'

        // Basic validation
        $validatedData = $request->validate([
            'data' => 'required|array',
            'data.email' => 'nullable|email',
        ]);

        // Create submission
        $submission = FormSubmission::create([
            'type' => $type,
            'user_id' => auth('sanctum')->id(), // If authenticated
            'data' => $request->input('data'), // Use input directly to avoid Laravel stripping unvalidated fields
            'ip_address' => $request->ip(),
            'status' => 'pending',
        ]);

        // Get all admin users
        $admins = User::whereHas('role', function($q) {
            $q->where('is_admin', true);
        })->get();
        
        // Queue the mail to all admins to avoid slowing down API
        if ($admins->isNotEmpty()) {
            \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\GeneralNotification([
                'title' => 'Yêu cầu mới: ' . strtoupper($type),
                'body' => 'Hệ thống vừa nhận được một form yêu cầu mới từ người dùng.',
                'url' => route('admin.forms.index'),
                'channels' => ['database']
            ]));
            
            Mail::to($admins)->queue(new AdminFormNotificationMail($submission));
        } else {
            $fallbackEmail = Setting::get('admin_email', 'admin@eduquiz.local');
            Mail::to($fallbackEmail)->queue(new AdminFormNotificationMail($submission));
        }

        return response()->json([
            'success' => true,
            'message' => 'Your form has been submitted successfully.',
        ]);
    }
}
