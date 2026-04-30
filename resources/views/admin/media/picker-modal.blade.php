<!-- Modal Media Picker -->
<div class="modal fade" id="mediaPickerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title d-flex align-items-center">
                    <i class="ri-image-2-fill text-primary fs-20 me-2"></i>
                    <span>Thư viện Media</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-0">
                <div class="d-flex h-100" style="min-height: 500px;">
                    <!-- Sidebar: Thư mục -->
                    <div class="bg-light border-end" style="width: 220px; flex-shrink: 0;">
                        <div class="p-3">
                            <h6 class="text-uppercase fw-semibold fs-11 text-muted mb-3">Thư mục</h6>
                            <div id="pickerFolderList">
                                <div class="picker-folder-item active" data-id="">
                                    <i class="ri-gallery-line me-2"></i> Tất cả ảnh
                                </div>
                                {{-- Thư mục sẽ được load bằng JS --}}
                            </div>
                        </div>
                    </div>

                    <!-- Nội dung chính: Grid ảnh -->
                    <div class="flex-grow-1 d-flex flex-column bg-white">
                        <!-- Toolbar -->
                        <div class="p-3 border-bottom d-flex align-items-center gap-3 bg-white sticky-top">
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <span class="input-group-text bg-light border-light"><i class="ri-search-line"></i></span>
                                <input type="text" class="form-control bg-light border-light" id="pickerSearchInput" placeholder="Tìm tên ảnh...">
                            </div>
                            
                            <div class="ms-auto d-flex gap-2 align-items-center">
                                <span class="upload-status small text-muted" id="pickerUploadStatus"></span>
                                <label class="btn btn-soft-primary btn-sm mb-0" for="pickerUploadInput">
                                    <i class="ri-upload-cloud-2-line me-1"></i> Tải ảnh lên
                                </label>
                                <input type="file" id="pickerUploadInput" multiple accept="image/*" class="d-none" onchange="pickerUploadFiles(this.files)">
                            </div>
                        </div>

                        <!-- Grid -->
                        <div class="p-3 flex-grow-1 position-relative overflow-auto">
                            <div id="pickerGrid" class="picker-media-grid">
                                {{-- Ảnh được load bằng JS --}}
                            </div>
                            
                            <!-- Loading overlay -->
                            <div id="pickerLoading" class="position-absolute top-50 start-50 translate-middle text-center" style="display:none;">
                                <div class="spinner-border text-primary" role="status"></div>
                                <p class="text-muted mt-2">Đang tải dữ liệu...</p>
                            </div>

                            <!-- Empty State -->
                            <div id="pickerEmpty" class="text-center py-5" style="display:none;">
                                <div class="avatar-lg mx-auto mb-3">
                                    <div class="avatar-title bg-soft-info text-info display-4 rounded-circle">
                                        <i class="ri-image-add-line"></i>
                                    </div>
                                </div>
                                <h5 class="fw-bold">Không tìm thấy tệp nào</h5>
                                <p class="text-muted mx-auto" style="max-width: 250px;">Thư mục này hiện đang trống hoặc không có kết quả tìm kiếm nào.</p>
                                <button class="btn btn-soft-primary btn-sm rounded-pill mt-2" onclick="document.getElementById('pickerUploadInput').click()">
                                    <i class="ri-upload-cloud-2-line me-1"></i> Tải ảnh lên ngay
                                </button>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div class="p-2 border-top bg-light">
                            <div id="pickerPagination" class="d-flex justify-content-center"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light border-top">
                <div class="flex-grow-1 d-flex align-items-center">
                    <div id="pickerSelectedInfo" class="text-muted small fw-medium">
                        <i class="ri-information-line me-1"></i> Chưa chọn ảnh nào
                    </div>
                </div>
                <button type="button" class="btn btn-link link-danger fw-medium" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" id="pickerConfirmBtn" onclick="confirmMediaSelection()" disabled>
                    <i class="ri-check-line me-1"></i> Xác nhận chọn
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.picker-media-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
    gap: 15px;
}

.picker-media-card {
    position: relative;
    border: 2px solid #eff2f7;
    border-radius: 8px;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.2s ease;
    background: #fff;
}

.picker-media-card:hover {
    border-color: var(--vz-primary);
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.picker-media-card.selected {
    border-color: var(--vz-primary);
    background: var(--vz-primary-bg-subtle);
}

.picker-media-card.selected::after {
    content: "\eb7b"; /* ri-checkbox-circle-fill */
    font-family: 'remixicon';
    position: absolute;
    top: 5px;
    right: 5px;
    background: var(--vz-primary);
    color: #fff;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
}

.picker-media-card img {
    width: 100%;
    height: 100px;
    object-fit: cover;
    display: block;
}

.picker-media-info {
    padding: 8px;
}

.picker-media-name {
    font-size: 11px;
    font-weight: 500;
    color: #333;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.picker-folder-item {
    padding: 8px 12px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 13px;
    color: #495057;
    margin-bottom: 2px;
    transition: all 0.2s;
}

.picker-folder-item:hover {
    background: #f3f3f9;
    color: var(--vz-primary);
}

.picker-folder-item.active {
    background: var(--vz-primary);
    color: #fff;
    font-weight: 500;
}
.picker-file-icon {
    height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    border-bottom: 1px solid #f3f3f9;
}
</style>

@push('scripts')
<script>
let _pickerTargetInput = null;
let _pickerTargetPreview = null;
let _pickerSelectedUrl = null;
let _pickerCurrentFolder = '';
let _pickerSearchQuery = '';

function openMediaPicker(inputId, previewId = null) {
    _pickerTargetInput = inputId;
    _pickerTargetPreview = previewId;
    _pickerSelectedUrl = null;
    
    const modal = new bootstrap.Modal(document.getElementById('mediaPickerModal'));
    modal.show();
    
    loadPickerFiles(1);
}

async function loadPickerFiles(page = 1) {
    document.getElementById('pickerLoading').style.display = 'block';
    document.getElementById('pickerGrid').style.opacity = '0.5';
    document.getElementById('pickerEmpty').style.display = 'none';

    try {
        let url = `{{ route('admin.media.files') }}?page=${page}`;
        if (_pickerCurrentFolder) url += `&folder_id=${_pickerCurrentFolder}`;
        if (_pickerSearchQuery) url += `&search=${_pickerSearchQuery}`;

        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
        const data = await res.json();
        
        renderPickerGrid(data.data);
        renderPickerPagination(data);
        
        if (data.data.length === 0) {
            document.getElementById('pickerEmpty').style.display = 'block';
        }
    } catch (e) {
        console.error('Picker error:', e);
    } finally {
        document.getElementById('pickerLoading').style.display = 'none';
        document.getElementById('pickerGrid').style.opacity = '1';
    }
}

function renderPickerGrid(files) {
    const grid = document.getElementById('pickerGrid');
    grid.innerHTML = '';

    files.forEach(file => {
        const card = document.createElement('div');
        card.className = `picker-media-card ${file.full_url === _pickerSelectedUrl ? 'selected' : ''}`;
        card.onclick = () => {
            document.querySelectorAll('.picker-media-card').forEach(c => c.classList.remove('selected'));
            card.classList.add('selected');
            _pickerSelectedUrl = file.full_url;
            document.getElementById('pickerConfirmBtn').disabled = false;
            document.getElementById('pickerSelectedInfo').innerHTML = `<i class="ri-check-line text-success me-1"></i> Đang chọn: <strong>${file.name}</strong>`;
        };
        
        let previewHtml = '';
        if (file.type === 'image') {
            previewHtml = `<img src="${file.thumb_url}" alt="${file.name}">`;
        } else {
            const ext = file.url.split('.').pop().toLowerCase();
            let iconClass = 'ri-file-text-fill text-muted';
            if (ext === 'pdf') iconClass = 'ri-file-pdf-fill text-danger';
            else if (['doc', 'docx'].includes(ext)) iconClass = 'ri-file-word-fill text-primary';
            else if (['xls', 'xlsx'].includes(ext)) iconClass = 'ri-file-excel-fill text-success';
            else if (['zip', 'rar'].includes(ext)) iconClass = 'ri-file-zip-fill text-warning';
            
            previewHtml = `<div class="picker-file-icon"><i class="${iconClass} fs-24"></i></div>`;
        }

        card.innerHTML = `
            ${previewHtml}
            <div class="picker-media-info">
                <div class="picker-media-name" title="${file.name}">${file.name}</div>
                <div class="text-muted" style="font-size: 9px;">${file.formatted_size}</div>
            </div>
        `;
        grid.appendChild(card);
    });
}

function renderPickerPagination(data) {
    const pag = document.getElementById('pickerPagination');
    if (data.last_page <= 1) { pag.innerHTML = ''; return; }

    let html = '<ul class="pagination pagination-sm mb-0">';
    for (let i = 1; i <= data.last_page; i++) {
        html += `<li class="page-item ${i === data.current_page ? 'active' : ''}">
            <a class="page-link" href="javascript:void(0)" onclick="loadPickerFiles(${i})">${i}</a>
        </li>`;
    }
    html += '</ul>';
    pag.innerHTML = html;
}

function confirmMediaSelection() {
    if (!_pickerSelectedUrl) return;
    const input = document.getElementById(_pickerTargetInput);
    if (input) input.value = _pickerSelectedUrl;
    if (_pickerTargetPreview) {
        const preview = document.getElementById(_pickerTargetPreview);
        if (preview) {
            preview.src = _pickerSelectedUrl;
            preview.style.display = 'block';
            preview.closest('.picker-preview-wrap')?.classList.remove('d-none');
        }
    }
    bootstrap.Modal.getInstance(document.getElementById('mediaPickerModal')).hide();
}

// Xử lý Search
document.getElementById('pickerSearchInput').oninput = (e) => {
    _pickerSearchQuery = e.target.value;
    clearTimeout(window._pickerSearchTimer);
    window._pickerSearchTimer = setTimeout(() => loadPickerFiles(1), 500);
};

// Upload nhanh
async function pickerUploadFiles(files) {
    const status = document.getElementById('pickerUploadStatus');
    const total = files.length;
    let uploaded = 0;

    for (let i = 0; i < total; i++) {
        const file = files[i];
        status.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span> Đang tải: ${i+1}/${total}...`;
        
        const form = new FormData();
        form.append('file', file);
        form.append('_token', '{{ csrf_token() }}');
        if (_pickerCurrentFolder) form.append('folder_id', _pickerCurrentFolder);

        try {
            const res = await fetch('{{ route("admin.media.upload") }}', { method: 'POST', body: form });
            const data = await res.json();
            if (!data.error) uploaded++;
        } catch (e) {
            console.error('Upload error:', e);
        }
    }

    status.innerHTML = `<span class="text-success"><i class="ri-checkbox-circle-line"></i> Đã xong ${uploaded}/${total} tệp!</span>`;
    setTimeout(() => {
        status.innerHTML = '';
        loadPickerFiles(1);
    }, 2000);
}
</script>
@endpush
