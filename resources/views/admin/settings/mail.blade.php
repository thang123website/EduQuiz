@extends('admin.layouts.master')

@section('title', 'Cấu hình Email SMTP')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Cấu hình Email SMTP</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Hệ thống</a></li>
                    <li class="breadcrumb-item active">Cấu hình Email</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Left: SMTP Config Form --}}
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-sm bg-soft-primary rounded p-2">
                            <i class="ri-mail-settings-line fs-22 text-primary"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-0">Thông số SMTP</h5>
                        <p class="text-muted mb-0 fs-13">Cấu hình máy chủ gửi email cho hệ thống</p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-border-left alert-dismissible fade show" role="alert">
                        <i class="ri-check-double-line me-2 align-middle"></i><strong>Thành công!</strong> — {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-border-left alert-dismissible fade show" role="alert">
                        <i class="ri-error-warning-line me-2 align-middle"></i><strong>Lỗi!</strong> — {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('admin.settings.mail.update') }}" method="POST" id="smtpForm">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        {{-- Driver --}}
                        <div class="col-lg-6">
                            <label for="mail_driver" class="form-label fw-semibold">Giao thức <span class="text-danger">*</span></label>
                            <select class="form-select" id="mail_driver" name="mail_driver">
                                <option value="smtp" @selected(($mailSettings['mail_driver'] ?? 'smtp') === 'smtp')>SMTP</option>
                                <option value="sendmail" @selected(($mailSettings['mail_driver'] ?? '') === 'sendmail')>Sendmail</option>
                                <option value="log" @selected(($mailSettings['mail_driver'] ?? '') === 'log')>Log (Debug)</option>
                            </select>
                        </div>

                        {{-- Encryption --}}
                        <div class="col-lg-6">
                            <label for="mail_encryption" class="form-label fw-semibold">Mã hóa <span class="text-danger">*</span></label>
                            <select class="form-select" id="mail_encryption" name="mail_encryption">
                                <option value="tls" @selected(($mailSettings['mail_encryption'] ?? 'tls') === 'tls')>TLS (Khuyến nghị)</option>
                                <option value="ssl" @selected(($mailSettings['mail_encryption'] ?? '') === 'ssl')>SSL</option>
                                <option value="starttls" @selected(($mailSettings['mail_encryption'] ?? '') === 'starttls')>STARTTLS</option>
                            </select>
                        </div>

                        {{-- Host --}}
                        <div class="col-lg-8">
                            <label for="mail_host" class="form-label fw-semibold">SMTP Host <span class="text-danger">*</span></label>
                            <input type="text" id="mail_host" name="mail_host" class="form-control @error('mail_host') is-invalid @enderror"
                                value="{{ old('mail_host', $mailSettings['mail_host'] ?? '') }}"
                                placeholder="smtp.gmail.com">
                            @error('mail_host')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Port --}}
                        <div class="col-lg-4">
                            <label for="mail_port" class="form-label fw-semibold">Port <span class="text-danger">*</span></label>
                            <select class="form-select @error('mail_port') is-invalid @enderror" id="mail_port" name="mail_port">
                                @foreach([587 => '587 (TLS)', 465 => '465 (SSL)', 25 => '25 (SMTP)', 2525 => '2525'] as $port => $label)
                                    <option value="{{ $port }}" @selected((int)($mailSettings['mail_port'] ?? 587) === $port)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('mail_port')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Username --}}
                        <div class="col-lg-12">
                            <label for="mail_username" class="form-label fw-semibold">Username (Email) <span class="text-danger">*</span></label>
                            <input type="email" id="mail_username" name="mail_username"
                                class="form-control @error('mail_username') is-invalid @enderror"
                                value="{{ old('mail_username', $mailSettings['mail_username'] ?? '') }}"
                                placeholder="your@gmail.com">
                            @error('mail_username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Password --}}
                        <div class="col-lg-12">
                            <label for="mail_password" class="form-label fw-semibold">Mật khẩu ứng dụng (App Password)</label>
                            <div class="input-group">
                                <input type="password" id="mail_password" name="mail_password"
                                    class="form-control" autocomplete="new-password"
                                    placeholder="Để trống nếu không muốn thay đổi mật khẩu hiện tại">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="ri-eye-line" id="eyeIcon"></i>
                                </button>
                            </div>
                            <div class="form-text"><i class="ri-shield-keyhole-line me-1 text-warning"></i>Mật khẩu được mã hóa AES-256 trước khi lưu vào Database.</div>
                        </div>

                        {{-- From Address --}}
                        <div class="col-lg-8">
                            <label for="mail_from_address" class="form-label fw-semibold">Địa chỉ gửi (From Address) <span class="text-danger">*</span></label>
                            <input type="email" id="mail_from_address" name="mail_from_address"
                                class="form-control @error('mail_from_address') is-invalid @enderror"
                                value="{{ old('mail_from_address', $mailSettings['mail_from_address'] ?? '') }}"
                                placeholder="noreply@eduquiz.vn">
                            @error('mail_from_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- From Name --}}
                        <div class="col-lg-4">
                            <label for="mail_from_name" class="form-label fw-semibold">Tên người gửi <span class="text-danger">*</span></label>
                            <input type="text" id="mail_from_name" name="mail_from_name"
                                class="form-control @error('mail_from_name') is-invalid @enderror"
                                value="{{ old('mail_from_name', $mailSettings['mail_from_name'] ?? 'EduQuiz') }}"
                                placeholder="EduQuiz">
                            @error('mail_from_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4 pt-3 border-top">
                        <button type="button" class="btn btn-outline-info" id="btnTestMail">
                            <i class="ri-send-plane-line me-1"></i> Gửi email thử nghiệm
                        </button>
                        <button type="submit" class="btn btn-primary ms-auto">
                            <i class="ri-save-2-line me-1"></i> Lưu cấu hình
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Right: Guide + Status --}}
    <div class="col-xl-4">
        {{-- Status card --}}
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ri-information-line me-2 text-info"></i>Trạng thái hiện tại</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span class="text-muted fs-13">Host</span>
                        <span class="fw-semibold fs-13">{{ $mailSettings['mail_host'] ?? '—' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span class="text-muted fs-13">Port</span>
                        <span class="fw-semibold fs-13">{{ $mailSettings['mail_port'] ?? '—' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span class="text-muted fs-13">Encryption</span>
                        <span class="badge bg-success-subtle text-success">{{ strtoupper($mailSettings['mail_encryption'] ?? '—') }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span class="text-muted fs-13">From</span>
                        <span class="fw-semibold fs-13 text-truncate ms-2" style="max-width:160px">{{ $mailSettings['mail_from_address'] ?? '—' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span class="text-muted fs-13">Mật khẩu</span>
                        @if(!empty($mailSettings['mail_username'] ?? ''))
                            <span class="badge bg-success-subtle text-success"><i class="ri-lock-line me-1"></i>Đã cấu hình</span>
                        @else
                            <span class="badge bg-warning-subtle text-warning">Chưa có</span>
                        @endif
                    </li>
                </ul>
            </div>
        </div>

        {{-- Gmail guide --}}
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ri-google-line me-2 text-danger"></i>Hướng dẫn Gmail</h5>
            </div>
            <div class="card-body">
                <ol class="fs-13 text-muted ps-3 mb-0">
                    <li class="mb-2">Đăng nhập Gmail → <strong>Quản lý Tài khoản Google</strong></li>
                    <li class="mb-2">Vào <strong>Bảo mật</strong> → Bật <strong>Xác minh 2 bước</strong></li>
                    <li class="mb-2">Tìm kiếm <strong>"Mật khẩu ứng dụng"</strong> (App Password)</li>
                    <li class="mb-2">Tạo mật khẩu cho <code>Mail / Khác</code></li>
                    <li>Copy mật khẩu 16 ký tự → Dán vào ô bên trái.</li>
                </ol>
                <div class="alert alert-warning mt-3 mb-0 p-2 fs-13">
                    <i class="ri-error-warning-line me-1"></i>
                    <strong>Lưu ý:</strong> Dùng <strong>mật khẩu ứng dụng</strong>, không phải mật khẩu Gmail!
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Test Result Modal --}}
<div class="modal fade" id="testResultModal" tabindex="-1" aria-hidden="true" data-bs-focus="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-body text-center p-5">
                <div id="testResultIcon" class="fs-1 mb-3"></div>
                <h5 id="testResultTitle" class="mb-2"></h5>
                <p id="testResultMessage" class="text-muted mb-4"></p>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Toggle show/hide password
    document.getElementById('togglePassword')?.addEventListener('click', function () {
        const input = document.getElementById('mail_password');
        const icon = document.getElementById('eyeIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'ri-eye-off-line';
        } else {
            input.type = 'password';
            icon.className = 'ri-eye-line';
        }
    });

    // Test SMTP Connection
    document.getElementById('btnTestMail')?.addEventListener('click', async function () {
        const btn = this;
        const password = document.getElementById('mail_password').value;

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Đang kiểm tra...';

        const payload = {
            mail_host:         document.getElementById('mail_host').value,
            mail_port:         document.getElementById('mail_port').value,
            mail_username:     document.getElementById('mail_username').value,
            mail_password:     password,
            mail_encryption:   document.getElementById('mail_encryption').value,
            mail_from_address: document.getElementById('mail_from_address').value,
            mail_from_name:    document.getElementById('mail_from_name').value,
        };

        try {
            const res = await fetch('{{ route("admin.settings.mail.test") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify(payload),
            });
            const data = await res.json();

            const modal = new bootstrap.Modal(document.getElementById('testResultModal'));
            document.getElementById('testResultIcon').innerHTML = data.success
                ? '<i class="ri-checkbox-circle-fill text-success"></i>'
                : '<i class="ri-close-circle-fill text-danger"></i>';
            document.getElementById('testResultTitle').textContent = data.success ? 'Gửi thành công!' : 'Kết nối thất bại';
            document.getElementById('testResultMessage').textContent = data.message;
            modal.show();
        } catch (err) {
            Toastify({ text: 'Lỗi hệ thống: ' + err.message, duration: 4000, backgroundColor: '#f06548', position: 'right' }).showToast();
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="ri-send-plane-line me-1"></i> Gửi email thử nghiệm';
        }
    });
});
</script>
@endpush
