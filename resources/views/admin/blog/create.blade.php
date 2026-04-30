@extends('admin.layouts.master')

@section('title', 'Thêm bài viết')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Thêm bài viết mới</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.blog.index') }}">Bài viết</a></li>
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

    <form action="{{ route('admin.blog.store') }}" method="POST">
        @csrf
        <div class="row">
            <!-- Cột trái: nội dung chính -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Nội dung bài viết</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" placeholder="Nhập tiêu đề bài viết..." required>
                        </div>
                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug <span class="text-muted fs-12">(Tự tạo nếu để trống)</span></label>
                            <input type="text" class="form-control" id="slug" name="slug" value="{{ old('slug') }}" placeholder="tieu-de-bai-viet">
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Mô tả ngắn</label>
                            <textarea class="form-control" id="description" name="description" rows="3" placeholder="Mô tả ngắn hiển thị ở trang danh sách...">{{ old('description') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="content" class="form-label">Nội dung <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="content" name="content" rows="15" placeholder="Nhập nội dung bài viết...">{{ old('content') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cột phải: thiết lập -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Thiết lập xuất bản</h5>
                    </div>
                    <div class="card-body">
                        <!-- Ảnh đại diện -->
                        <div class="mb-3">
                            <label class="form-label">Ảnh đại diện</label>
                            <div class="d-flex flex-column gap-2">
                                <input type="hidden" id="image" name="image" value="{{ old('image') }}">
                                <button type="button" class="btn btn-outline-primary" onclick="openMediaPicker('image', 'imgPreviewDisplay')">
                                    <i class="ri-image-2-line me-1"></i> Chọn ảnh đại diện
                                </button>
                                <div id="imagePreview" class="picker-preview-wrap {{ old('image') ? '' : 'd-none' }}">
                                    <img src="{{ old('image') }}" id="imgPreviewDisplay" alt="Preview" class="img-fluid rounded border" style="max-height: 200px;">
                                    <button type="button" class="btn btn-sm btn-danger mt-1 d-block w-100" onclick="removeImage()">
                                        <i class="ri-delete-bin-line me-1"></i> Xóa ảnh
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Danh mục -->
                        <div class="mb-3">
                            <label class="form-label" for="category_id">Danh mục</label>
                            <select class="form-select" id="category_id" name="category_id">
                                <option value="">— Không có danh mục —</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        @if($category->level > 0)
                                            {{ str_repeat('— ', $category->level) }}
                                        @endif
                                        {{ $category->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Trạng thái -->
                        <div class="mb-3">
                            <label class="form-label" for="blog_status">Trạng thái</label>
                            <select class="form-select" id="blog_status" name="status">
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                                <option value="publish" {{ old('status') == 'publish' ? 'selected' : '' }}>Xuất bản ngay</option>
                            </select>
                        </div>

                        <!-- Cho phép bình luận -->
                        <div class="mb-3">
                            <label class="form-label d-block">Bình luận</label>
                            <div class="form-check form-switch form-switch-custom form-switch-success">
                                <input class="form-check-input" type="checkbox" role="switch" id="enable_comment" name="enable_comment" value="1" {{ old('enable_comment', '1') ? 'checked' : '' }}>
                                <label class="form-check-label" for="enable_comment">Cho phép bình luận</label>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex gap-2">
                        <a href="{{ route('admin.blog.index') }}" class="btn btn-light flex-grow-1">Hủy bỏ</a>
                        <button type="submit" class="btn btn-success flex-grow-1">
                            <i class="ri-add-line me-1"></i> Thêm mới
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- Thêm Modal Picker --}}
    @include('admin.media.picker-modal')
@endsection

@push('scripts')
<script>
    function removeImage() {
        document.getElementById('image').value = '';
        document.getElementById('imagePreview').classList.add('d-none');
        document.getElementById('imgPreviewDisplay').src = '';
    }
</script>
@endpush
