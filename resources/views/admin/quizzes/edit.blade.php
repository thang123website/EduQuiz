@extends('admin.layouts.master')

@section('title', 'Chỉnh sửa Đề thi: ' . $quiz->title)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Quiz Builder: {{ $quiz->title }}</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.quizzes.index') }}">Danh sách Quiz</a></li>
                    <li class="breadcrumb-item active">Chỉnh sửa</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs nav-tabs-custom nav-success nav-justified mb-3" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#general-info" role="tab">
                            <i class="ri-information-line me-1 align-bottom"></i> Thông tin chung
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#questions-builder" role="tab">
                            <i class="ri-question-line me-1 align-bottom"></i> Quản lý câu hỏi ({{ $quiz->questions->where('type', '!=', 'group')->count() }})
                        </a>
                    </li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content text-muted">
                    <div class="tab-pane active" id="general-info" role="tabpanel">
                        <form action="{{ route('admin.quizzes.update', $quiz->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="mb-3">
                                        <label class="form-label" for="title">Tiêu đề đề thi <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="title" name="title" value="{{ $quiz->title }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="description">Mô tả ngắn</label>
                                        <textarea class="form-control" id="description" name="description" rows="5">{{ $quiz->description }}</textarea>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold text-dark" for="type">Loại bài thi <span class="text-danger">*</span></label>
                                                <select class="form-select" id="type" name="type" data-choices data-choices-search-false required>
                                                    <option value="full_test" {{ $quiz->type == 'full_test' ? 'selected' : '' }}>Full Test (Đầy đủ)</option>
                                                    <option value="practice" {{ $quiz->type == 'practice' ? 'selected' : '' }}>Practice (Luyện tập)</option>
                                                    <option value="minitest" {{ $quiz->type == 'minitest' ? 'selected' : '' }}>Mini Test (Ngắn)</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold text-dark" for="duration">Thời gian (Phút)</label>
                                                <input type="number" class="form-control" id="duration" name="duration" value="{{ $quiz->duration }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold text-dark" for="pass_mark">Điểm đạt (%)</label>
                                                <input type="number" class="form-control" id="pass_mark" name="pass_mark" value="{{ $quiz->pass_mark }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="mb-3">
                                                <label class="form-label" for="difficulty">Độ khó <span class="text-danger">*</span></label>
                                                <select class="form-select" id="difficulty" name="difficulty" data-choices data-choices-search-false required>
                                                    <option value="easy" {{ $quiz->difficulty == 'easy' ? 'selected' : '' }}>Dễ</option>
                                                    <option value="medium" {{ $quiz->difficulty == 'medium' ? 'selected' : '' }}>Trung bình</option>
                                                    <option value="hard" {{ $quiz->difficulty == 'hard' ? 'selected' : '' }}>Khó</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="card border shadow-none mb-3">
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label for="category_id" class="form-label fw-semibold text-dark">Danh mục <span class="text-danger">*</span></label>
                                                <select class="form-select" id="category_id" name="category_id" data-choices data-choices-sorting-false required>
                                                    <option value="">Chọn danh mục</option>
                                                    @foreach($categories as $cat)
                                                        <option value="{{ $cat->id }}" {{ $quiz->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name_prefixed ?? $cat->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label for="tags" class="form-label fw-semibold text-dark">Thẻ (Tags)</label>
                                                @php $selectedTags = $quiz->tags->pluck('id')->toArray(); @endphp
                                                <select class="form-select" id="tags" name="tags[]" data-choices data-choices-removeItem multiple>
                                                    <option value="">Chọn thẻ...</option>
                                                    @foreach($tags as $tag)
                                                        <option value="{{ $tag->id }}" {{ in_array($tag->id, $selectedTags) ? 'selected' : '' }}>{{ $tag->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-semibold text-dark" for="status">Trạng thái <span class="text-danger">*</span></label>
                                                <select class="form-select" id="status" name="status" data-choices data-choices-search-false required>
                                                    <option value="draft" {{ $quiz->status == 'draft' ? 'selected' : '' }}>Bản nháp (Draft)</option>
                                                    <option value="published" {{ $quiz->status == 'published' ? 'selected' : '' }}>Xuất bản (Published)</option>
                                                    <option value="archived" {{ $quiz->status == 'archived' ? 'selected' : '' }}>Lưu trữ (Archived)</option>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-semibold text-dark">Nổi bật & Mới</label>
                                                <div class="form-check form-switch mb-2">
                                                    <input class="form-check-input" type="checkbox" role="switch" id="is_popular" name="is_popular" value="1" {{ $quiz->is_popular ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="is_popular">Đánh dấu Nổi bật (Popular)</label>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" role="switch" id="is_new" name="is_new" value="1" {{ $quiz->is_new ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="is_new">Đánh dấu Mới (New)</label>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-semibold text-dark">Ảnh đại diện</label>
                                                <div class="d-flex flex-column gap-2">
                                                    <div class="input-group input-group-sm">
                                                        <input type="text" class="form-control bg-light" id="thumbnail" name="thumbnail" value="{{ old('thumbnail', $quiz->thumbnail) }}" placeholder="Đường dẫn ảnh..." readonly>
                                                        @if(auth()->user()->isAdmin() || auth()->user()->hasPermission('exams.update'))
                                                        <button type="button" class="btn btn-primary" onclick="openMediaPicker('thumbnail', 'quizPreviewDisplay')">
                                                            <i class="ri-image-2-line"></i> Chọn
                                                        </button>
                                                        @endif
                                                    </div>
                                                    
                                                    <div id="thumbnailPreview" class="picker-preview-wrap {{ old('thumbnail', $quiz->thumbnail) ? '' : 'd-none' }}">
                                                        <div class="mt-2 position-relative border rounded p-1 bg-light text-center">
                                                            <img src="{{ old('thumbnail', $quiz->thumbnail) ? get_image_url(old('thumbnail', $quiz->thumbnail)) : '' }}" id="quizPreviewDisplay" alt="Preview" class="img-fluid rounded" style="max-height: 150px; object-fit: contain;">
                                                            @if(auth()->user()->isAdmin() || auth()->user()->hasPermission('exams.update'))
                                                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" onclick="removeQuizImage()" title="Gỡ bỏ">
                                                                <i class="ri-close-line"></i>
                                                            </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <hr class="my-4">

                                            @if(auth()->user()->isAdmin() || auth()->user()->hasPermission('exams.update'))
                                            <div class="mb-0">
                                                <button type="submit" class="btn btn-success w-100 shadow-sm">
                                                    <i class="ri-save-line me-1"></i> Cập nhật thông tin
                                                </button>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="tab-pane" id="questions-builder" role="tabpanel">
                        <div class="d-flex align-items-center mb-3">
                            <h5 class="flex-grow-1 mb-0">Cấu trúc Đề thi (Parts)</h5>
                            @if(auth()->user()->isAdmin() || auth()->user()->hasPermission('exams.create'))
                            <div class="flex-shrink-0">
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addPartModal">
                                    <i class="ri-add-line align-middle me-1"></i> Thêm Part Mới
                                </button>
                            </div>
                            @endif
                        </div>

                        <div id="parts-list">
                            @forelse($quiz->parts as $part)
                                <div class="card border mb-3 shadow-none part-item" data-id="{{ $part->id }}">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                                        <h6 class="mb-0 fw-semibold text-primary part-drag-handle" style="cursor: grab;"><i class="ri-drag-move-fill text-muted me-2"></i>{{ $part->title }} <span class="text-muted fs-12 ms-1">({{ $part->questions->where('type', '!=', 'group')->count() }} câu hỏi)</span></h6>
                                        <div>
                                            <button type="button" class="btn btn-sm btn-info" onclick="openBulkImport('{{ $part->id }}')">
                                                <i class="ri-upload-2-line"></i> Nhập từ Excel
                                            </button>
                                            <button type="button" class="btn btn-sm btn-success ms-1" onclick="openQuestionModal(null, '{{ $part->id }}')">
                                                <i class="ri-add-line"></i> Thêm câu hỏi
                                            </button>
                                            <button type="button" class="btn btn-sm btn-warning ms-1" onclick="openEditPartModal('{{ $part->id }}', '{{ htmlspecialchars($part->title, ENT_QUOTES) }}', '{{ htmlspecialchars($part->description, ENT_QUOTES) }}')" title="Sửa Part">
                                                <i class="ri-pencil-fill"></i>
                                            </button>
                                            <form action="{{ route('admin.quiz-parts.destroy', $part->id) }}" method="POST" class="d-inline-block ms-1" onsubmit="return confirm('Cảnh báo: Bạn có chắc muốn xóa Part này cùng TOÀN BỘ câu hỏi bên trong không?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Xóa Part"><i class="ri-delete-bin-line"></i></button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-soft-dark ms-1" data-bs-toggle="collapse" data-bs-target="#collapsePart{{ $part->id }}" aria-expanded="true" title="Thu gọn / Mở rộng">
                                                <i class="ri-arrow-up-s-line"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="collapse show" id="collapsePart{{ $part->id }}">
                                        <div class="card-body bg-white">
                                            <div class="nested-sortable">
                                                @php $qIndex = 1; @endphp
                                                @forelse($part->questions->whereNull('parent_id') as $question)
                                                    @include('admin.quizzes.partials.question-card', ['question' => $question, 'index' => $qIndex++])
                                                @empty
                                                    <div class="text-center py-3 border-dashed rounded text-muted">
                                                        Part này chưa có câu hỏi nào.
                                                    </div>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5 border rounded border-dashed">
                                    <i class="ri-list-check-2 fs-48 text-muted"></i>
                                    <h5 class="mt-2">Chưa có Part nào</h5>
                                    <p class="text-muted">Cấu trúc đề thi hiện tại yêu cầu bạn phải tạo các Part (Phần) trước khi thêm câu hỏi.<br>Ví dụ: Part 1 - Listening, Part 5 - Reading.</p>
                                    <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addPartModal">
                                        Tạo Part đầu tiên
                                    </button>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Add Part Modal --}}
<div class="modal fade" id="addPartModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm Part mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.quizzes.parts.store', $quiz->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tên Part <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="Ví dụ: Part 1: Photographs" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả thêm</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Hướng dẫn làm bài..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu Part</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Part Modal --}}
<div class="modal fade" id="editPartModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chỉnh sửa Part</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editPartForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tên Part <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="editPartTitle" class="form-control" placeholder="Ví dụ: Part 1: Photographs" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả thêm</label>
                        <textarea name="description" id="editPartDescription" class="form-control" rows="3" placeholder="Hướng dẫn làm bài..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Bulk Import Modal --}}
<div class="modal fade" id="importQuestionsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nhập câu hỏi từ Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="importQuestionsForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-4 text-center">
                        <a href="{{ asset('templates/Questions_Template.xlsx') }}" download class="btn btn-soft-success btn-sm me-2">
                            <i class="ri-file-excel-2-line align-bottom me-1"></i> Tải Mẫu Excel (.xlsx)
                        </a>
                        <a href="{{ asset('templates/Questions_Template.csv') }}" download class="btn btn-soft-info btn-sm">
                            <i class="ri-file-text-line align-bottom me-1"></i> Tải Mẫu CSV (.csv)
                        </a>
                        <p class="text-muted mt-2 mb-0 fs-12">Vui lòng tải file mẫu, điền dữ liệu đúng định dạng và tải lên lại.</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Chọn File (.xlsx, .xls, .csv) <span class="text-danger">*</span></label>
                        <input type="file" name="file" id="importExcelFile" class="form-control" accept=".xlsx, .xls, .csv" required>
                    </div>
                    <div id="importProgressContainer" class="d-none mt-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fs-12 text-muted" id="importStatusText">Đang tải và xử lý dữ liệu...</span>
                            <span class="fs-12 fw-medium" id="importPercentage">0%</span>
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" id="importProgressBar" role="progressbar" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary" id="btnImportSubmit">Bắt đầu Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Question Modal --}}
@include('admin.quizzes.partials.question-modal')

{{-- Media Picker Modal --}}
@include('admin.media.picker-modal')

@endsection

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

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">
<style>
    .question-card { border: 1px solid var(--vz-border-color); margin-bottom: 15px; border-radius: 8px; }
    .question-card .card-header { background-color: var(--vz-light); border-bottom: 1px solid var(--vz-border-color); cursor: move; }
    .option-item { background-color: var(--vz-card-bg-custom); border: 1px solid var(--vz-border-color); padding: 10px; border-radius: 6px; margin-bottom: 10px; }
    .option-item.correct { border-left: 4px solid #0ab39c; background-color: rgba(10, 179, 156, 0.1); }
    .choices__list--dropdown { z-index: 1050 !important; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="{{ asset('assets/admin/js/pages/quiz-edit.init.js') }}"></script>
@endpush
