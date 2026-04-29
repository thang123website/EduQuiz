@extends('admin.layouts.master')

@section('title', 'Thêm mới Role')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Thêm mới Phân quyền</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Phân quyền</a></li>
                        <li class="breadcrumb-item active">Thêm mới</li>
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

    <form action="{{ route('admin.roles.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Thông tin cơ bản</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Mã vai trò (Key) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="VD: editor, manager..." required>
                        </div>
                        <div class="mb-3">
                            <label for="caption" class="form-label">Tên hiển thị <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="caption" name="caption" value="{{ old('caption') }}" placeholder="VD: Biên tập viên" required>
                        </div>
                        <div class="form-check form-switch form-switch-md mb-3" dir="ltr">
                            <input type="checkbox" class="form-check-input" id="is_admin" name="is_admin" value="1" {{ old('is_admin') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_admin">Là Quản trị viên (Admin)</label>
                            <div class="text-muted fs-12 mt-1">Admin có toàn quyền mà không cần xét các quyền chi tiết.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8" id="permissions-card" style="{{ old('is_admin') ? 'opacity: 0.5; pointer-events: none;' : '' }}">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Thiết lập Quyền hạn chi tiết</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($sections as $group => $items)
                                <div class="col-md-6 mb-4">
                                    <h6 class="fs-14 fw-bold text-primary mb-3 text-uppercase border-bottom pb-2">{{ $group }}</h6>
                                    @foreach($items as $section)
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold text-dark">{{ $section->caption }}</label>
                                                <div class="form-check form-check-outline form-check-success">
                                                    <input class="form-check-input" type="checkbox" id="perm_{{ $section->id }}" name="permissions[]" value="{{ $section->id }}"
                                                    {{ is_array(old('permissions')) && in_array($section->id, old('permissions')) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="perm_{{ $section->id }}">
                                                        Cho phép
                                                    </label>
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
                <a href="{{ route('admin.roles.index') }}" class="btn btn-light me-2">Hủy bỏ</a>
                <button type="submit" class="btn btn-success">Lưu vai trò</button>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    document.getElementById('is_admin').addEventListener('change', function() {
        if(this.checked) {
            document.getElementById('permissions-card').style.opacity = '0.5';
            document.getElementById('permissions-card').style.pointerEvents = 'none';
        } else {
            document.getElementById('permissions-card').style.opacity = '1';
            document.getElementById('permissions-card').style.pointerEvents = 'auto';
        }
    });
</script>
@endpush
