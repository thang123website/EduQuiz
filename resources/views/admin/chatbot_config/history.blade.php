@extends('admin.layouts.master')

@section('title', 'Lịch sử Chatbot')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Quản lý Lịch sử Chatbot</h4>
            <div class="page-title-right">
                <form action="{{ route('admin.chatbot-history.clear') }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa TOÀN BỘ dữ liệu chat của tất cả người dùng không? Hành động này không thể hoàn tác và sẽ xóa sạch bảng chat_messages và chat_sessions!');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger"><i class="ri-delete-bin-2-line align-bottom me-1"></i> Xóa Toàn Bộ Lịch Sử</button>
                </form>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<form action="{{ route('admin.chatbot-history.bulk-delete') }}" method="POST" id="bulk-delete-form">
    @csrf
    @method('DELETE')
    <div class="card">
        <div class="card-header border-0 pb-0">
            <div class="d-flex align-items-center">
                <h5 class="card-title mb-0 flex-grow-1">Danh sách phiên chat</h5>
                <div class="flex-shrink-0">
                    <button type="button" class="btn btn-danger btn-sm" id="btn-delete-selected" style="display: none;" onclick="if(confirm('Bạn có chắc muốn xóa các phiên chat đã chọn không?')) document.getElementById('bulk-delete-form').submit();">
                        <i class="ri-delete-bin-fill align-bottom me-1"></i> Xóa mục đã chọn
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" style="width: 40px;">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="checkAll">
                                </div>
                            </th>
                            <th style="width: 5%;">STT</th>
                            <th>Session Token</th>
                            <th>Người dùng</th>
                            <th>Số tin nhắn</th>
                            <th>Trạng thái</th>
                            <th>Thời gian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sessions as $session)
                        <tr>
                            <th scope="row">
                                <div class="form-check">
                                    <input class="form-check-input row-checkbox" type="checkbox" name="ids[]" value="{{ $session->id }}">
                                </div>
                            </th>
                            <td>{{ $loop->iteration }}</td>
                            <td><span class="badge bg-info-subtle text-info">{{ Str::limit($session->session_token, 30) }}</span></td>
                            <td>{{ $session->user ? $session->user->name : 'Admin (Test)' }}</td>
                            <td><span class="badge bg-primary">{{ $session->messages_count }}</span></td>
                            <td>
                                @if($session->status == 'active')
                                    <span class="badge bg-success">Đang Chat</span>
                                @else
                                    <span class="badge bg-secondary">Kết Thúc</span>
                                @endif
                            </td>
                            <td>{{ $session->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Chưa có dữ liệu lịch sử chat nào.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $sessions->links() }}
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
    document.getElementById('checkAll').addEventListener('change', function() {
        let checkboxes = document.querySelectorAll('.row-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
        toggleDeleteBtn();
    });

    document.querySelectorAll('.row-checkbox').forEach(cb => {
        cb.addEventListener('change', toggleDeleteBtn);
    });

    function toggleDeleteBtn() {
        let checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
        let deleteBtn = document.getElementById('btn-delete-selected');
        if(checkedCount > 0) {
            deleteBtn.style.display = 'inline-block';
        } else {
            deleteBtn.style.display = 'none';
        }
    }
</script>
@endpush
@endsection
