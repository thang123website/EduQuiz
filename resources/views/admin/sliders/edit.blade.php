@extends('admin.layouts.master')

@section('title', 'Chỉnh sửa Slider: ' . $slider->name)

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Chỉnh sửa Slider</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.sliders.index') }}">Slider</a></li>
                        <li class="breadcrumb-item active">{{ Str::limit($slider->name, 20) }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header border-bottom-dashed">
                    <h5 class="card-title mb-0">Cấu hình nhóm</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.sliders.update', $slider) }}" method="POST" id="sliderGroupForm">
                        @csrf @method('PUT')
                        <div class="row g-3">
                            <div class="col-lg-12">
                                <label for="name" class="form-label fw-semibold">Tên Slider <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', $slider->name) }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-lg-12">
                                <label for="key" class="form-label fw-semibold">Key định danh <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('key') is-invalid @enderror"
                                    id="key" name="key" value="{{ old('key', $slider->key) }}" required>
                                <div class="form-text mt-2">Sử dụng: <code>get_slider('{{ $slider->key }}')</code></div>
                                @error('key')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-lg-12">
                                <label for="description" class="form-label fw-semibold">Mô tả</label>
                                <textarea class="form-control" id="description" name="description" rows="2">{{ old('description', $slider->description) }}</textarea>
                            </div>
                            <div class="col-lg-12">
                                <label for="status" class="form-label fw-semibold">Trạng thái</label>
                                <select class="form-select" id="status" name="status" data-choices data-choices-search-false>
                                    <option value="active" @selected(old('status', $slider->status) === 'active')>Đang hoạt động</option>
                                    <option value="inactive" @selected(old('status', $slider->status) === 'inactive')>Tắt</option>
                                </select>
                            </div>
                            <div class="col-lg-12">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="ri-save-line me-1"></i> Lưu thay đổi
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header align-items-center d-flex border-bottom-dashed">
                    <h4 class="card-title mb-0 flex-grow-1">Danh sách Slides</h4>
                    <div class="flex-shrink-0 hstack gap-2">
                        <button type="button" id="btn-save-order-items" class="btn btn-warning btn-sm d-none">
                            <i class="ri-drag-move-line me-1"></i> Lưu thứ tự
                        </button>
                        @can('slider.update')
                        <button type="button" class="btn btn-success btn-sm" id="btn-add-item-modal">
                            <i class="ri-add-line me-1"></i> Thêm Slide
                        </button>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive table-card">
                        <table class="table align-middle">
                            <thead class="table-light text-muted">
                                <tr>
                                    <th style="width: 40px;"></th>
                                    <th style="width: 80px;">Ảnh</th>
                                    <th>Thông tin Slide</th>
                                    <th>Link</th>
                                    <th>Trạng thái</th>
                                    <th style="width: 100px;">Hành động</th>
                                </tr>
                            </thead>
                            <tbody id="sortable-items">
                                @forelse($slider->items as $item)
                                <tr data-id="{{ $item->id }}">
                                    <td class="drag-handle" style="cursor: grab;">
                                        <i class="ri-drag-move-2-line fs-16 text-muted"></i>
                                    </td>
                                    <td>
                                        <div class="avatar-md bg-light rounded p-1">
                                            @if($item->image)
                                                <img src="{{ $item->image }}" alt="" class="img-fluid d-block rounded">
                                            @else
                                                <i class="ri-image-line fs-24 text-muted d-flex align-items-center justify-content-center h-100"></i>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <h5 class="fs-14 mb-1 text-dark">{{ $item->title ?: 'Không có tiêu đề' }}</h5>
                                        <p class="text-muted mb-0 fs-12">{{ Str::limit($item->description, 50) }}</p>
                                    </td>
                                    <td>
                                        @if($item->link)
                                            <a href="{{ $item->link }}" target="_blank" class="text-primary fs-12">
                                                <i class="ri-external-link-line me-1"></i> Truy cập
                                            </a>
                                        @else
                                            <span class="text-muted fs-12">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->status === 'active')
                                            <span class="badge bg-success-subtle text-success">Hiện</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger">Ẩn</span>
                                        @endif
                                    </td>
                                    <td>
                                        <ul class="list-inline hstack gap-2 mb-0">
                                            @can('slider.update')
                                            <li class="list-inline-item">
                                                <a href="javascript:void(0);" class="text-primary d-inline-block btn-edit-item"
                                                    data-id="{{ $item->id }}"
                                                    data-item="{{ json_encode($item) }}">
                                                    <i class="ri-edit-2-line fs-16"></i>
                                                </a>
                                            </li>
                                            @endcan
                                            @can('slider.delete')
                                            <li class="list-inline-item">
                                                <a href="javascript:void(0);" class="text-danger d-inline-block btn-delete-item"
                                                    data-id="{{ $item->id }}">
                                                    <i class="ri-delete-bin-line fs-16"></i>
                                                </a>
                                            </li>
                                            @endcan
                                        </ul>
                                    </td>
                                </tr>
                                @empty
                                <tr id="empty-row">
                                    <td colspan="6" class="text-center py-5">
                                        <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                        <h5 class="mt-2">Chưa có slide nào</h5>
                                        <p class="text-muted mb-0">Nhấn nút "Thêm Slide" để bắt đầu thiết lập.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Thêm/Sửa --}}
    <div class="modal fade" id="itemModal" tabindex="-1" aria-hidden="true" data-bs-focus="false">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header bg-soft-info p-3">
                    <h5 class="modal-title" id="itemModalLabel">Thêm Slide mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="item-form-alert" class="alert alert-danger d-none"></div>
                    <div class="row g-3">
                        <div class="col-lg-12">
                            <label class="form-label fw-semibold">Ảnh Slide <span class="text-danger">*</span></label>
                            <div class="d-flex gap-3 align-items-center p-3 border border-dashed rounded">
                                <div id="img-preview-wrapper" class="bg-light rounded d-flex align-items-center justify-content-center"
                                    style="width:120px;height:80px;overflow:hidden;">
                                    <img id="img-preview" src="" alt="" class="img-fluid d-none">
                                    <i id="img-placeholder" class="ri-image-add-line fs-24 text-muted"></i>
                                </div>
                                <div>
                                    <input type="hidden" id="item-image" name="image">
                                    <button type="button" class="btn btn-primary btn-sm" onclick="openMediaPicker('item-image', 'img-preview')">
                                        <i class="ri-folder-image-line me-1"></i> Chọn từ Thư viện
                                    </button>
                                    <p class="text-muted mb-0 mt-2 fs-12">Kích thước khuyên dùng: 1920x600px. Hỗ trợ JPG, PNG.</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-8">
                            <label for="item-title" class="form-label fw-semibold">Tiêu đề Slide</label>
                            <input type="text" class="form-control" id="item-title" placeholder="Nhập tiêu đề hiển thị...">
                        </div>

                        <div class="col-lg-4">
                            <label for="item-status" class="form-label fw-semibold">Trạng thái</label>
                            <select class="form-select" id="item-status">
                                <option value="active">Hiển thị</option>
                                <option value="inactive">Ẩn</option>
                            </select>
                        </div>

                        <div class="col-lg-12">
                            <label for="item-link" class="form-label fw-semibold">Đường dẫn (Link)</label>
                            <input type="url" class="form-control" id="item-link" placeholder="https://...">
                        </div>

                        <div class="col-lg-12">
                            <label for="item-description" class="form-label fw-semibold">Mô tả ngắn</label>
                            <textarea class="form-control" id="item-description" rows="2" placeholder="Nội dung phụ trên slide..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-success" id="btn-submit-item-slider">
                        <i class="ri-save-line me-1"></i> Lưu Slide
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Xóa --}}
    <div class="modal fade flip" id="deleteItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body p-5 text-center">
                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:90px;height:90px"></lord-icon>
                    <div class="mt-4 text-center">
                        <h4>Bạn chắc chắn muốn xóa?</h4>
                        <p class="text-muted fs-14 mb-4">Hành động này không thể hoàn tác. Slide sẽ bị xóa vĩnh viễn khỏi danh sách.</p>
                        <div class="hstack gap-2 justify-content-center remove">
                            <button class="btn btn-link link-success fw-medium text-decoration-none" data-bs-dismiss="modal">
                                <i class="ri-close-line me-1 align-middle"></i> Hủy
                            </button>
                            <button class="btn btn-danger" id="btn-confirm-delete-item">Có, Xóa nó!</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Thêm Modal Picker --}}
    @include('admin.media.picker-modal')
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
    const SLIDER_ID   = {{ $slider->id }};
    const API_BASE    = '{{ route("admin.sliders.items.store", $slider->id) }}';
    const CSRF_TOKEN  = '{{ csrf_token() }}';

    let currentItemId = null;
    let itemModal     = null;
    let deleteModal   = null;
    let deleteTargetBtn = null;

    document.addEventListener('DOMContentLoaded', function () {
        itemModal   = new bootstrap.Modal(document.getElementById('itemModal'));
        deleteModal = new bootstrap.Modal(document.getElementById('deleteItemModal'));

        // Nút thêm mới
        document.getElementById('btn-add-item-modal')?.addEventListener('click', function(e) {
            e.preventDefault();
            openItemModal();
        });

        // Event Delegation cho nút sửa/xóa
        document.addEventListener('click', function(e) {
            const editBtn = e.target.closest('.btn-edit-item');
            if (editBtn) {
                e.preventDefault();
                openItemModal(editBtn.dataset.id, JSON.parse(editBtn.dataset.item));
            }

            const deleteBtn = e.target.closest('.btn-delete-item');
            if (deleteBtn) {
                e.preventDefault();
                currentItemId = deleteBtn.dataset.id;
                deleteTargetBtn = deleteBtn;
                deleteModal.show();
            }
        });

        // Xác nhận xóa trong Modal
        document.getElementById('btn-confirm-delete-item')?.addEventListener('click', function() {
            if (!currentItemId) return;
            
            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Đang xóa...';

            fetch(`${API_BASE}/${currentItemId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    deleteModal.hide();
                    deleteTargetBtn.closest('tr').remove();
                    Toastify({ text: "Đã xóa slide thành công!", duration: 3000, backgroundColor: "#f06548", position: "right" }).showToast();
                }
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = 'Có, Xóa nó!';
            });
        });

        // Nút lưu trong Modal
        document.getElementById('btn-submit-item-slider')?.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            saveItem();
        });

        // Nút lưu thứ tự
        document.getElementById('btn-save-order-items')?.addEventListener('click', function(e) {
            e.preventDefault();
            saveOrder();
        });

        // Kéo thả
        const sortableEl = document.getElementById('sortable-items');
        if (sortableEl) {
            Sortable.create(sortableEl, {
                handle: '.drag-handle',
                animation: 150,
                ghostClass: 'bg-light',
                onEnd: () => document.getElementById('btn-save-order-items')?.classList.remove('d-none')
            });
        }
    });

    function openItemModal(itemId = null, itemData = null) {
        currentItemId = itemId;
        document.getElementById('itemModalLabel').textContent = itemId ? 'Chỉnh sửa Slide' : 'Thêm Slide mới';
        
        // Reset form
        document.getElementById('item-image').value = '';
        document.getElementById('item-title').value = '';
        document.getElementById('item-link').value = '';
        document.getElementById('item-description').value = '';
        document.getElementById('item-status').value = 'active';

        document.getElementById('img-preview').src = '';
        document.getElementById('img-preview').classList.add('d-none');
        document.getElementById('img-placeholder').classList.remove('d-none');
        document.getElementById('item-form-alert').classList.add('d-none');

        if (itemData) {
            document.getElementById('item-title').value = itemData.title ?? '';
            document.getElementById('item-link').value = itemData.link ?? '';
            document.getElementById('item-description').value = itemData.description ?? '';
            document.getElementById('item-status').value = itemData.status ?? 'active';
            document.getElementById('item-image').value = itemData.image ?? '';
            
            if (itemData.image) {
                document.getElementById('img-preview').src = itemData.image;
                document.getElementById('img-preview').classList.remove('d-none');
                document.getElementById('img-placeholder').classList.add('d-none');
            }
        }
        itemModal.show();
    }

    function saveItem() {
        const image = document.getElementById('item-image').value;
        if (!image) {
            showAlert('Vui lòng chọn ảnh cho slide.');
            return;
        }

        const payload = {
            title: document.getElementById('item-title').value,
            image: image,
            link: document.getElementById('item-link').value,
            description: document.getElementById('item-description').value,
            status: document.getElementById('item-status').value,
        };

        const url = currentItemId ? `${API_BASE}/${currentItemId}` : API_BASE;
        const method = currentItemId ? 'PUT' : 'POST';

        const btn = document.getElementById('btn-submit-item-slider');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Đang lưu...';

        fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
            body: JSON.stringify(payload),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                itemModal.hide();
                location.reload();
            } else {
                showAlert(data.message || 'Có lỗi xảy ra.');
            }
        })
        .catch(err => {
            console.error('Fetch error:', err);
            showAlert('Lỗi hệ thống: Không thể kết nối tới máy chủ.');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="ri-save-line me-1"></i> Lưu Slide';
        });
    }

    function saveOrder() {
        const rows = document.querySelectorAll('#sortable-items tr[data-id]');
        const orders = Array.from(rows).map((row, index) => ({ id: row.dataset.id, order: index }));
        fetch(`${API_BASE}/reorder`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
            body: JSON.stringify({ orders }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('btn-save-order-items').classList.add('d-none');
                Toastify({ text: "Đã lưu thứ tự thành công!", duration: 3000, backgroundColor: "#0ab39c" }).showToast();
            }
        });
    }

    function showAlert(message) {
        const el = document.getElementById('item-form-alert');
        el.textContent = message;
        el.classList.remove('d-none');
    }
</script>
@endpush
