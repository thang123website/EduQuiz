# EduQuiz API-Based Login Architecture & Implementation Plan

This document outlines the API login design for the EduQuiz website, based on scalable architecture patterns. This plan details how to implement a secure, robust authentication system optimized for a high-performance Next.js 16 frontend and a Laravel/MySQL backend.

## 1. System Architecture Overview

The authentication system will follow a **Stateless Token-Based Architecture** using **Laravel Sanctum** (Personal Access Tokens).

- **Frontend (Next.js 16)**: Manages user sessions on the client side, securely stores tokens (e.g., HttpOnly cookies), and handles UI routing (protected routes).
- **Backend (Laravel API)**: Validates credentials, issues tokens, manages user states (banned, inactive, unverified), and tracks login history.

## 2. API Endpoints Design

The backend will expose the following RESTful API endpoints for authentication.

### Core Authentication
- [ ] `POST /api/v1/auth/login`
  - **Payload**: `{ username: "email_or_phone", password: "user_password" }`
  - **Response**: JWT/Sanctum Token, User Profile (ID, Name, Role, Profile Completion Status).
- [ ] `POST /api/v1/auth/register`
  - **Payload**: `{ full_name, email/phone, password, password_confirmation }`
- [ ] `POST /api/v1/auth/logout`
  - **Headers**: `Authorization: Bearer {token}`
  - **Action**: Invalidates the current token and clears session records.

### Account Verification & Recovery
- [ ] `POST /api/v1/auth/verify-account`
  - Validates OTP or Email confirmation link.
- [ ] `POST /api/v1/auth/forgot-password`
  - Sends a reset link/OTP to the registered email or phone.
- [ ] `POST /api/v1/auth/reset-password`
  - Accepts a token and a new password to reset credentials.

### Social Login (OAuth2)
- [ ] `GET /api/v1/auth/{provider}/redirect` (Google, Facebook)
- [ ] `POST /api/v1/auth/{provider}/callback`
  - Handles the callback from the OAuth provider and generates an internal API token.

## 3. Backend Implementation Details (Laravel)

The Login Controller (`app/Http/Controllers/Api/Auth/LoginController.php`) will execute the following steps:

### A. Credential Validation
- [ ] **Determine Identity Type**: Check if the username input is an email address or a mobile phone number using regex.
- [ ] **Attempt Login**: Verify credentials against the `users` table.

### B. Pre-Authentication Checks
Before issuing a token, the system MUST verify the user's status:
- [ ] **Ban Status**: Check if `is_banned` is true and if the current time is within `ban_start_at` and `ban_end_at`. If banned, reject the login.
- [ ] **Account Activation/Verification**: Check if `status` equals `active`. If unverified, trigger the verification process (e.g., prompt for OTP).
- [ ] **Concurrent Device Limits**: Check `logged_count` against the maximum allowed devices. If the limit is exceeded, reject the login or force logout older sessions.

### C. Post-Authentication Actions
If checks pass, proceed with:
- [ ] **Generate Token**: Issue an API access token.
- [ ] **Track Session**: Save the session details in a `user_sessions` or `user_firebase_sessions` table (useful for FCM push notifications).
- [ ] **Log History**: Record the login event (IP address, User Agent, Timestamp) using a `UserLoginHistory` service.
- [ ] **Update Counter**: Increment the user's `logged_count`.

## 4. Frontend Integration (Next.js 16)

The Next.js application will securely interact with these APIs.

- **Recommended Stack**: NextAuth.js (v5) or custom React Context for auth state management.
- **Login Form**: User enters credentials. Next.js calls `POST /api/v1/auth/login`.
- **Token Storage**: Store the API token in HttpOnly, Secure Cookies to prevent XSS attacks. Avoid `localStorage` for sensitive tokens.
- **Middleware Protection**: Use Next.js Middleware (`middleware.ts`) to intercept routes like `/dashboard` or `/quiz-builder`. If no valid auth cookie is present, redirect to `/login`.
- **Axios/Fetch Interceptors**: Attach the Bearer token to all outbound API requests automatically. Handle `401 Unauthorized` responses globally to trigger a silent token refresh or force a logout.

## 5. Database Schema Requirements

To support this logic, the `users` table should have the following fields:
- `email` (string, unique, nullable)
- `mobile` (string, unique, nullable)
- `password` (string)
- `status` (enum: 'active', 'inactive', 'pending_verification')
- `ban` (boolean)
- `ban_start_at` (timestamp)
- `ban_end_at` (timestamp)
- `logged_count` (integer) - Tracks active sessions

**Related Tables:**
- `user_login_history` - Logs IP, device, timestamp.
- `user_sessions` (or `user_firebase_sessions`) - Maps user ID to active FCM/device tokens.

---

## 6. Open Questions & Configuration

> [!WARNING]
> **Token Storage Strategy**: For the Next.js frontend, will you be using NextAuth.js or managing the auth state manually via custom contexts and HttpOnly cookies? NextAuth is highly recommended for handling Social Logins (Google/Facebook) easily.

> [!IMPORTANT]
> 1. Do you want to enforce a strict 1-device limit (where logging into a new device automatically logs out the old one), or simply block the new login if the limit is reached?
> 2. Should we include two-factor authentication (2FA) in the initial design, or save it for a later phase?
