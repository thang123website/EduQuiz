@extends('admin.layouts.master')

@section('title', 'Quản lý Danh mục Quiz')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Danh mục Quiz</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Danh mục Quiz</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Cột trái: Cây danh mục -->
    <div class="col-xl-7">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Cấu trúc danh mục</h4>
                <div class="flex-shrink-0">
                    <button type="button" class="btn btn-soft-info btn-sm" onclick="location.reload()">
                        <i class="ri-refresh-line align-middle"></i> Làm mới
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div id="category-tree" class="nested-list nested-sortable">
                    @forelse($categories as $category)
                        @include('admin.quiz-categories.partials.category-item', ['category' => $category])
                    @empty
                        <div class="text-center py-4">
                            <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:75px;height:75px"></lord-icon>
                            <h5 class="mt-2">Chưa có danh mục nào</h5>
                            <p class="text-muted">Hãy tạo danh mục đầu tiên ở bảng bên cạnh.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Cột phải: Form Thêm/Sửa -->
    <div class="col-xl-5">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0" id="form-title">Thêm danh mục mới</h4>
            </div>
            <div class="card-body">
                <form id="category-form" action="{{ route('admin.quiz-categories.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="form-method" value="POST">
                    <input type="hidden" name="id" id="category-id">

                    <div class="mb-3">
                        <label for="name" class="form-label">Tên danh mục <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Nhập tên danh mục" required>
                    </div>

                    <div class="mb-3">
                        <label for="parent_id" class="form-label">Danh mục cha</label>
                        <select class="form-select" id="parent_id" name="parent_id">
                            <option value="">Gốc (Root)</option>
                            @foreach($allCategories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name_prefixed ?? $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="type" class="form-label">Loại <span class="text-danger">*</span></label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="academic">Học thuật (K12)</option>
                                    <option value="toeic">Luyện thi TOEIC</option>
                                    <option value="ielts">Luyện thi IELTS</option>
                                    <option value="other">Khác</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="icon" class="form-label">Icon</label>
                                <select class="form-select" id="icon" name="icon">
                                    <option value="">-- Không chọn / Mặc định --</option>
                                    
                                    <optgroup label="Chứng chỉ & Ngoại ngữ">
                                        <option value="ri-english-input">Tiếng Anh / TOEIC / IELTS</option>
                                        <option value="ri-global-line">TOEFL / Ngoại ngữ chung</option>
                                        <option value="ri-translate">Dịch thuật / Đa ngôn ngữ</option>
                                        <option value="ri-font-color">Ngữ pháp / Từ vựng</option>
                                        <option value="ri-character-recognition-line">Tiếng Nhật / Trung / Hàn</option>
                                    </optgroup>

                                    <optgroup label="Môn học (Subjects)">
                                        <option value="ri-calculator-line">Toán học (Math)</option>
                                        <option value="ri-functions">Đại số / Giải tích</option>
                                        <option value="ri-compasses-2-line">Hình học</option>
                                        <option value="ri-quill-pen-line">Ngữ Văn / Tiếng Việt</option>
                                        <option value="ri-book-open-line">Đọc hiểu / Văn học</option>
                                        <option value="ri-flask-line">Hóa học (Chemistry)</option>
                                        <option value="ri-test-tube-line">Thí nghiệm Hóa học</option>
                                        <option value="ri-magnet-line">Vật lý (Physics)</option>
                                        <option value="ri-flashlight-line">Quang học / Điện (Vật lý)</option>
                                        <option value="ri-microscope-line">Sinh học (Biology)</option>
                                        <option value="ri-leaf-line">Thực vật / Sinh thái (Sinh)</option>
                                        <option value="ri-earth-line">Địa lý (Geography)</option>
                                        <option value="ri-map-2-line">Bản đồ (Địa lý)</option>
                                        <option value="ri-history-line">Lịch sử (History)</option>
                                        <option value="ri-ancient-pavilion-line">Di tích / Lịch sử</option>
                                        <option value="ri-computer-line">Tin học / IT</option>
                                        <option value="ri-code-box-line">Lập trình</option>
                                        <option value="ri-user-heart-line">Giáo dục công dân</option>
                                        <option value="ri-palette-line">Mỹ thuật</option>
                                        <option value="ri-music-2-line">Âm nhạc</option>
                                        <option value="ri-run-line">Thể dục / Thể thao</option>
                                    </optgroup>

                                    <optgroup label="Cấp học & Khối Lớp">
                                        <option value="ri-backpack-line">Tiểu học (Lớp 1-5)</option>
                                        <option value="ri-building-4-line">Trung học cơ sở (Lớp 6-9)</option>
                                        <option value="ri-graduation-cap-line">Trung học phổ thông (Lớp 10-12)</option>
                                        <option value="ri-number-1">Lớp 1</option>
                                        <option value="ri-number-2">Lớp 2</option>
                                        <option value="ri-number-3">Lớp 3</option>
                                        <option value="ri-number-4">Lớp 4</option>
                                        <option value="ri-number-5">Lớp 5</option>
                                        <option value="ri-number-6">Lớp 6</option>
                                        <option value="ri-number-7">Lớp 7</option>
                                        <option value="ri-number-8">Lớp 8</option>
                                        <option value="ri-number-9">Lớp 9</option>
                                        <option value="ri-bookmark-line">Lớp 10</option>
                                        <option value="ri-bookmark-2-line">Lớp 11</option>
                                        <option value="ri-bookmark-3-line">Lớp 12</option>
                                    </optgroup>

                                    <optgroup label="Giáo dục & Học tập Chung">
                                        <option value="ri-book-read-line">Sách đang đọc</option>
                                        <option value="ri-book-2-line">Sách</option>
                                        <option value="ri-pencil-ruler-2-line">Dụng cụ học tập</option>
                                        <option value="ri-draft-line">Bản nháp/Đề thi</option>
                                        <option value="ri-file-text-line">Tài liệu</option>
                                        <option value="ri-file-list-3-line">Danh sách/Bài tập</option>
                                        <option value="ri-file-paper-2-line">Giấy thi</option>
                                    </optgroup>

                                    <optgroup label="Đánh giá & Câu hỏi">
                                        <option value="ri-questionnaire-line">Bảng câu hỏi (Quiz)</option>
                                        <option value="ri-question-answer-line">Hỏi đáp</option>
                                        <option value="ri-checkbox-circle-line">Đánh dấu đúng</option>
                                        <option value="ri-close-circle-line">Đánh dấu sai</option>
                                        <option value="ri-medal-line">Huy chương / Khen thưởng</option>
                                        <option value="ri-trophy-line">Cúp vàng</option>
                                        <option value="ri-timer-line">Thời gian / Đếm ngược</option>
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch form-switch-md">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                            <label class="form-check-label" for="is_active">Hoạt động</label>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="button" id="btn-reset" class="btn btn-light me-1 d-none">Hủy bỏ</button>
                        <button type="submit" class="btn btn-primary">Lưu thông tin</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .nested-list { list-style: none; padding-left: 0; }
    .nested-list .list-group-item { margin-bottom: 8px; border-radius: 6px !important; border: 1px solid #e9ebec; }
    .nested-list .list-group-item:hover { background-color: #f3f6f9; }
    .nested-list .children { padding-left: 30px; margin-top: 8px; }
    .handle { cursor: move; margin-right: 10px; color: #adb5bd; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle Edit Click
        document.querySelectorAll('.edit-item').forEach(btn => {
            btn.addEventListener('click', function() {
                const data = JSON.parse(this.dataset.item);
                
                document.getElementById('form-title').innerText = 'Chỉnh sửa: ' + data.name;
                document.getElementById('form-method').value = 'PUT';
                document.getElementById('category-form').action = PATH_ROOT + '/admin/quiz-categories/' + data.id;
                
                document.getElementById('category-id').value = data.id;
                document.getElementById('name').value = data.name;
                document.getElementById('parent_id').value = data.parent_id || '';
                document.getElementById('type').value = data.type;
                document.getElementById('icon').value = data.icon || '';
                document.getElementById('is_active').checked = data.is_active == 1;
                
                document.getElementById('btn-reset').classList.remove('d-none');
            });
        });

        // Handle Reset
        document.getElementById('btn-reset').addEventListener('click', function() {
            document.getElementById('category-form').reset();
            document.getElementById('form-title').innerText = 'Thêm danh mục mới';
            document.getElementById('form-method').value = 'POST';
            document.getElementById('category-form').action = "{{ route('admin.quiz-categories.store') }}";
            this.classList.add('d-none');
        });
    });
</script>
@endpush
