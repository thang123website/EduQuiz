<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Handle user login.
     */
    public function login(\App\Http\Requests\Api\Auth\LoginRequest $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validated();
        $username = $validated['username'];
        $password = $validated['password'];

        $isEmail = filter_var($username, FILTER_VALIDATE_EMAIL);
        $field = $isEmail ? 'email' : 'mobile';

        $user = \App\Models\User::where($field, $username)->first();

        if (!$user || !\Illuminate\Support\Facades\Hash::check($password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials.',
            ], 401);
        }

        // Pre-Authentication Checks
        if ($user->ban && $user->ban_start_at && $user->ban_end_at && now()->between($user->ban_start_at, $user->ban_end_at)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Your account is currently banned.',
            ], 403);
        }

        if ($user->status !== 'active') {
            return response()->json([
                'status' => 'error',
                'message' => 'Your account is not active.',
            ], 403);
        }

        // Generate Token
        $token = $user->createToken('api_token')->plainTextToken;

        // Post-Authentication Actions
        $user->increment('logged_count');

        \Illuminate\Support\Facades\DB::table('user_login_histories')->insert([
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful.',
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'mobile' => $user->mobile,
                    'role_id' => $user->role_id,
                    'status' => $user->status,
                    'avatar_url' => $user->avatar_url,
                ]
            ]
        ]);
    }

    /**
     * Handle user registration.
     */
    public function register(\App\Http\Requests\Api\Auth\RegisterRequest $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validated();

        $studentRole = \App\Models\Role::where('name', 'student')->first();

        $user = \App\Models\User::create([
            'name' => $validated['full_name'],
            'email' => $validated['email'] ?? null,
            'mobile' => $validated['mobile'] ?? null,
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
            'status' => 'active',
            'role_id' => $studentRole ? $studentRole->id : null,
            'role_name' => $studentRole ? $studentRole->name : null,
        ]);

        $token = $user->createToken('api_token')->plainTextToken;

        $user->increment('logged_count');

        \Illuminate\Support\Facades\DB::table('user_login_histories')->insert([
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Registration successful.',
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'mobile' => $user->mobile,
                    'role_id' => $user->role_id,
                    'status' => $user->status,
                    'avatar_url' => $user->avatar_url,
                ]
            ]
        ], 201);
    }

    /**
     * Handle user logout.
     */
    public function logout(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();

        if ($user) {
            $request->user()->currentAccessToken()->delete();

            if ($user->logged_count > 0) {
                $user->decrement('logged_count');
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully.'
        ]);
    }

    /**
     * Handle account verification.
     */
    public function verifyAccount(\App\Http\Requests\Api\Auth\VerifyAccountRequest $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validated();
        $username = $validated['username'];
        $code = $validated['code'];

        $isEmail = filter_var($username, FILTER_VALIDATE_EMAIL);
        $field = $isEmail ? 'email' : 'mobile';

        $user = \App\Models\User::where($field, $username)->first();

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found.'], 404);
        }

        $otp = \App\Models\OtpCode::where('identifier', $username)
            ->where('code', $code)
            ->where('type', 'verify_account')
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otp) {
            return response()->json(['status' => 'error', 'message' => 'Invalid or expired OTP.'], 400);
        }

        $otp->update(['is_used' => true]);
        $user->update(['status' => 'active', 'email_verified_at' => now()]);

        return response()->json(['status' => 'success', 'message' => 'Account verified successfully.']);
    }

    /**
     * Handle forgot password.
     */
    public function forgotPassword(\App\Http\Requests\Api\Auth\ForgotPasswordRequest $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validated();
        $username = $validated['username'];

        $isEmail = filter_var($username, FILTER_VALIDATE_EMAIL);
        $field = $isEmail ? 'email' : 'mobile';

        $user = \App\Models\User::where($field, $username)->first();

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found.'], 404);
        }

        $code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        \App\Models\OtpCode::create([
            'identifier' => $username,
            'code' => $code,
            'type' => 'forgot_password',
            'expires_at' => now()->addMinutes(15),
        ]);

        if ($isEmail) {
            try {
                \Illuminate\Support\Facades\Mail::to($username)->send(new \App\Mail\SendOtpMail($code));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Lỗi gửi mail OTP: ' . $e->getMessage());
                return response()->json([
                    'status' => 'error',
                    'message' => 'Hệ thống gửi thư đang gặp sự cố. Vui lòng thử lại sau.'
                ], 500);
            }
        } else {
            // SMS logic will go here
        }

        return response()->json([
            'status' => 'success',
            'message' => 'OTP sent successfully.',
        ]);
    }

    /**
     * Handle reset password.
     */
    public function resetPassword(\App\Http\Requests\Api\Auth\ResetPasswordRequest $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validated();
        $username = $validated['username'];
        $code = $validated['code'];
        $password = $validated['password'];

        $isEmail = filter_var($username, FILTER_VALIDATE_EMAIL);
        $field = $isEmail ? 'email' : 'mobile';

        $user = \App\Models\User::where($field, $username)->first();

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found.'], 404);
        }

        $otp = \App\Models\OtpCode::where('identifier', $username)
            ->where('code', $code)
            ->where('type', 'forgot_password')
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otp) {
            return response()->json(['status' => 'error', 'message' => 'Invalid or expired OTP.'], 400);
        }

        $otp->update(['is_used' => true]);
        $user->update(['password' => \Illuminate\Support\Facades\Hash::make($password)]);

        // Optionally invalidate all existing tokens
        $user->tokens()->delete();

        return response()->json(['status' => 'success', 'message' => 'Password reset successfully.']);
    }
}
