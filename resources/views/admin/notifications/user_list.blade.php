@extends('admin.layouts.master')
@section('title') Thông báo của tôi @endsection

@push('styles')
<link href="{{ asset('assets/admin/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<style>
    .favourite-btn.text-warning i {
        color: #f7b84b !important;
    }
    .favourite-btn.text-muted i {
        color: #adb5bd !important;
    }
    /* Standard Mailbox UI Fix */
    .message-list li {
        height: 50px !important;
        position: relative !important;
        display: block !important;
        background: #fff;
        transition: all 0.2s;
        border-bottom: 1px solid var(--vz-border-color);
        overflow: hidden;
    }
    .message-list li:hover {
        background-color: var(--vz-light) !important;
        z-index: 1;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .message-list li .col-mail-1 {
        width: 280px !important;
        position: absolute !important;
        left: 0;
        top: 0;
        height: 50px;
        display: flex;
        align-items: center;
        padding: 0 15px;
        z-index: 2;
        background: inherit;
    }
    .message-list li .col-mail-2 {
        position: absolute !important;
        left: 280px !important;
        right: 0 !important;
        top: 0;
        height: 50px;
        display: flex;
        align-items: center;
        padding: 0 15px;
        background: inherit;
    }
    .message-list li .col-mail-2 .subject {
        flex-grow: 1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        padding-right: 150px; /* Tăng khoảng trống cho ngày tháng */
        color: var(--vz-body-color);
    }
    .message-list li .col-mail-2 .date {
        position: absolute;
        right: 20px;
        top: 0;
        line-height: 50px;
        color: var(--vz-muted);
        white-space: nowrap; /* Ngăn chặn xuống dòng gây đè chữ */
        background: inherit;
    }
    .message-list li.unread {
        background-color: var(--vz-light-subtle);
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="email-wrapper d-lg-flex gap-1 mx-n4 mt-n4 p-1 {{ isset($selectedNotification) ? 'email-detail-show' : '' }}">
    <div class="email-menu-sidebar">
        <div class="p-4 d-flex flex-column h-100">
            <div class="pb-4 border-bottom border-bottom-dashed">
                <h5 class="fs-16 mb-0">Hộp thư thông báo</h5>
            </div>

            <div class="mx-n4 px-4 email-menu-sidebar-scroll" data-simplebar>
                <div class="mail-list mt-3">
                    <a href="{{ route('notifications.userList') }}" class="{{ !$type ? 'active' : '' }}">
                        <i class="ri-inbox-archive-fill me-3 align-middle fw-medium"></i> 
                        <span class="mail-list-link">Tất cả</span> 
                        <span class="badge bg-success-subtle text-success ms-auto">{{ auth()->user()->notifications()->count() }}</span>
                    </a>
                    <a href="{{ route('notifications.userList', ['type' => 'unread']) }}" class="{{ $type === 'unread' ? 'active' : '' }}">
                        <i class="ri-mail-unread-fill me-3 align-middle fw-medium"></i> 
                        <span class="mail-list-link">Chưa đọc</span> 
                        <span class="badge bg-danger-subtle text-danger ms-auto">{{ $unreadCount }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- end email-menu-sidebar -->

    <div class="email-content">
        <div class="p-4 pb-0">
            <div class="border-bottom border-bottom-dashed">
                <div class="row mt-n2 mb-3 mb-sm-0">
                    <div class="col-sm order-3 order-sm-2">
                        <div class="hstack gap-sm-1 align-items-center flex-wrap email-topbar-link">
                            <div class="dropdown">
                                <button class="btn btn-ghost-secondary btn-icon btn-sm fs-16" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="ri-more-2-fill align-bottom"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#" id="mark-all-read">Đánh dấu tất cả là đã đọc</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto order-2 order-sm-3">
                        <div class="d-flex gap-sm-1 email-topbar-link">
                            <button type="button" class="btn btn-ghost-secondary btn-icon btn-sm fs-16" onclick="location.reload()">
                                <i class="ri-refresh-line align-bottom"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row align-items-end mt-3">
                    <div class="col">
                        <div id="mail-filter-navlist">
                            <ul class="nav nav-tabs nav-tabs-custom nav-success gap-1 text-center border-bottom-0" role="tablist">
                                <li class="nav-item">
                                    <button class="nav-link fw-semibold active" id="pills-primary-tab" data-bs-toggle="pill" data-bs-target="#pills-primary" type="button" role="tab" aria-controls="pills-primary" aria-selected="true">
                                        <i class="ri-inbox-fill align-bottom d-inline-block"></i>
                                        <span class="ms-1 d-none d-sm-inline-block">Thông báo của bạn</span>
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="text-muted mb-2">
                            {{ $notifications->firstItem() ?? 0 }}-{{ $notifications->lastItem() ?? 0 }} của {{ $notifications->total() }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="pills-primary" role="tabpanel" aria-labelledby="pills-primary-tab">
                    <div class="message-list-content mx-n4 px-4 message-list-scroll" data-simplebar>
                        <ul class="message-list">
                            @forelse($notifications as $notification)
                            <li class="{{ $notification->read_at ? '' : 'unread' }} {{ isset($selectedNotification) && $selectedNotification->id == $notification->id ? 'active' : '' }}">
                                <div class="col-mail col-mail-1">
                                    <div class="form-check checkbox-wrapper-mail fs-14">
                                        <input class="form-check-input" type="checkbox" value="{{ $notification->id }}">
                                        <label class="form-check-label"></label>
                                    </div>
                                    <button type="button" class="btn avatar-xs p-0 favourite-btn fs-15 {{ $notification->read_at ? 'text-muted' : 'text-warning' }}">
                                        <i class="ri-star-fill"></i>
                                    </button>
                                    <a href="{{ route('notifications.show', $notification->id) }}" class="title">
                                        Hệ thống EduQuiz
                                    </a>
                                </div>
                                <div class="col-mail col-mail-2">
                                    <a href="{{ route('notifications.show', $notification->id) }}" class="subject">
                                        <span class="teaser">{{ $notification->data['title'] ?? 'N/A' }}</span> - {{ Str::limit($notification->data['body'] ?? '', 100) }}
                                    </a>
                                    <div class="date">{{ $notification->created_at->diffForHumans() }}</div>
                                </div>
                            </li>
                            @empty
                            <li class="text-center p-4">
                                <h5>Không có thông báo nào</h5>
                            </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="p-3">
                {{ $notifications->links() }}
            </div>
        </div>
    </div>
    <!-- end email-content -->

    @if(isset($selectedNotification))
    <div class="email-detail-content show">
        <div class="p-4 d-flex flex-column h-100">
            <div class="pb-4 border-bottom border-bottom-dashed">
                <div class="row">
                    <div class="col">
                        <div class="">
                            <a href="{{ route('notifications.userList') }}" class="btn btn-soft-danger btn-icon btn-sm fs-16">
                                <i class="ri-close-fill align-bottom"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="hstack gap-sm-1 align-items-center flex-wrap email-topbar-link">
                            <button type="button" class="btn btn-ghost-secondary btn-icon btn-sm fs-16" onclick="window.print()">
                                <i class="ri-printer-fill align-bottom"></i>
                            </button>
                            <button class="btn btn-ghost-secondary btn-icon btn-sm fs-16" onclick="deleteSingleNotification('{{ $selectedNotification->id }}')">
                                <i class="ri-delete-bin-5-fill align-bottom"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mx-n4 px-4 email-detail-content-scroll" data-simplebar>
                <div class="mt-4 mb-3">
                    <h5 class="fw-bold email-subject-title">{{ $selectedNotification->data['title'] ?? 'Thông báo' }}</h5>
                </div>

                <div class="accordion accordion-flush">
                    <div class="accordion-item border-dashed left">
                        <div class="accordion-header">
                            <div class="btn w-100 text-start px-0 bg-transparent shadow-none">
                                <div class="d-flex align-items-center text-muted">
                                    <div class="flex-shrink-0 avatar-xs me-3">
                                        <div class="avatar-title bg-primary-subtle text-primary rounded-circle">
                                            H
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <h5 class="fs-14 text-truncate email-user-name mb-0">Hệ thống EduQuiz</h5>
                                        <div class="text-truncate fs-12">tới: tôi</div>
                                    </div>
                                    <div class="flex-shrink-0 align-self-start">
                                        <div class="text-muted fs-12">{{ $selectedNotification->created_at->format('d M Y, H:i') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-collapse collapse show">
                            <div class="accordion-body text-body px-0">
                                <div>
                                    @if(isset($selectedNotification->data['image']) && $selectedNotification->data['image'])
                                    <div class="mb-4">
                                        <img src="{{ get_image_url($selectedNotification->data['image']) }}" alt="notification image" class="img-fluid rounded shadow-sm" style="max-height: 400px;">
                                    </div>
                                    @endif

                                    <div class="fs-15">
                                        {!! nl2br(e($selectedNotification->data['body'] ?? '')) !!}
                                    </div>

                                    @if(isset($selectedNotification->data['url']) && $selectedNotification->data['url'])
                                    <div class="mt-4 border-top border-top-dashed pt-3">
                                        <p class="mb-2 text-muted">Liên kết đính kèm:</p>
                                        <a href="{{ $selectedNotification->data['url'] }}" class="btn btn-primary btn-label waves-effect waves-light">
                                            <i class="ri-link align-middle m-1 label-icon"></i> Truy cập ngay
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/admin/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script>
    function deleteSingleNotification(id) {
        Swal.fire({
            title: 'Xác nhận xóa?',
            text: "Thông báo này sẽ bị xóa vĩnh viễn!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonClass: 'btn btn-primary w-xs me-2 mt-2',
            cancelButtonClass: 'btn btn-danger w-xs mt-2',
            confirmButtonText: 'Đúng, xóa nó!',
            cancelButtonText: 'Hủy bỏ',
            buttonsStyling: false,
            showCloseButton: true
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/notifications/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Đã xóa!',
                            text: 'Thông báo đã được loại bỏ.',
                            icon: 'success',
                            confirmButtonClass: 'btn btn-primary w-xs mt-2',
                            buttonsStyling: false
                        }).then(() => {
                            window.location.href = "{{ route('notifications.userList') }}";
                        });
                    } else {
                        Swal.fire('Lỗi', data.message, 'error');
                    }
                });
            }
        });
    }

    document.getElementById('mark-all-read')?.addEventListener('click', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Xác nhận?',
            text: "Đánh dấu tất cả thông báo là đã đọc?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonClass: 'btn btn-primary w-xs me-2 mt-2',
            cancelButtonClass: 'btn btn-danger w-xs mt-2',
            confirmButtonText: 'Đồng ý',
            cancelButtonText: 'Hủy',
            buttonsStyling: false,
            showCloseButton: true
        }).then((result) => {
            if (result.isConfirmed) {
                fetch("{{ route('notifications.markAllAsRead') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                });
            }
        });
    });
</script>
@endpush
