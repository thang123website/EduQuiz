@extends('admin.layouts.master')

@section('title', 'Quản lý Yêu cầu / Form')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Danh sách Yêu cầu / Form</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header border-0">
                <form action="{{ route('admin.forms.index') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <select name="type" class="form-select">
                            <option value="">-- Tất cả phân loại --</option>
                            <option value="contact" {{ request('type') == 'contact' ? 'selected' : '' }}>Liên hệ</option>
                            <option value="instructor_register" {{ request('type') == 'instructor_register' ? 'selected' : '' }}>Đăng ký giảng viên</option>
                            <option value="support" {{ request('type') == 'support' ? 'selected' : '' }}>Hỗ trợ</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">-- Tất cả trạng thái --</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Đang giải quyết</option>
                            <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Đã xử lý</option>
                            <option value="ignored" {{ request('status') == 'ignored' ? 'selected' : '' }}>Bỏ qua</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="ri-filter-2-line me-1"></i> Lọc</button>
                    </div>
                </form>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-nowrap align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Mã (#ID)</th>
                                <th scope="col">Phân loại</th>
                                <th scope="col">Thông tin (Trích xuất)</th>
                                <th scope="col">Thời gian gửi</th>
                                <th scope="col">Trạng thái</th>
                                <th scope="col">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($submissions as $item)
                            <tr>
                                <td><a href="{{ route('admin.forms.show', $item->id) }}" class="fw-semibold">#{{ $item->id }}</a></td>
                                <td>
                                    <span class="badge bg-info-subtle text-info text-uppercase">{{ $item->type }}</span>
                                </td>
                                <td>
                                    <h5 class="fs-14 mb-1">{{ $item->data['name'] ?? 'Ẩn danh' }}</h5>
                                    <p class="text-muted mb-0">{{ $item->data['email'] ?? ($item->user ? $item->user->email : '') }}</p>
                                </td>
                                <td>{{ display_datetime($item->created_at, 'd/m/Y H:i') }}</td>
                                <td>
                                    @if($item->status == 'pending')
                                        <span class="badge bg-warning-subtle text-warning">Chờ xử lý</span>
                                    @elseif($item->status == 'processing')
                                        <span class="badge bg-primary-subtle text-primary">Đang giải quyết</span>
                                    @elseif($item->status == 'resolved')
                                        <span class="badge bg-success-subtle text-success">Đã xử lý</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger">Bỏ qua</span>
                                    @endif
                                </td>
                                <td>
                                    <ul class="list-inline hstack gap-2 mb-0">
                                        <li class="list-inline-item">
                                            <a href="{{ route('admin.forms.show', $item->id) }}" class="text-primary d-inline-block">
                                                <i class="ri-eye-fill fs-16"></i>
                                            </a>
                                        </li>
                                        <li class="list-inline-item">
                                            <form action="{{ route('admin.forms.destroy', $item->id) }}" method="POST">
                                                @csrf @method('DELETE')
                                                <button type="button" class="btn btn-link text-danger p-0 confirm-delete"><i class="ri-delete-bin-5-fill fs-16"></i></button>
                                            </form>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">Chưa có dữ liệu.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                {{ $submissions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
