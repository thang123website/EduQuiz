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
                            <i class="ri-question-line me-1 align-bottom"></i> Quản lý câu hỏi ({{ $quiz->questions_count ?? $quiz->questions->count() }})
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
                                                <select class="form-select" id="category_id" name="category_id" data-choices required>
                                                    <option value="">Chọn danh mục</option>
                                                    @foreach($categories as $cat)
                                                        <option value="{{ $cat->id }}" {{ $quiz->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
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
                            <h5 class="flex-grow-1 mb-0">Danh sách câu hỏi</h5>
                            @if(auth()->user()->isAdmin() || auth()->user()->hasPermission('exams.create'))
                            <div class="flex-shrink-0">
                                <button type="button" class="btn btn-success btn-sm" onclick="openQuestionModal()">
                                    <i class="ri-add-line align-middle me-1"></i> Thêm câu hỏi
                                </button>
                                <button type="button" class="btn btn-info btn-sm" onclick="openBulkImport()">
                                    <i class="ri-upload-2-line align-middle me-1"></i> Nhập từ Excel
                                </button>
                            </div>
                            @endif
                        </div>

                        <div id="questions-list" class="nested-sortable">
                            @forelse($quiz->questions as $question)
                                @include('admin.quizzes.partials.question-card', ['question' => $question, 'index' => $loop->iteration])
                            @empty
                                <div class="text-center py-5 border rounded border-dashed">
                                    <i class="ri-questionnaire-line fs-48 text-muted"></i>
                                    <h5 class="mt-2">Chưa có câu hỏi nào</h5>
                                    <p class="text-muted">Nhấp vào nút "Thêm câu hỏi" để bắt đầu xây dựng nội dung.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
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
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    var mdeEditor;
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Markdown Editor for Modal
        mdeEditor = new SimpleMDE({ element: document.getElementById("question-content-editor") });

        // Initialize Sortable
        var el = document.getElementById('questions-list');
        if (el) {
            Sortable.create(el, {
                handle: '.card-header',
                animation: 150,
                onEnd: function() {
                    // logic to save order via AJAX
                }
            });
        }
    });

    function openQuestionModal(questionId = null) {
        if (!questionId) {
            // Thêm sẵn 2 đáp án mặc định cho câu hỏi mới
            const container = document.getElementById('options-container');
            if (container && container.children.length === 0) {
                addOption('', false);
                addOption('', false);
            }
        }
        const modal = new bootstrap.Modal(document.getElementById('questionModal'));
        modal.show();
    }

    function openBulkImport() {
        Swal.fire({
            title: 'Tính năng đang phát triển',
            text: 'Chức năng nhập câu hỏi từ Excel sẽ sớm được ra mắt!',
            icon: 'info',
            confirmButtonClass: 'btn btn-primary w-xs mt-2',
            buttonsStyling: false
        });
    }
</script>
@endpush
