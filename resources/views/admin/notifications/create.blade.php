@extends('admin.layouts.master')

@section('title', 'Gửi thông báo')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Gửi thông báo đa kênh</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Thông báo</a></li>
                    <li class="breadcrumb-item active">Gửi mới</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-xxl-9">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Soạn thảo thông báo</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.notifications.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-lg-12">
                            <div>
                                <label for="title" class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" placeholder="Nhập tiêu đề thông báo" value="{{ old('title', $template->title ?? '') }}" required>
                            </div>
                        </div>
                        
                        <div class="col-lg-12">
                            <div>
                                <label for="body" class="form-label">Nội dung thông báo <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="body" name="body" rows="4" placeholder="Nhập nội dung chi tiết..." required>{{ old('body', $template->body ?? '') }}</textarea>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div>
                                <label for="audience_type" class="form-label">Đối tượng nhận <span class="text-danger">*</span></label>
                                <select class="form-select" id="audience_type" name="audience_type" required onchange="toggleTargetSelect()">
                                    @foreach($audienceTypes as $key => $label)
                                        <option value="{{ $key }}" {{ old('audience_type', $template->audience_type ?? '') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6" id="target_user_wrapper" style="display: none;">
                            <div>
                                <label for="target_id" class="form-label">Chọn người dùng</label>
                                <select class="form-select select2" id="target_id" name="target_id">
                                    <option value="">-- Chọn người dùng --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <label class="form-label">Kênh gửi thông báo <span class="text-danger">*</span></label>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="channels[]" value="database" id="channel_db" {{ in_array('database', old('channels', $template->channels ?? ['database'])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="channel_db">
                                        Website (In-app)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="channels[]" value="mail" id="channel_mail" {{ in_array('mail', old('channels', $template->channels ?? [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="channel_mail">
                                        Email
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="channels[]" value="fcm" id="channel_fcm" {{ in_array('fcm', old('channels', $template->channels ?? [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="channel_fcm">
                                        Firebase (Push)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div>
                                <label for="url" class="form-label">Đường dẫn liên kết (URL)</label>
                                <input type="url" class="form-control" id="url" name="url" placeholder="https://example.com" value="{{ old('url', $template->url ?? '') }}">
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div>
                                <label class="form-label text-muted fw-bold">Ảnh Banner (Tùy chọn)</label>
                                <div class="d-flex flex-column gap-2">
                                    <input type="hidden" id="image" name="image" value="{{ old('image', $template->image ?? '') }}">
                                    <button type="button" class="btn btn-outline-primary btn-label" onclick="openMediaPicker('image', 'imgPreviewDisplay')">
                                        <div class="d-flex align-items-center">
                                            <i class="ri-image-2-line label-icon align-middle fs-16 me-2"></i> 
                                            Chọn ảnh từ thư viện
                                        </div>
                                    </button>
                                    
                                    <div id="imagePreview" class="picker-preview-wrap mt-2 {{ old('image', $template->image ?? '') ? '' : 'd-none' }}">
                                        <div class="position-relative d-inline-block">
                                            @php
                                                $displayUrl = get_image_url(old('image', $template->image ?? ''));
                                            @endphp
                                            <img src="{{ $displayUrl }}" 
                                                 id="imgPreviewDisplay" alt="Preview" class="img-fluid rounded border shadow-sm" style="max-height: 180px; width: 100%; object-fit: cover;">
                                            <button type="button" class="btn btn-danger btn-icon btn-sm position-absolute top-0 end-0 m-2 shadow" onclick="removeNotificationImage()">
                                                <i class="ri-close-line"></i>
                                            </button>
                                        </div>
                                        <p class="text-muted small mt-1 mb-0"><i class="ri-information-line me-1"></i> Ảnh này sẽ hiển thị trong nội dung thông báo.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary btn-label px-5">
                                    <div class="d-flex align-items-center">
                                        <i class="ri-send-plane-fill label-icon align-middle fs-16 me-2"></i> Gửi thông báo ngay
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Thêm Modal Picker --}}
@include('admin.media.picker-modal')
@endsection

@push('scripts')
<script>
    function toggleTargetSelect() {
        const type = document.getElementById('audience_type').value;
        const wrapper = document.getElementById('target_user_wrapper');
        if (type === 'single') {
            wrapper.style.display = 'block';
        } else {
            wrapper.style.display = 'none';
        }
    }

    function removeNotificationImage() {
        document.getElementById('image').value = '';
        document.getElementById('imagePreview').classList.add('d-none');
        document.getElementById('imgPreviewDisplay').src = '';
    }
</script>
@endpush
