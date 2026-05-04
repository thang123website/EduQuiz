@extends('admin.layouts.master')
@section('title') Chi tiết thông báo @endsection
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Chi tiết thông báo</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('notifications.userList') }}">Thông báo</a></li>
                    <li class="breadcrumb-item active">Chi tiết</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">{{ $notification->data['title'] ?? 'Thông báo' }}</h4>
                <div class="flex-shrink-0">
                    <span class="text-muted"><i class="ri-calendar-line align-bottom"></i> {{ $notification->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
            <div class="card-body">
                @if(isset($notification->data['image']) && $notification->data['image'])
                <div class="mb-4 text-center">
                    <img src="{{ get_image_url($notification->data['image']) }}" alt="notification image" class="img-fluid rounded shadow-sm" style="max-height: 300px;">
                </div>
                @endif

                <div class="notification-content fs-15 text-muted">
                    {!! nl2br(e($notification->data['body'] ?? 'Không có nội dung.')) !!}
                </div>

                @if(isset($notification->data['url']) && $notification->data['url'])
                <div class="mt-4">
                    <a href="{{ $notification->data['url'] }}" class="btn btn-primary waves-effect waves-light">
                        <i class="ri-link-m align-middle me-1"></i> Truy cập liên kết
                    </a>
                </div>
                @endif
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('notifications.userList') }}" class="btn btn-link link-success fw-medium p-0">
                        <i class="ri-arrow-left-line align-middle me-1"></i> Quay lại danh sách
                    </a>
                    <form action="{{ route('notifications.deletePersonal', $notification->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa thông báo này?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-soft-danger">
                            <i class="ri-delete-bin-line align-middle me-1"></i> Xóa thông báo này
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
