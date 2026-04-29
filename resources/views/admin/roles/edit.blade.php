@extends('admin.layouts.master')

@section('title', 'Chỉnh sửa Role')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Chỉnh sửa Phân quyền</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Phân quyền</a></li>
                        <li class="breadcrumb-item active">Chỉnh sửa</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.roles.update', $role) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Thông tin cơ bản</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="name" class="form-label text-muted text-uppercase fw-semibold">Mã vai trò (Key) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $role->name) }}" placeholder="VD: editor, manager..." required>
                        </div>
                        <div class="mb-3">
                            <label for="caption" class="form-label text-muted text-uppercase fw-semibold">Tên hiển thị <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="caption" name="caption" value="{{ old('caption', $role->caption) }}" placeholder="VD: Biên tập viên" required>
                        </div>
                        <div class="form-check form-switch form-switch-custom form-switch-primary mb-3" dir="ltr">
                            <input type="checkbox" class="form-check-input" id="is_admin" name="is_admin" value="1" {{ old('is_admin', $role->is_admin) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="is_admin">Là Quản trị viên (Admin)</label>
                            <div class="text-muted fs-12 mt-1">Admin có toàn quyền mà không cần xét các quyền chi tiết.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8" id="permissions-card" style="{{ old('is_admin', $role->is_admin) ? 'opacity: 0.5; pointer-events: none;' : '' }}">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h5 class="card-title mb-0 flex-grow-1">Thiết lập Quyền hạn chi tiết</h5>
                        <div class="text-muted fs-12">Gạt công tắc để cấp quyền</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($sections as $group => $items)
                                <div class="col-md-6 mb-4 permission-group">
                                    <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-3">
                                        <h6 class="fs-13 fw-bold text-primary mb-0 text-uppercase"><i class="ri-shield-user-fill me-1"></i> {{ $group }}</h6>
                                        <div class="form-check form-switch form-switch-sm">
                                            <input class="form-check-input select-all-group" type="checkbox" role="switch">
                                            <label class="form-check-label fs-11 text-muted">Chọn tất cả</label>
                                        </div>
                                    </div>
                                    
                                    @foreach($items as $section)
                                        <div class="d-flex align-items-center justify-content-between mb-2 pb-1">
                                            <label class="form-check-label text-dark fw-medium mb-0" for="perm_{{ $section->id }}">
                                                {{ $section->caption }}
                                            </label>
                                            <div class="form-check form-switch form-switch-custom form-switch-success">
                                                @php
                                                    $isChecked = false;
                                                    if (old('_token')) {
                                                        $isChecked = is_array(old('permissions')) && in_array($section->id, old('permissions'));
                                                    } else {
                                                        $isChecked = in_array($section->id, $rolePermissions);
                                                    }
                                                @endphp
                                                <input class="form-check-input permission-checkbox" type="checkbox" id="perm_{{ $section->id }}" name="permissions[]" value="{{ $section->id }}" {{ $isChecked ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12 text-end">
                <a href="{{ route('admin.roles.index') }}" class="btn btn-light btn-label waves-effect waves-light me-2"><i class="ri-arrow-go-back-line label-icon align-middle fs-16 me-2"></i> Hủy bỏ</a>
                <button type="submit" class="btn btn-success btn-label waves-effect waves-light"><i class="ri-check-double-line label-icon align-middle fs-16 me-2"></i> Cập nhật vai trò</button>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    // Xử lý ẩn hiện phần phân quyền khi là Admin
    document.getElementById('is_admin').addEventListener('change', function() {
        const card = document.getElementById('permissions-card');
        if(this.checked) {
            card.style.opacity = '0.5';
            card.style.pointerEvents = 'none';
        } else {
            card.style.opacity = '1';
            card.style.pointerEvents = 'auto';
        }
    });

    // Xử lý Chọn tất cả cho từng nhóm
    document.querySelectorAll('.select-all-group').forEach(function(toggle) {
        toggle.addEventListener('change', function() {
            const group = this.closest('.permission-group');
            const checkboxes = group.querySelectorAll('.permission-checkbox');
            checkboxes.forEach(cb => {
                cb.checked = this.checked;
            });
        });
    });

    // Cập nhật trạng thái nút "Chọn tất cả" khi các checkbox con thay đổi
    function updateSelectAllStatus() {
        document.querySelectorAll('.permission-group').forEach(function(group) {
            const checkboxes = group.querySelectorAll('.permission-checkbox');
            const selectAll = group.querySelector('.select-all-group');
            const checkedCount = group.querySelectorAll('.permission-checkbox:checked').length;
            
            selectAll.checked = (checkedCount === checkboxes.length);
        });
    }

    document.querySelectorAll('.permission-checkbox').forEach(function(cb) {
        cb.addEventListener('change', updateSelectAllStatus);
    });

    // Chạy lần đầu khi load trang
    updateSelectAllStatus();
</script>
@endpush
