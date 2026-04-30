@extends('admin.layouts.master')

@section('title', 'Cấu hình Media')

@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 text-uppercase">Cài đặt hệ thống</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Hệ thống</a></li>
                    <li class="breadcrumb-item active">Cấu hình Media</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<!-- end page title -->

<div class="row justify-content-center">
    <div class="col-xxl-9">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <div class="flex-grow-1">
                    <h4 class="card-title mb-0">Cấu hình Media</h4>
                    <p class="text-muted mb-0">Quản lý các thông số kỹ thuật và giới hạn của thư viện media</p>
                </div>
            </div><!-- end card header -->

            <div class="card-body p-4">
                @if(session('success'))
                    <div class="alert alert-success alert-border-left alert-dismissible fade show mb-4" role="alert">
                        <i class="ri-check-double-line me-3 align-middle"></i> <strong>Thành công</strong> - {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        @foreach($settings as $setting)
                            <div class="col-lg-12">
                                <div class="mb-4">
                                    <label class="form-label fw-bold d-flex align-items-center gap-2">
                                        <i class="ri-settings-4-line text-primary fs-18"></i>
                                        {{ $setting->description ?? $setting->key }}
                                    </label>
                                    
                                    @if($setting->type === 'integer')
                                        <div class="input-group">
                                            <input type="number" name="{{ $setting->key }}" class="form-control form-control-lg" value="{{ $setting->value }}" required>
                                            <span class="input-group-text bg-light">MB</span>
                                        </div>
                                    @elseif($setting->type === 'boolean')
                                        <div class="form-check form-switch form-switch-lg" dir="ltr">
                                            <input type="hidden" name="{{ $setting->key }}" value="0">
                                            <input type="checkbox" name="{{ $setting->key }}" class="form-check-input" id="switch_{{ $setting->key }}" value="1" {{ $setting->value == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label text-muted ms-2" for="switch_{{ $setting->key }}">Kích hoạt tính năng này</label>
                                        </div>
                                    @else
                                        <input type="text" name="{{ $setting->key }}" class="form-control form-control-lg" value="{{ $setting->value }}" required>
                                    @endif
                                    
                                    <div class="form-text text-muted mt-2">
                                        <i class="ri-information-line me-1"></i> 
                                        @if($setting->key === 'upload_max_size')
                                            Giá trị này giới hạn dung lượng tối đa cho mỗi tệp tin được tải lên hệ thống.
                                        @else
                                            Cấu hình kỹ thuật cho hệ thống xử lý Media.
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-light btn-lg px-4" onclick="window.history.back()">Hủy bỏ</button>
                        <button type="submit" class="btn btn-primary btn-lg px-4 shadow-none">
                            <i class="ri-save-2-line me-1 align-bottom"></i> Cập nhật thay đổi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Smooth interaction for the settings page
    document.querySelectorAll('.form-control').forEach(input => {
        input.addEventListener('focus', function() {
            this.closest('.mb-4').classList.add('focused');
        });
        input.addEventListener('blur', function() {
            this.closest('.mb-4').classList.remove('focused');
        });
    });
</script>
@endpush

