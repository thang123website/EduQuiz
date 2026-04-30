@extends('admin.layouts.master')

@section('title', 'Tạo Slider mới')

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Tạo Slider mới</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.sliders.index') }}">Slider</a></li>
                        <li class="breadcrumb-item active">Tạo mới</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row justify-content-center">
        <div class="col-xxl-6">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Thông tin Slider Group</h4>
                </div><!-- end card header -->
                <div class="card-body">
                    <p class="text-muted">Vui lòng điền đầy đủ các thông tin cần thiết để tạo một nhóm slider mới.</p>
                    <div class="live-preview">
                        <form action="{{ route('admin.sliders.store') }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-lg-12">
                                    <label for="name" class="form-label fw-semibold">Tên Slider <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name') }}"
                                        placeholder="Ví dụ: Banner Trang chủ" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <!--end col-->
                                <div class="col-lg-12">
                                    <label for="key" class="form-label fw-semibold">Key định danh <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('key') is-invalid @enderror"
                                        id="key" name="key" value="{{ old('key') }}"
                                        placeholder="Ví dụ: home-main-banner" required>
                                    <div class="form-text text-muted">
                                        Chỉ dùng chữ thường, số, dấu <code>-</code> hoặc <code>_</code>. Key này dùng để gọi ở Frontend.
                                    </div>
                                    @error('key')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <!--end col-->
                                <div class="col-lg-12">
                                    <label for="description" class="form-label fw-semibold">Mô tả</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                        id="description" name="description" rows="3"
                                        placeholder="Mô tả ngắn về mục đích của slider này...">{{ old('description') }}</textarea>
                                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <!--end col-->
                                <div class="col-lg-12">
                                    <label for="status" class="form-label fw-semibold">Trạng thái</label>
                                    <select class="form-select" id="status" name="status" data-choices data-choices-search-false>
                                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Tắt</option>
                                    </select>
                                </div>
                                <!--end col-->
                                <div class="col-lg-12">
                                    <div class="hstack gap-2 justify-content-end">
                                        <a href="{{ route('admin.sliders.index') }}" class="btn btn-light">Hủy</a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ri-save-line me-1"></i> Tạo Slider
                                        </button>
                                    </div>
                                </div>
                                <!--end col-->
                            </div>
                            <!--end row-->
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Auto-generate key từ name
    document.getElementById('name').addEventListener('input', function () {
        const keyField = document.getElementById('key');
        if (!keyField.dataset.manually) {
            keyField.value = this.value
                .toLowerCase()
                .trim()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/[đĐ]/g, 'd')
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
        }
    });

    document.getElementById('key').addEventListener('input', function () {
        this.dataset.manually = 'true';
    });
</script>
@endpush
