@extends('admin.layouts.master')

@section('title', 'Tạo Đề thi mới')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Tạo Đề thi</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.quizzes.index') }}">Danh sách Quiz</a></li>
                    <li class="breadcrumb-item active">Tạo mới</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('admin.quizzes.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="title">Tiêu đề đề thi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" placeholder="Ví dụ: Luyện thi TOEIC Part 1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="description">Mô tả ngắn</label>
                        <textarea class="form-control" id="description" name="description" rows="5" placeholder="Mô tả về đề thi này..."></textarea>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Cấu hình chi tiết</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark" for="type">Loại bài thi <span class="text-danger">*</span></label>
                                <select class="form-select" id="type" name="type" data-choices data-choices-search-false required>
                                    <option value="full_test" selected>Full Test (Đầy đủ)</option>
                                    <option value="practice">Practice (Luyện tập)</option>
                                    <option value="minitest">Mini Test (Ngắn)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label" for="duration">Thời gian làm bài (Phút) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="duration" name="duration" value="45" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark" for="pass_mark">Điểm đạt (%) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="pass_mark" name="pass_mark" value="50" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark" for="difficulty">Độ khó <span class="text-danger">*</span></label>
                                <select class="form-select" id="difficulty" name="difficulty" data-choices data-choices-search-false required>
                                    <option value="easy">Dễ</option>
                                    <option value="medium" selected>Trung bình</option>
                                    <option value="hard">Khó</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark" for="status">Trạng thái <span class="text-danger">*</span></label>
                                <select class="form-select" id="status" name="status" data-choices data-choices-search-false required>
                                    <option value="draft" selected>Bản nháp (Draft)</option>
                                    <option value="published">Xuất bản (Published)</option>
                                    <option value="archived">Lưu trữ (Archived)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark">Nổi bật & Mới</label>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" role="switch" id="is_popular" name="is_popular" value="1">
                                    <label class="form-check-label" for="is_popular">Đánh dấu Nổi bật (Popular)</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="is_new" name="is_new" value="1" checked>
                                    <label class="form-check-label" for="is_new">Đánh dấu Mới (New)</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Phân loại & Ảnh</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="category_id" class="form-label fw-semibold text-dark">Danh mục <span class="text-danger">*</span></label>
                        <select class="form-select" id="category_id" name="category_id" data-choices data-choices-sorting-false required>
                            <option value="">Chọn danh mục</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name_prefixed ?? $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="tags" class="form-label fw-semibold text-dark">Thẻ (Tags)</label>
                        <select class="form-select" id="tags" name="tags[]" data-choices data-choices-removeItem multiple>
                            <option value="">Chọn thẻ...</option>
                            @foreach($tags as $tag)
                                <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Ảnh đại diện</label>
                        <div class="d-flex flex-column gap-2">
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control bg-light" id="thumbnail" name="thumbnail" value="{{ old('thumbnail') }}" placeholder="Đường dẫn ảnh..." readonly>
                                <button type="button" class="btn btn-primary" onclick="openMediaPicker('thumbnail', 'quizPreviewDisplay')">
                                    <i class="ri-image-2-line"></i> Chọn
                                </button>
                            </div>
                            
                            <div id="thumbnailPreview" class="picker-preview-wrap {{ old('thumbnail') ? '' : 'd-none' }}">
                                <div class="mt-2 position-relative border rounded p-1 bg-light text-center">
                                    <img src="{{ old('thumbnail') ? get_image_url(old('thumbnail')) : '' }}" id="quizPreviewDisplay" alt="Preview" class="img-fluid rounded" style="max-height: 150px; object-fit: contain;">
                                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" onclick="removeQuizImage()" title="Gỡ bỏ">
                                        <i class="ri-close-line"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Tùy chọn nâng cao (JSON Settings)</h5>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="settings[shuffle_questions]" value="1" checked>
                        <label class="form-check-label">Xáo trộn câu hỏi</label>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="settings[show_explanation]" value="1" checked>
                        <label class="form-check-label">Hiện giải thích sau khi thi</label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="settings[allow_review]" value="1" checked>
                        <label class="form-check-label">Cho phép xem lại bài làm</label>
                    </div>
                </div>
            </div>

            <div class="text-end mb-4">
                <a href="{{ route('admin.quizzes.index') }}" class="btn btn-light w-sm">Hủy bỏ</a>
                <button type="submit" class="btn btn-success w-sm">Tiếp tục</button>
            </div>
        </div>
    </div>
</form>

{{-- Tích hợp Media Manager Modal --}}
@include('admin.media.picker-modal')

@endsection

@push('styles')
<style>
    .choices__list--dropdown { z-index: 1050 !important; }
</style>
@endpush

@push('scripts')
<script>
    function removeQuizImage() {
        const input = document.getElementById('thumbnail');
        const previewWrap = document.getElementById('thumbnailPreview');
        const previewImg = document.getElementById('quizPreviewDisplay');
        
        if (input) input.value = '';
        if (previewWrap) previewWrap.classList.add('d-none');
        if (previewImg) previewImg.src = '';
    }
</script>
@endpush
