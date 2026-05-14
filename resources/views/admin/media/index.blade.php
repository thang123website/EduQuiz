@extends('admin.layouts.master')

@section('title', 'Thư viện Media')

@push('styles')
<link href="{{ asset('assets/admin/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<style>
:root {
    --vz-primary-gradient: linear-gradient(135deg, #405189 0%, #0ab39c 100%);
}
.media-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 24px; }
@media (max-width: 575.98px) {
    .media-grid { grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 12px; }
    .media-card img, .file-icon-wrapper { height: 120px; }
    .media-card .media-name { font-size: 11px; }
}
.media-card { position: relative; border-radius: 16px; overflow: hidden; border: 1px solid rgba(0,0,0,0.05); cursor: pointer; transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1); background: #fff; box-shadow: 0 4px 6px rgba(0,0,0,0.02); }
.media-card:hover { transform: translateY(-8px); box-shadow: 0 12px 24px rgba(0,0,0,0.08); border-color: rgba(64,81,137,0.2); }
.media-card img { width: 100%; height: 160px; object-fit: cover; display: block; border-bottom: 1px solid rgba(0,0,0,0.05); }
.media-card .media-info { padding: 15px; background: #fff; }
.media-card .media-name { font-size: 13px; font-weight: 600; color: #2a2e34; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 6px; }
.media-card .media-size { font-size: 11px; color: #878a99; font-weight: 500; }
.media-card .media-actions { position: absolute; top: 12px; right: 12px; display: flex; gap: 8px; opacity: 0; transition: all 0.3s ease; z-index: 2; transform: translateY(-5px); }
.media-card:hover .media-actions { opacity: 1; transform: translateY(0); }
.media-card .btn-action { background: rgba(255,255,255,0.9); backdrop-filter: blur(4px); border: 1px solid rgba(0,0,0,0.05); width: 34px; height: 34px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 15px; color: #495057; transition: all 0.2s; }
.media-card .btn-action:hover { background: #fff; color: #405189; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
.media-card .btn-delete:hover { color: #f06548; }

.file-icon-wrapper { height: 160px; display: flex; align-items: center; justify-content: center; background: #f3f6f9; border-bottom: 1px solid rgba(0,0,0,0.05); }

.folder-list { display: flex; flex-direction: column; gap: 4px; }
.folder-item { display: flex; align-items: center; gap: 12px; padding: 10px 16px; border-radius: 8px; cursor: pointer; color: var(--vz-body-color); transition: all 0.2s; font-weight: 500; }
.folder-item:hover { background: var(--vz-light); color: var(--vz-primary); }
.folder-item.active { background: var(--vz-primary-bg-subtle); color: var(--vz-primary); }
.folder-item i { font-size: 18px; opacity: 0.8; }

.drop-zone { border: 2px dashed #d1d5db; border-radius: 20px; padding: 40px 20px; text-align: center; transition: all 0.3s; background: #ffffff; cursor: pointer; position: relative; overflow: hidden; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02); }
.drop-zone:hover, .drop-zone.drag-over { border-color: #405189; background: #f8f9ff; }
.drop-zone .avatar-title { transition: all 0.3s ease; }
.drop-zone:hover .avatar-title { transform: scale(1.1); background: var(--vz-primary-gradient) !important; color: #fff !important; }

.media-card .media-checkbox { position: absolute; top: 12px; left: 12px; z-index: 3; opacity: 0; transition: opacity 0.2s; }
.media-card:hover .media-checkbox, .media-card.selected .media-checkbox { opacity: 1; }
.media-card.selected { border-color: var(--vz-primary); box-shadow: 0 0 0 2px var(--vz-primary); }
.media-card.selected::after { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(64,81,137,0.05); pointer-events: none; }

#bulkActions { display: none; position: fixed; bottom: 30px; left: 50%; transform: translateX(-50%); z-index: 1000; background: #fff; padding: 12px 24px; border-radius: 50px; box-shadow: 0 15px 45px rgba(0,0,0,0.2); border: 1px solid rgba(64,81,137,0.1); align-items: center; gap: 15px; animation: slideUp 0.3s ease; width: auto; max-width: 95%; }
@media (max-width: 575.98px) {
    #bulkActions { padding: 10px 16px; gap: 8px; font-size: 12px; bottom: 20px; white-space: nowrap; }
    #bulkActions .vr { height: 15px !important; margin: 0 4px !important; }
    #bulkActions button { padding: 5px 10px; }
}
@keyframes slideUp { from { transform: translate(-50%, 100px); opacity: 0; } to { transform: translate(-50%, 0); opacity: 1; } }

.empty-state { padding: 80px 20px; }
.empty-state-icon { width: 120px; height: 120px; background: #f3f6f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; color: #ced4da; transition: all 0.5s ease; position: relative; }
.empty-state:hover .empty-state-icon { color: #405189; background: #eef1ff; transform: rotate(10deg); }
.empty-state-icon i { font-size: 56px; }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Thư viện Media</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Hệ thống</a></li>
                    <li class="breadcrumb-item active">Media</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Sidebar thư mục -->
    <div class="col-xl-3 col-lg-4">
        <div class="card shadow-none border-0">
            <div class="card-body p-0">
                <div class="p-3 d-flex align-items-center justify-content-between">
                    <h6 class="text-uppercase fw-bold text-muted mb-0 fs-11">Danh mục thư mục</h6>
                    <button class="btn btn-sm btn-soft-primary" data-bs-toggle="modal" data-bs-target="#createFolderModal">
                        <i class="ri-add-line"></i>
                    </button>
                </div>
                <div class="px-2 pb-3 folder-list">
                    <div class="folder-item {{ !$folderId ? 'active' : '' }}" onclick="window.location='{{ route('admin.media.index') }}'">
                        <i class="ri-gallery-line align-middle me-1"></i> <span>Tất cả Media</span>
                        <span class="badge {{ !$folderId ? 'bg-primary' : 'bg-soft-primary text-primary' }} ms-auto">{{ \App\Models\MediaFile::count() }}</span>
                    </div>
                    @foreach($folders as $folder)
                    <div class="folder-item {{ $folderId == $folder->id ? 'active' : '' }}" onclick="window.location='{{ route('admin.media.index', ['folder_id' => $folder->id]) }}'">
                        <i class="{{ $folderId == $folder->id ? 'ri-folder-open-fill' : 'ri-folder-6-fill' }} align-middle me-1"></i>
                        <span>{{ $folder->name }}</span>
                        <span class="badge {{ $folderId == $folder->id ? 'bg-primary' : 'bg-soft-primary text-primary' }} ms-auto">{{ $folder->files->count() }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card mt-3 bg-soft-info border-0 shadow-none">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="ri-database-2-line display-4 text-info"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-info mb-1 fw-bold">Dung lượng</h6>
                        <p class="text-muted mb-0 fs-12">Tổng cộng: {{ number_format(\App\Models\MediaFile::sum('size') / 1048576, 2) }} MB</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vùng nội dung chính -->
    <div class="col-xl-9 col-lg-8">
        <div class="card">
            <div class="card-header border-0">
                <div class="row align-items-center g-3">
                    <div class="col-md-auto col-12">
                        <div class="d-flex align-items-center">
                            <div class="form-check me-2 mb-0">
                                <input class="form-check-input" type="checkbox" id="selectAll">
                            </div>
                            <h4 class="card-title mb-0 flex-grow-1">
                                @if($currentFolder)
                                    <i class="ri-folder-6-line me-1"></i> <span class="text-truncate">{{ $currentFolder->name }}</span>
                                @else
                                    <i class="ri-gallery-line me-1"></i> <span>Tất cả Media</span>
                                @endif
                            </h4>
                        </div>
                    </div>
                    <div class="col-md-auto col-12 ms-auto">
                        <div class="d-flex gap-2 justify-content-md-end align-items-center">
                            <select class="form-select" style="width: auto;" id="sortSelect" onchange="changeSort(this.value)">
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Cũ nhất</option>
                            </select>
                            <div class="search-box">
                                <input type="text" class="form-control search" id="searchInput" placeholder="Tìm tệp..." value="{{ request('search') }}">
                                <i class="ri-search-line search-icon"></i>
                            </div>
                            @can('media.upload')
                            <label class="btn btn-primary btn-label mb-0 rounded-pill" for="uploadInput">
                                <div class="d-flex align-items-center">
                                    <i class="ri-upload-cloud-2-line label-icon align-middle fs-18 me-2"></i> 
                                    <span class="d-none d-lg-inline">Upload tệp</span>
                                    <span class="d-inline d-lg-none">Tải lên</span>
                                </div>
                            </label>
                            <input type="file" id="uploadInput" multiple accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.zip" class="d-none" onchange="uploadFiles(this.files)">
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @can('media.upload')
                <div class="drop-zone mb-4" id="dropZone" onclick="document.getElementById('uploadInput').click()">
                    <div class="avatar-md mx-auto mb-3">
                        <div class="avatar-title bg-light text-primary display-4 rounded-circle border-dashed">
                            <i class="ri-upload-cloud-2-fill"></i>
                        </div>
                    </div>
                    <h5 class="mb-2">Kéo thả hoặc Click để tải tệp lên</h5>
                    <p class="text-muted mb-0 fs-13">Hỗ trợ định dạng: Hình ảnh, PDF, DOC, XLS, ZIP (Max {{ \App\Models\Setting::get('max_upload_size', 20) }}MB)</p>
                    
                    <div class="upload-progress mt-4 mx-auto" id="uploadProgress" style="display:none; max-width: 400px;">
                        <div class="progress animated-progress progress-sm">
                            <div class="progress-bar bg-primary" id="progressBar" style="width:0%"></div>
                        </div>
                        <p class="text-muted small mt-2 fw-medium" id="progressText">Đang xử lý...</p>
                    </div>
                </div>
                @endcan

                <div class="media-grid" id="mediaGrid">
                    @foreach($files as $file)
                    <div class="media-card" data-id="{{ $file->id }}" data-name="{{ $file->name }}" data-path="{{ $file->url }}" data-full-url="{{ $file->full_url }}">
                        <div class="form-check media-checkbox">
                            <input class="form-check-input file-checkbox" type="checkbox" value="{{ $file->id }}">
                        </div>
                        @if($file->type == 'image')
                            <img src="{{ $file->thumb_url }}" alt="{{ $file->name }}" loading="lazy">
                        @else
                            <div class="file-icon-wrapper">
                                @php
                                    $ext = pathinfo($file->url, PATHINFO_EXTENSION);
                                    $iconClass = match($ext) {
                                        'pdf' => 'ri-file-pdf-fill text-danger',
                                        'doc', 'docx' => 'ri-file-word-fill text-primary',
                                        'xls', 'xlsx' => 'ri-file-excel-fill text-success',
                                        'zip', 'rar' => 'ri-file-zip-fill text-warning',
                                        default => 'ri-file-text-fill text-muted'
                                    };
                                @endphp
                                <i class="{{ $iconClass }} display-3"></i>
                            </div>
                        @endif
                        <div class="media-actions">
                            <a href="{{ $file->full_url }}" target="_blank" class="btn-action" title="Xem ảnh gốc"><i class="ri-eye-line"></i></a>
                            @can('media.delete')
                            <button class="btn-action btn-delete text-danger" onclick="deleteFile(event, {{ $file->id }}, this)" title="Xóa vĩnh viễn"><i class="ri-delete-bin-fill"></i></button>
                            @endcan
                        </div>
                        <div class="media-info">
                            <div class="media-name" title="{{ $file->name }}">{{ $file->name }}</div>
                            <div class="media-size d-flex justify-content-between">
                                <span>{{ $file->formatted_size }}</span>
                                <span class="text-uppercase">{{ pathinfo($file->url, PATHINFO_EXTENSION) }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                @if($files->isEmpty())
                <div class="text-center empty-state w-100">
                    <div class="empty-state-icon shadow-sm">
                        <i class="ri-image-add-line"></i>
                    </div>
                    <h4 class="fw-bold text-dark">Thư viện trống rỗng</h4>
                    <p class="text-muted mx-auto" style="max-width: 300px;">Hãy bắt đầu xây dựng bộ sưu tập của bạn bằng cách tải lên bức ảnh đầu tiên.</p>
                    <button class="btn btn-primary btn-md px-4 mt-2 rounded-pill shadow-sm" onclick="document.getElementById('uploadInput').click()">
                        <i class="ri-upload-cloud-2-line me-2"></i> Tải lên ngay
                    </button>
                </div>
                @endif

                <div class="mt-4 d-flex justify-content-center">
                    {{ $files->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Actions Toolbar -->
<div id="bulkActions">
    <span class="fw-medium text-dark"><span id="selectedCount">0</span> tệp đã chọn</span>
    <div class="vr mx-2" style="height: 20px;"></div>
    <button class="btn btn-soft-danger btn-sm rounded-pill px-3" onclick="bulkDelete()">
        <i class="ri-delete-bin-line me-1"></i> Xóa hàng loạt
    </button>
    <button class="btn btn-ghost-secondary btn-sm rounded-pill" onclick="clearSelection()">
        Hủy chọn
    </button>
</div>

<!-- Modal: Tạo thư mục -->
<div class="modal fade" id="createFolderModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Tạo thư mục mới</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <input type="text" class="form-control" id="folderNameInput" placeholder="Tên thư mục...">
            </div>
            <div class="modal-footer">
                <button class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                <button class="btn btn-primary" onclick="createFolder()">Tạo</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/admin/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script>
const UPLOAD_URL  = '{{ route("admin.media.upload") }}';
const BULK_DELETE_URL = '{{ route("admin.media.bulk-destroy") }}';
const CSRF_TOKEN      = '{{ csrf_token() }}';
const FOLDER_ID       = '{{ $folderId ?? "" }}';
const DELETE_BASE     = '{{ url("admin/media") }}';
const IS_PICKER       = new URLSearchParams(window.location.search).get('picker') == '1';

// ===== Lọc và Sắp xếp =====
function changeSort(sort) {
    const url = new URL(window.location.href);
    url.searchParams.set('sort', sort);
    window.location.href = url.toString();
}

// ===== Tìm kiếm =====
document.getElementById('searchInput').addEventListener('input', function(e) {
    const query = e.target.value.toLowerCase();
    const cards = document.querySelectorAll('.media-card');
    
    cards.forEach(card => {
        const name = card.dataset.name.toLowerCase();
        if (name.includes(query)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
});

// ===== Upload Files =====
async function uploadFiles(files) {
    if (!files.length) return;
    
    const progress = document.getElementById('uploadProgress');
    const bar      = document.getElementById('progressBar');
    const text     = document.getElementById('progressText');
    progress.style.display = 'block';

    const totalFiles = files.length;
    let uploadedCount = 0;
    let errors = [];

    // Chạy tuần tự để tránh quá tải bộ nhớ máy chủ (OOM) khi xử lý nhiều ảnh nặng cùng lúc
    for (let i = 0; i < totalFiles; i++) {
        const file = files[i];
        const form = new FormData();
        form.append('file', file);
        form.append('_token', CSRF_TOKEN);
        if (FOLDER_ID) form.append('folder_id', FOLDER_ID);

        try {
            text.textContent = `Đang tải: ${file.name} (${i + 1}/${totalFiles})`;
            const res = await fetch(UPLOAD_URL, { method: 'POST', body: form });
            
            if (!res.ok) {
                let errorMsg = `HTTP ${res.status}`;
                try {
                    const data = await res.json();
                    if (data.message) errorMsg = data.message;
                    if (data.errors) errorMsg = Object.values(data.errors).flat().join(', ');
                } catch (e) {}
                
                console.error('Server error:', errorMsg);
                errors.push(`Lỗi "${file.name}": ${errorMsg}`);
                continue;
            }

            const data = await res.json();
            if (!data.error) {
                uploadedCount++;
                bar.style.width = Math.round((uploadedCount / totalFiles) * 100) + '%';
                appendMediaCard(data.file, data.url);
            } else {
                errors.push(`Lỗi file "${file.name}": ${data.message}`);
            }
        } catch (e) {
            console.error(e);
            errors.push(`Lỗi kết nối khi tải "${file.name}"`);
        }
    }

    if (errors.length > 0) {
        Swal.fire({
            icon: 'error',
            title: 'Lỗi tải lên',
            html: errors.join('<br>'),
            confirmButtonClass: 'btn btn-primary w-xs mt-2',
            buttonsStyling: false,
            footer: '<a href="">Bạn cần hỗ trợ?</a>'
        });
    }

    text.textContent = `Hoàn thành! Đã tải ${uploadedCount}/${totalFiles} tệp.`;
    
    // Reset input
    document.getElementById('uploadInput').value = '';
    
    setTimeout(() => { 
        progress.style.display = 'none'; 
        bar.style.width = '0%'; 
    }, 1500);
}

function appendMediaCard(file, url) {
    const grid = document.getElementById('mediaGrid');
    const card = document.createElement('div');
    card.className = 'media-card';
    card.dataset.name = file.name;
    card.dataset.path = file.url;
    card.dataset.fullUrl = url;
    
    const ext = file.url.split('.').pop().toLowerCase();
    let previewHtml = '';
    
    if (file.type === 'image') {
        previewHtml = `<img src="${url}" alt="${file.name}" loading="lazy">`;
    } else {
        let iconClass = 'ri-file-text-fill text-muted';
        if (ext === 'pdf') iconClass = 'ri-file-pdf-fill text-danger';
        else if (['doc', 'docx'].includes(ext)) iconClass = 'ri-file-word-fill text-primary';
        else if (['xls', 'xlsx'].includes(ext)) iconClass = 'ri-file-excel-fill text-success';
        else if (['zip', 'rar'].includes(ext)) iconClass = 'ri-file-zip-fill text-warning';
        
        previewHtml = `<div class="file-icon-wrapper"><i class="${iconClass} display-3"></i></div>`;
    }
    
    card.innerHTML = `
        <div class="form-check media-checkbox">
            <input class="form-check-input file-checkbox" type="checkbox" value="${file.id}">
        </div>
        ${previewHtml}
        <div class="media-actions">
            <a href="${url}" target="_blank" class="btn-action"><i class="ri-eye-line"></i></a>
            <button class="btn-action btn-delete text-danger" onclick="deleteFile(event, ${file.id}, this)"><i class="ri-delete-bin-fill"></i></button>
        </div>
        <div class="media-info">
            <div class="media-name" title="${file.name}">${file.name}</div>
            <div class="media-size d-flex justify-content-between">
                <span>${formatSize(file.size)}</span>
                <span class="text-uppercase">${ext}</span>
            </div>
        </div>`;
    grid.prepend(card);
}

function formatSize(bytes) {
    if (bytes >= 1048576) return (bytes/1048576).toFixed(2) + ' MB';
    if (bytes >= 1024)    return (bytes/1024).toFixed(2) + ' KB';
    return bytes + ' B';
}

// ===== Xóa File =====
async function deleteFile(event, id, btn) {
    event.stopPropagation();
    
    Swal.fire({
        title: 'Xác nhận xóa?',
        text: "Hành động này không thể hoàn tác!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'btn btn-primary w-xs me-2 mt-2',
        cancelButtonClass: 'btn btn-danger w-xs mt-2',
        confirmButtonText: 'Đúng, xóa nó!',
        cancelButtonText: 'Hủy bỏ',
        buttonsStyling: false,
        showCloseButton: true
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const res  = await fetch(`${DELETE_BASE}/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (!data.error) {
                    btn.closest('.media-card').remove();
                    Swal.fire({
                        title: 'Đã xóa!',
                        text: 'Tệp tin đã được loại bỏ khỏi hệ thống.',
                        icon: 'success',
                        confirmButtonClass: 'btn btn-primary w-xs mt-2',
                        buttonsStyling: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: data.message || 'Không thể xóa tệp tin.',
                        confirmButtonClass: 'btn btn-primary w-xs mt-2',
                        buttonsStyling: false
                    });
                }
            } catch (e) {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: 'Lỗi kết nối máy chủ.',
                    confirmButtonClass: 'btn btn-primary w-xs mt-2',
                    buttonsStyling: false
                });
            }
        }
    });
}

// ===== Tạo Thư Mục =====
async function createFolder() {
    const name = document.getElementById('folderNameInput').value.trim();
    if (!name) return alert('Vui lòng nhập tên thư mục');

    const res  = await fetch('{{ route("admin.media.folders.store") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ name })
    });
    const data = await res.json();
    if (!data.error) {
        location.reload();
    } else {
        alert('Lỗi tạo thư mục');
    }
}

// ===== Drag & Drop =====
const dropZone = document.getElementById('dropZone');
if (dropZone) {
    dropZone.addEventListener('dragover',  e => { e.preventDefault(); dropZone.classList.add('drag-over'); });
    dropZone.addEventListener('dragleave', ()  => dropZone.classList.remove('drag-over'));
    dropZone.addEventListener('drop', e => {
        e.preventDefault();
        dropZone.classList.remove('drag-over');
        uploadFiles(e.dataTransfer.files);
    });
}
// ===== Chọn File Hàng Loạt =====
const bulkActions   = document.getElementById('bulkActions');
const selectedCount = document.getElementById('selectedCount');
const selectAll     = document.getElementById('selectAll');

function updateSelectionUI() {
    const checked = document.querySelectorAll('.file-checkbox:checked');
    const count = checked.length;
    
    if (count > 0) {
        bulkActions.style.display = 'flex';
        selectedCount.textContent = count;
    } else {
        bulkActions.style.display = 'none';
        selectAll.checked = false;
    }

    document.querySelectorAll('.media-card').forEach(card => {
        const checkbox = card.querySelector('.file-checkbox');
        if (checkbox.checked) card.classList.add('selected');
        else card.classList.remove('selected');
    });
}

document.addEventListener('change', function(e) {
    if (e.target.classList.contains('file-checkbox')) {
        updateSelectionUI();
    }
});

// Click vào card (trừ nút hành động) để chọn
document.addEventListener('click', function(e) {
    const card = e.target.closest('.media-card');
    const actions = e.target.closest('.media-actions');
    const checkbox = e.target.closest('.media-checkbox');
    
    if (card && !actions && !checkbox) {
        if (IS_PICKER) {
            if (window.opener && typeof window.opener.mediaPickerCallback === 'function') {
                window.opener.mediaPickerCallback({
                    id: card.dataset.id,
                    name: card.dataset.name,
                    path: card.dataset.path,
                    full_url: card.dataset.fullUrl
                });
                window.close();
            }
            return;
        }

        const cb = card.querySelector('.file-checkbox');
        cb.checked = !cb.checked;
        updateSelectionUI();
    }
});

selectAll.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.file-checkbox');
    checkboxes.forEach(cb => {
        // Chỉ chọn những item đang hiển thị (nếu đang search)
        const card = cb.closest('.media-card');
        if (card.style.display !== 'none') {
            cb.checked = selectAll.checked;
        }
    });
    updateSelectionUI();
});

function clearSelection() {
    document.querySelectorAll('.file-checkbox').forEach(cb => cb.checked = false);
    selectAll.checked = false;
    updateSelectionUI();
}

async function bulkDelete() {
    const checked = document.querySelectorAll('.file-checkbox:checked');
    const ids = Array.from(checked).map(cb => cb.value);
    
    if (ids.length === 0) return;

    Swal.fire({
        title: 'Xóa hàng loạt?',
        text: `Bạn có chắc muốn xóa ${ids.length} tệp đã chọn?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'btn btn-primary w-xs me-2 mt-2',
        cancelButtonClass: 'btn btn-danger w-xs mt-2',
        confirmButtonText: 'Đúng, xóa hết!',
        cancelButtonText: 'Hủy bỏ',
        buttonsStyling: false
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const res = await fetch(BULK_DELETE_URL, {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ ids: ids })
                });
                
                const data = await res.json();
                if (!data.error) {
                    checked.forEach(cb => cb.closest('.media-card').remove());
                    clearSelection();
                    Swal.fire({
                        title: 'Đã xóa!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonClass: 'btn btn-primary w-xs mt-2',
                        buttonsStyling: false
                    });
                } else {
                    Swal.fire('Lỗi', data.message, 'error');
                }
            } catch (e) {
                Swal.fire('Lỗi', 'Lỗi kết nối máy chủ.', 'error');
            }
        }
    });
}
</script>
@endpush
