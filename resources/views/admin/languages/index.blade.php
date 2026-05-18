@extends('admin.layouts.master')

@section('title', 'Cấu hình Ngôn ngữ')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Quản lý Ngôn ngữ</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Hệ thống</a></li>
                    <li class="breadcrumb-item active">Ngôn ngữ</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="ri-check-double-line me-3 align-middle"></i> <strong>Thành công</strong> - {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="ri-error-warning-line me-3 align-middle"></i> <strong>Lỗi</strong> - {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Danh sách ngôn ngữ hệ thống</h4>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLangModal"><i class="ri-add-line align-bottom me-1"></i> Thêm ngôn ngữ</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Mã (Code)</th>
                                <th>Tên ngôn ngữ</th>
                                <th>Hướng viết</th>
                                <th>Trạng thái</th>
                                <th>Mặc định</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($languages as $lang)
                            <tr>
                                <td><span class="badge bg-info">{{ $lang['code'] }}</span></td>
                                <td>{{ $lang['name'] }}</td>
                                <td>{{ strtoupper($lang['direction']) }}</td>
                                <td>
                                    @if($lang['status'])
                                        <span class="badge bg-success">Hoạt động</span>
                                    @else
                                        <span class="badge bg-danger">Khóa</span>
                                    @endif
                                </td>
                                <td>
                                    @if($lang['default'] ?? false)
                                        <i class="ri-star-fill text-warning fs-18"></i>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.languages.translations', $lang['code']) }}" class="btn btn-sm btn-soft-secondary">
                                            <i class="ri-translate-2 align-bottom"></i> Từ vựng
                                        </a>

                                        @if($lang['code'] !== 'vi')
                                        <button type="button" class="btn btn-sm btn-soft-info btn-translate-data" data-code="{{ $lang['code'] }}" data-name="{{ $lang['name'] }}">
                                            <i class="ri-database-2-line align-bottom"></i> Dịch DB
                                        </button>
                                        @endif
                                        
                                        <form action="{{ route('admin.languages.update', $lang['code']) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="default" value="1">
                                            <button type="submit" class="btn btn-sm btn-soft-warning" {{ ($lang['default'] ?? false) ? 'disabled' : '' }}>
                                                Đặt mặc định
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.languages.destroy', $lang['code']) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-soft-danger confirm-delete" {{ ($lang['default'] ?? false) ? 'disabled' : '' }}>
                                                <i class="ri-delete-bin-line"></i> Xóa
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Chưa có ngôn ngữ nào được cấu hình</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Thêm -->
<div class="modal fade" id="addLangModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.languages.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Thêm ngôn ngữ mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Chọn ngôn ngữ (Language)</label>
                    <select name="code" class="form-select" data-choices data-choices-search-true required>
                        <option value="">-- Chọn ngôn ngữ --</option>
                        @foreach($availableLanguages as $code => $name)
                            <option value="{{ $code }}">{{ $name }} - {{ $code }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Hướng viết chữ (Direction)</label>
                    <select name="direction" class="form-select" required>
                        <option value="ltr">LTR (Từ trái qua phải)</option>
                        <option value="rtl">RTL (Từ phải qua trái)</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
                <button type="submit" class="btn btn-primary">Thêm mới</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Dịch Dữ Liệu -->
<div class="modal fade" id="translateDataModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Dịch tự động CSDL sang <span id="targetLangName" class="text-primary"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Chọn các bảng dữ liệu bạn muốn tự động dịch (Bỏ qua các bản ghi đã dịch):</p>
                
                <div class="form-check mb-2">
                    <input class="form-check-input table-checkbox" type="checkbox" value="blog" id="chkBlog" checked>
                    <label class="form-check-label" for="chkBlog">Bài viết (Blog)</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input table-checkbox" type="checkbox" value="blog_category" id="chkBlogCat" checked>
                    <label class="form-check-label" for="chkBlogCat">Danh mục Bài viết (BlogCategory)</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input table-checkbox" type="checkbox" value="slider" id="chkSlider" checked>
                    <label class="form-check-label" for="chkSlider">Trình chiếu (Slider)</label>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input table-checkbox" type="checkbox" value="slider_item" id="chkSliderItem" checked>
                    <label class="form-check-label" for="chkSliderItem">Ảnh trình chiếu (SliderItem)</label>
                </div>

                <div id="dbTranslateProgressWrapper" class="d-none">
                    <div class="d-flex justify-content-between mb-1">
                        <span id="dbTranslateStatus" class="fw-semibold">Đang chuẩn bị...</span>
                        <span id="dbTranslateProgressText" class="fw-bold">0%</span>
                    </div>
                    <div class="progress">
                        <div id="dbTranslateProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-info" role="progressbar" style="width: 0%"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal" id="btnCancelDbTranslate">Đóng</button>
                <button type="button" class="btn btn-info" id="btnStartDbTranslate"><i class="ri-play-circle-line align-bottom me-1"></i> Bắt đầu dịch</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentCode = '';
    let selectedModels = [];
    let currentModelIndex = 0;
    let currentPage = 1;

    const translateModal = new bootstrap.Modal(document.getElementById('translateDataModal'));
    const btnStart = document.getElementById('btnStartDbTranslate');
    const btnCancel = document.getElementById('btnCancelDbTranslate');
    const progressWrapper = document.getElementById('dbTranslateProgressWrapper');
    const progressBar = document.getElementById('dbTranslateProgressBar');
    const progressText = document.getElementById('dbTranslateProgressText');
    const statusText = document.getElementById('dbTranslateStatus');

    // Mở Modal
    document.querySelectorAll('.btn-translate-data').forEach(btn => {
        btn.addEventListener('click', function() {
            currentCode = this.getAttribute('data-code');
            document.getElementById('targetLangName').innerText = this.getAttribute('data-name');
            
            // Reset modal
            progressWrapper.classList.add('d-none');
            btnStart.disabled = false;
            btnCancel.disabled = false;
            document.querySelectorAll('.table-checkbox').forEach(chk => { chk.disabled = false; chk.checked = true; });
            
            translateModal.show();
        });
    });

    function processNextModelBatch() {
        if (currentModelIndex >= selectedModels.length) {
            statusText.innerText = "Hoàn tất dịch toàn bộ DB!";
            progressBar.classList.remove('progress-bar-animated');
            progressBar.style.width = '100%';
            progressText.innerText = '100%';
            btnCancel.disabled = false;
            btnStart.disabled = false;
            btnStart.innerHTML = '<i class="ri-check-line align-bottom me-1"></i> Xong';
            
            Swal.fire('Thành công', 'Đã dịch xong các bảng được chọn!', 'success');
            return;
        }

        const model = selectedModels[currentModelIndex];
        statusText.innerText = `Đang dịch ${model} (Trang ${currentPage})...`;

        fetch('{{ route("admin.languages.auto-translate-data", [], false) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ code: currentCode, model: model, page: currentPage })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                // Progress tổng quan giả lập dựa trên index model và progress của model đó
                let overallProgress = ((currentModelIndex * 100) + (data.progress || 100)) / selectedModels.length;
                progressBar.style.width = overallProgress + '%';
                progressText.innerText = Math.round(overallProgress) + '%';
                
                if (!data.completed) {
                    currentPage = data.current_page + 1; // Tăng trang
                    setTimeout(processNextModelBatch, 1000); // Đợi 1s tránh rate limit Google
                } else {
                    // Xong model này, chuyển sang model tiếp theo
                    currentModelIndex++;
                    currentPage = 1;
                    setTimeout(processNextModelBatch, 1000);
                }
            } else {
                Swal.fire('Lỗi!', 'Có lỗi: ' + data.message, 'error');
                btnCancel.disabled = false;
            }
        })
        .catch(err => {
            Swal.fire('Lỗi mạng!', 'Mất kết nối', 'error');
            btnCancel.disabled = false;
        });
    }

    btnStart.addEventListener('click', function() {
        selectedModels = [];
        document.querySelectorAll('.table-checkbox:checked').forEach(chk => {
            selectedModels.push(chk.value);
        });

        if(selectedModels.length === 0) {
            Swal.fire('Chú ý', 'Vui lòng chọn ít nhất một bảng để dịch', 'warning');
            return;
        }

        // Bắt đầu
        this.disabled = true;
        this.innerHTML = '<i class="ri-loader-4-line spin"></i> Đang xử lý...';
        btnCancel.disabled = true;
        document.querySelectorAll('.table-checkbox').forEach(chk => chk.disabled = true);
        
        progressWrapper.classList.remove('d-none');
        progressBar.style.width = '0%';
        progressText.innerText = '0%';
        
        currentModelIndex = 0;
        currentPage = 1;
        processNextModelBatch();
    });
});
</script>
<style>
.spin { animation: spin 1s linear infinite; display: inline-block; }
@keyframes spin { 100% { transform: rotate(360deg); } }
</style>
@endpush
