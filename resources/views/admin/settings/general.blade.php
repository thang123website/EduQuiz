@extends('admin.layouts.master')

@section('title', 'Cấu hình Website')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Cấu hình Website</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Hệ thống</a></li>
                    <li class="breadcrumb-item active">Cấu hình Website</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        @if(session('success'))
            <div class="alert alert-success alert-border-left alert-dismissible fade show mb-4" role="alert">
                <i class="ri-check-double-line me-3 align-middle"></i> <strong>Thành công</strong> - {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="_group" value="general">
            
            <div class="row">
                <!-- Cột trái: Thông tin văn bản -->
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0"><i class="ri-information-line align-middle me-1 text-primary"></i> Thông tin chung & Liên hệ</h4>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Tên nền tảng</label>
                                    <input type="text" name="site_name" class="form-control" value="{{ \App\Models\Setting::get('site_name') }}" placeholder="Ví dụ: EduQuiz">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Email hỗ trợ</label>
                                    <input type="email" name="site_email" class="form-control" value="{{ \App\Models\Setting::get('site_email') }}" placeholder="Ví dụ: contact@eduquiz.vn">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Số điện thoại</label>
                                    <input type="text" name="site_phone" class="form-control" value="{{ \App\Models\Setting::get('site_phone') }}" placeholder="Số hotline">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Địa chỉ trụ sở</label>
                                    <textarea name="site_address" class="form-control" rows="2">{{ \App\Models\Setting::get('site_address') }}</textarea>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Thông tin bản quyền (Footer)</label>
                                    <input type="text" name="site_copyright" class="form-control" value="{{ \App\Models\Setting::get('site_copyright') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0"><i class="ri-share-line align-middle me-1 text-primary"></i> Mạng xã hội</h4>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Facebook</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-primary"><i class="ri-facebook-fill"></i></span>
                                        <input type="text" name="social_facebook" class="form-control" value="{{ \App\Models\Setting::get('social_facebook') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Youtube</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-danger"><i class="ri-youtube-fill"></i></span>
                                        <input type="text" name="social_youtube" class="form-control" value="{{ \App\Models\Setting::get('social_youtube') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Tiktok</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-dark"><i class="ri-tiktok-fill"></i></span>
                                        <input type="text" name="social_tiktok" class="form-control" value="{{ \App\Models\Setting::get('social_tiktok') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cột phải: Hình ảnh & Logo -->
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0"><i class="ri-image-line align-middle me-1 text-primary"></i> Hình ảnh thương hiệu</h4>
                        </div>
                        <div class="card-body">
                            <!-- Logo Dark -->
                            <div class="mb-4 text-center">
                                <label class="form-label d-block text-start fw-semibold">Logo (Bản tối - cho nền sáng)</label>
                                <div class="position-relative d-inline-block">
                                    <div class="avatar-lg bg-light rounded p-1" style="width: 200px; height: 80px;">
                                        <img src="{{ get_image_url(\App\Models\Setting::get('site_logo_dark')) ?: asset('assets/admin/images/logo-dark.png') }}" 
                                             id="preview_site_logo_dark" class="w-100 h-100 object-fit-contain">
                                    </div>
                                    <input type="hidden" name="site_logo_dark" id="site_logo_dark" value="{{ \App\Models\Setting::get('site_logo_dark') }}">
                                    <button type="button" class="btn btn-sm btn-primary position-absolute top-0 end-0 m-n2 rounded-circle shadow" 
                                            onclick="openMediaPicker('site_logo_dark', 'preview_site_logo_dark')">
                                        <i class="ri-pencil-fill"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Logo Light -->
                            <div class="mb-4 text-center">
                                <label class="form-label d-block text-start fw-semibold">Logo (Bản sáng - cho nền tối)</label>
                                <div class="position-relative d-inline-block">
                                    <div class="avatar-lg bg-dark rounded p-1" style="width: 200px; height: 80px;">
                                        <img src="{{ get_image_url(\App\Models\Setting::get('site_logo_light')) ?: asset('assets/admin/images/logo-light.png') }}" 
                                             id="preview_site_logo_light" class="w-100 h-100 object-fit-contain">
                                    </div>
                                    <input type="hidden" name="site_logo_light" id="site_logo_light" value="{{ \App\Models\Setting::get('site_logo_light') }}">
                                    <button type="button" class="btn btn-sm btn-primary position-absolute top-0 end-0 m-n2 rounded-circle shadow" 
                                            onclick="openMediaPicker('site_logo_light', 'preview_site_logo_light')">
                                        <i class="ri-pencil-fill"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Favicon -->
                            <div class="mb-3 text-center">
                                <label class="form-label d-block text-start fw-semibold">Favicon</label>
                                <div class="position-relative d-inline-block">
                                    <div class="avatar-md bg-light rounded p-1">
                                        <img src="{{ get_image_url(\App\Models\Setting::get('site_favicon')) ?: asset('assets/admin/images/favicon.ico') }}" 
                                             id="preview_site_favicon" class="w-100 h-100 object-fit-contain">
                                    </div>
                                    <input type="hidden" name="site_favicon" id="site_favicon" value="{{ \App\Models\Setting::get('site_favicon') }}">
                                    <button type="button" class="btn btn-sm btn-primary position-absolute top-0 end-0 m-n2 rounded-circle shadow" 
                                            onclick="openMediaPicker('site_favicon', 'preview_site_favicon')">
                                        <i class="ri-pencil-fill"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card bg-soft-info border-dashed">
                        <div class="card-body">
                            <h5 class="text-info fs-14 fw-bold"><i class="ri-lightbulb-line me-1"></i> Gợi ý</h5>
                            <p class="text-muted mb-0 fs-12">Sử dụng logo định dạng <b>PNG</b> hoặc <b>SVG</b> có nền trong suốt để hiển thị tốt nhất trên mọi giao diện.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-light px-4" onclick="window.history.back()">Hủy bỏ</button>
                        <button type="submit" class="btn btn-primary px-4 shadow-none">
                            <i class="ri-save-2-line me-1 align-bottom"></i> Lưu cấu hình
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@include('admin.media.picker-modal')

@endsection

{{-- Script section removed because we use the standard openMediaPicker from picker-modal.blade.php --}}
