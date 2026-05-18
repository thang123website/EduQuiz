@extends('admin.layouts.master')

@section('title', 'Dịch từ vựng tĩnh')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Từ vựng giao diện: <span class="text-primary text-uppercase">{{ $code }}</span></h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.languages.index') }}">Ngôn ngữ</a></li>
                    <li class="breadcrumb-item active">Từ vựng tĩnh</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Ngôn ngữ nguồn: Tiếng Việt (vi)</h4>
                <div>
                    <button id="btnTranslateAll" class="btn btn-warning shadow-none">
                        <i class="ri-translate-2 align-bottom me-1"></i> Translate All
                    </button>
                    <a href="{{ route('admin.languages.index') }}" class="btn btn-light ms-2">Quay lại</a>
                </div>
            </div>
            <div class="card-body">
                <div id="translateProgressWrapper" class="d-none mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="fw-semibold">Đang dịch tự động...</span>
                        <span id="translateProgressText" class="fw-bold">0%</span>
                    </div>
                    <div class="progress">
                        <div id="translateProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-warning" role="progressbar" style="width: 0%"></div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle" id="translationTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%">SL#</th>
                                <th style="width: 40%">Giá trị gốc (Current Value)</th>
                                <th style="width: 35%">Bản dịch (Translated Value)</th>
                                <th style="width: 10%" class="text-center">Auto Translate</th>
                                <th style="width: 10%" class="text-center">Update</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i = 1; @endphp
                            @foreach($defaultTranslations as $key => $originalValue)
                            @php 
                                $translatedValue = $translations[$key] ?? ''; 
                            @endphp
                            <tr>
                                <td>{{ $i++ }}</td>
                                <td>
                                    <div class="text-wrap">{{ $originalValue }}</div>
                                    <small class="text-muted">Key: <code>{{ $key }}</code></small>
                                </td>
                                <td>
                                    <input type="text" class="form-control translation-val" data-key="{{ $key }}" value="{{ $translatedValue }}">
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-soft-success w-100 btn-auto-translate" data-key="{{ $key }}">
                                        <i class="ri-global-line"></i>
                                    </button>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-primary w-100 btn-save-translation" data-key="{{ $key }}">
                                        <i class="ri-save-line"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Cập nhật thủ công từng dòng
    document.querySelectorAll('.btn-save-translation').forEach(btn => {
        btn.addEventListener('click', function() {
            const key = this.getAttribute('data-key');
            const input = document.querySelector(`.translation-val[data-key="${key}"]`);
            const value = input.value;
            const originalHtml = this.innerHTML;
            
            this.innerHTML = '<i class="ri-loader-4-line spin"></i>';
            this.disabled = true;

            fetch('{{ route("admin.languages.translations.update", $code, false) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ key: key, value: value })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    input.classList.add('bg-success', 'text-white');
                    setTimeout(() => input.classList.remove('bg-success', 'text-white'), 800);
                }
            })
            .finally(() => {
                this.innerHTML = originalHtml;
                this.disabled = false;
            });
        });
    });

    // 2. Dịch tự động 1 dòng
    document.querySelectorAll('.btn-auto-translate').forEach(btn => {
        btn.addEventListener('click', function() {
            const key = this.getAttribute('data-key');
            const input = document.querySelector(`.translation-val[data-key="${key}"]`);
            const originalHtml = this.innerHTML;
            
            this.innerHTML = '<i class="ri-loader-4-line spin"></i>';
            this.disabled = true;

            fetch('{{ route("admin.languages.translate-single", $code, false) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ key: key })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    input.value = data.translated;
                    input.classList.add('bg-warning', 'text-dark');
                    setTimeout(() => input.classList.remove('bg-warning', 'text-dark'), 800);
                } else {
                    alert(data.message || 'Lỗi dịch thuật');
                }
            })
            .finally(() => {
                this.innerHTML = originalHtml;
                this.disabled = false;
            });
        });
    });

    // 3. Dịch tất cả (Đệ quy lô nhỏ)
    const btnTranslateAll = document.getElementById('btnTranslateAll');
    const progressWrapper = document.getElementById('translateProgressWrapper');
    const progressBar = document.getElementById('translateProgressBar');
    const progressText = document.getElementById('translateProgressText');

    function processBatch() {
        fetch('{{ route("admin.languages.auto-translate", [], false) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ code: '{{ $code }}' })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                progressBar.style.width = data.progress + '%';
                progressText.innerText = data.progress + '%';
                
                if (!data.completed) {
                    // Tiếp tục gọi đệ quy
                    setTimeout(processBatch, 1000); 
                } else {
                    Swal.fire({
                        title: 'Thành công!',
                        text: 'Đã dịch xong toàn bộ!',
                        icon: 'success',
                        confirmButtonClass: 'btn btn-primary w-xs me-2 mt-2',
                        buttonsStyling: false,
                        showCloseButton: true
                    }).then(() => {
                        window.location.reload();
                    });
                }
            } else {
                Swal.fire({
                    title: 'Lỗi!',
                    text: 'Có lỗi xảy ra: ' + data.message,
                    icon: 'error',
                    confirmButtonClass: 'btn btn-primary w-xs mt-2',
                    buttonsStyling: false,
                    showCloseButton: true
                });
                btnTranslateAll.disabled = false;
            }
        })
        .catch(err => {
            Swal.fire({
                title: 'Lỗi mạng!',
                text: 'Vui lòng thử lại sau',
                icon: 'error',
                confirmButtonClass: 'btn btn-primary w-xs mt-2',
                buttonsStyling: false,
                showCloseButton: true
            });
            btnTranslateAll.disabled = false;
        });
    }

    if (btnTranslateAll) {
        btnTranslateAll.addEventListener('click', function() {
            Swal.fire({
                title: 'Bắt đầu dịch tự động?',
                text: "Quá trình này sẽ gọi API dịch hàng loạt. Hãy chắc chắn bạn không thoát trang trong lúc này.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonClass: 'btn btn-primary w-xs me-2 mb-1',
                cancelButtonClass: 'btn btn-danger w-xs mb-1',
                confirmButtonText: 'Có, bắt đầu!',
                cancelButtonText: 'Hủy',
                buttonsStyling: false,
                showCloseButton: true
            }).then((result) => {
                if (result.isConfirmed) {
                    btnTranslateAll.disabled = true;
                    progressWrapper.classList.remove('d-none');
                    progressBar.style.width = '0%';
                    progressText.innerText = '0%';
                    
                    processBatch();
                }
            });
        });
    }
});
</script>
<style>
.spin { animation: spin 1s linear infinite; display: inline-block; }
@keyframes spin { 100% { transform: rotate(360deg); } }
</style>
@endpush
