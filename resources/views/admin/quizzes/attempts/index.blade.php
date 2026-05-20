@extends('admin.layouts.master')

@section('title', 'Lịch sử thi')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Lịch sử thi</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Lịch sử thi</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header border-0">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">Tất cả lượt thi</h5>
                    <div class="flex-shrink-0" id="bulk-delete-container" style="display: none;">
                        <button class="btn btn-soft-danger" onClick="deleteMultiple()">
                            <i class="ri-delete-bin-2-line"></i> Xóa đã chọn (<span id="selected-count">0</span>)
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body border border-dashed border-end-0 border-start-0">
                <form action="{{ route('admin.quiz-attempts.index') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-xxl-4 col-sm-6">
                            <div class="search-box">
                                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Tìm tên thí sinh hoặc đề thi...">
                                <i class="ri-search-line search-icon"></i>
                            </div>
                        </div>
                        <div class="col-xxl-3 col-sm-6">
                            <select class="form-select" name="quiz_id" data-choices>
                                <option value="">Tất cả đề thi</option>
                                @foreach($quizzes as $quiz)
                                    <option value="{{ $quiz->id }}" {{ request('quiz_id') == $quiz->id ? 'selected' : '' }}>{{ $quiz->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xxl-2 col-sm-4">
                            <select class="form-select" name="status" data-choices data-choices-search-false>
                                <option value="">Trạng thái</option>
                                <option value="passed" {{ request('status') == 'passed' ? 'selected' : '' }}>Đạt (Passed)</option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Không đạt (Failed)</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                            </select>
                        </div>
                        <div class="col-xxl-2 col-sm-4">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ri-equalizer-fill me-1 align-bottom"></i> Lọc
                            </button>
                        </div>
                        @if(request()->anyFilled(['search', 'quiz_id', 'status']))
                        <div class="col-xxl-1 col-sm-4">
                            <a href="{{ route('admin.quiz-attempts.index') }}" class="btn btn-soft-danger w-100">
                                <i class="ri-refresh-line"></i>
                            </a>
                        </div>
                        @endif
                    </div>
                </form>
            </div>
            <div class="card-body">
                <div class="table-responsive table-card mb-4">
                    <table class="table align-middle table-nowrap mb-0">
                        <thead class="table-light text-muted">
                            <tr>
                                <th scope="col" style="width: 50px;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="checkAll" value="option">
                                    </div>
                                </th>
                                <th>Thí sinh</th>
                                <th>Đề thi</th>
                                <th>Kết quả</th>
                                <th>Thời gian làm bài</th>
                                <th>Ngày thi</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attempts as $attempt)
                            <tr>
                                <th scope="row">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="chk_child" value="{{ $attempt->id }}">
                                    </div>
                                </th>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $attempt->user->avatar_url }}" class="avatar-xs rounded-circle me-2" alt="">
                                        <div>
                                            <h6 class="mb-0">{{ $attempt->user->name }}</h6>
                                            <small class="text-muted">{{ $attempt->user->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <h6 class="mb-0">{{ $attempt->quiz->title }}</h6>
                                    @if($attempt->part_names)
                                        <div class="text-primary small mb-1">
                                            <i class="ri-git-repository-line align-middle"></i> {{ $attempt->part_names }}
                                        </div>
                                    @endif
                                    <small class="text-muted">{{ $attempt->quiz->category->name }}</small>
                                </td>
                                <td>
                                    <div class="text-{{ $attempt->status == 'passed' ? 'success' : ($attempt->status == 'failed' ? 'danger' : 'info') }}">
                                        <span class="fw-bold fs-14">{{ $attempt->total_count > 0 ? number_format(($attempt->correct_count / $attempt->total_count) * 100, 2) : 0 }}%</span>
                                        <div class="text-muted small">({{ $attempt->correct_count }}/{{ $attempt->total_count }} câu đúng)</div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $min = floor($attempt->time_spent / 60);
                                        $sec = $attempt->time_spent % 60;
                                    @endphp
                                    {{ $min > 0 ? $min . ' phút ' : '' }}{{ $sec }} giây
                                </td>
                                <td>{{ display_datetime($attempt->created_at, 'H:i d/m/Y') }}</td>
                                <td>
                                    @if($attempt->status == 'passed')
                                        <span class="badge bg-success text-uppercase">Passed</span>
                                    @elseif($attempt->status == 'failed')
                                        <span class="badge bg-danger text-uppercase">Failed</span>
                                    @else
                                        <span class="badge bg-info text-uppercase">Completed</span>
                                    @endif
                                </td>
                                <td>
                                    <ul class="list-inline hstack gap-2 mb-0">
                                        <li class="list-inline-item" data-bs-toggle="tooltip" title="Chi tiết">
                                            <a href="{{ route('admin.quiz-attempts.show', $attempt->id) }}" class="text-primary d-inline-block">
                                                <i class="ri-eye-fill fs-16"></i>
                                            </a>
                                        </li>
                                        <li class="list-inline-item">
                                            <form action="{{ route('admin.quiz-attempts.destroy', $attempt->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn p-0 text-danger confirm-delete">
                                                    <i class="ri-delete-bin-5-fill fs-16"></i>
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <h5 class="mt-2 text-muted">Chưa có lượt thi nào</h5>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end">
                    {{ $attempts->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const checkAll = document.getElementById('checkAll');
        const checkboxes = document.querySelectorAll('input[name="chk_child"]');
        const bulkDeleteContainer = document.getElementById('bulk-delete-container');
        const selectedCount = document.getElementById('selected-count');

        if(checkAll) {
            checkAll.addEventListener('change', function() {
                checkboxes.forEach(function(checkbox) {
                    checkbox.checked = checkAll.checked;
                });
                toggleRemoveActions();
            });
        }

        checkboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', toggleRemoveActions);
        });

        function toggleRemoveActions() {
            const checkedCount = document.querySelectorAll('input[name="chk_child"]:checked').length;
            if(checkedCount > 0) {
                bulkDeleteContainer.style.display = 'block';
                selectedCount.innerText = checkedCount;
            } else {
                bulkDeleteContainer.style.display = 'none';
                selectedCount.innerText = '0';
                if(checkAll) checkAll.checked = false;
            }
        }
    });

    // Make it global so onClick can access it
    function deleteMultiple() {
        const checkedCheckboxes = document.querySelectorAll('input[name="chk_child"]:checked');
        if(checkedCheckboxes.length === 0) {
            alert('Vui lòng chọn ít nhất 1 dòng.');
            return;
        }

        Swal.fire({
            title: "Bạn có chắc chắn?",
            text: "Bạn sẽ không thể khôi phục dữ liệu đã xóa!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonClass: 'btn btn-primary w-xs me-2 mt-2',
            cancelButtonClass: 'btn btn-danger w-xs mt-2',
            confirmButtonText: "Đúng, xóa nó!",
            cancelButtonText: "Hủy",
            buttonsStyling: false,
            showCloseButton: true
        }).then(function (result) {
            if (result.value) {
                const ids = Array.from(checkedCheckboxes).map(cb => cb.value);
                
                fetch('{{ route("admin.quiz-attempts.bulk-destroy") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ ids: ids })
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        Swal.fire({
                            title: 'Đã xóa!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonClass: 'btn btn-primary w-xs mt-2',
                            buttonsStyling: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Lỗi!',
                            text: data.message || 'Đã có lỗi xảy ra',
                            icon: 'error',
                            confirmButtonClass: 'btn btn-primary w-xs mt-2',
                            buttonsStyling: false
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Lỗi!',
                        text: 'Đã có lỗi xảy ra trong quá trình xử lý.',
                        icon: 'error',
                        confirmButtonClass: 'btn btn-primary w-xs mt-2',
                        buttonsStyling: false
                    });
                });
            }
        });
    }
</script>
@endpush

