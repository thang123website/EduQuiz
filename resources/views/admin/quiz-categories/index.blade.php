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
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
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
                                <label for="icon" class="form-label">Icon (Lucide)</label>
                                <input type="text" class="form-control" id="icon" name="icon" placeholder="ri-home-line">
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
