@extends('admin.layouts.master')

@section('title', 'Lịch sử thông báo')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Lịch sử thông báo</h4>
            <div class="page-title-right">
                <a href="{{ route('admin.notifications.create') }}" class="btn btn-primary">
                    <i class="ri-add-line align-bottom me-1"></i> Gửi thông báo mới
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body border-bottom-dashed border-bottom">
                <form action="{{ route('admin.notifications.index') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-xl-4">
                            <div class="search-box">
                                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm tiêu đề hoặc nội dung..." value="{{ request('search') }}">
                                <i class="ri-search-line search-icon"></i>
                            </div>
                        </div>
                        <div class="col-xl-3">
                            <div>
                                <select class="form-control" name="channel">
                                    <option value="">Tất cả kênh gửi</option>
                                    @foreach($channels as $key => $label)
                                        <option value="{{ $key }}" {{ request('channel') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-3">
                            <div>
                                <select class="form-control" name="sender_id">
                                    <option value="">Tất cả người gửi</option>
                                    @foreach($senders as $sender)
                                        <option value="{{ $sender->id }}" {{ request('sender_id') == $sender->id ? 'selected' : '' }}>{{ $sender->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-2">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="ri-equalizer-fill me-1 align-bottom"></i> Hiển thị kết quả
                                </button>
                                @if(request()->anyFilled(['search', 'channel', 'sender_id']))
                                    <a href="{{ route('admin.notifications.index') }}" class="btn btn-light w-100">Xoá</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-centered align-middle table-nowrap mb-0">
                        <thead class="text-muted table-light">
                            <tr>
                                <th scope="col">Ngày gửi</th>
                                <th scope="col">Tiêu đề</th>
                                <th scope="col">Đối tượng</th>
                                <th scope="col">Số người nhận</th>
                                <th scope="col">Kênh gửi</th>
                                <th scope="col">Người gửi</th>
                                <th scope="col">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($histories as $history)
                            <tr>
                                <td>{{ $history->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($history->image_url)
                                            <div class="flex-shrink-0 me-2">
                                                <img src="{{ $history->image_url }}" alt="" class="avatar-xs rounded-circle">
                                            </div>
                                        @endif
                                        <div class="flex-grow-1">
                                            <span class="fw-medium">{{ $history->title }}</span>
                                            <br>
                                            <small class="text-muted text-truncate d-inline-block" style="max-width: 200px;">
                                                {{ Str::limit($history->body, 50) }}
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info-subtle text-info">
                                        {{ $audienceTypes[$history->audience_type] ?? $history->audience_type }}
                                    </span>
                                </td>
                                <td>{{ number_format($history->user_count) }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        @foreach($history->channels as $channel)
                                            @php
                                                $badgeClass = match($channel) {
                                                    'database' => 'bg-primary',
                                                    'mail' => 'bg-success',
                                                    'fcm' => 'bg-warning',
                                                    default => 'bg-secondary'
                                                };
                                                $label = match($channel) {
                                                    'database' => 'Web',
                                                    'mail' => 'Email',
                                                    'fcm' => 'Firebase',
                                                    default => $channel
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">{{ $label }}</span>
                                        @endforeach
                                    </div>
                                </td>
                                <td>{{ $history->sender->name ?? 'N/A' }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.notifications.create', ['from_history' => $history->id]) }}" class="btn btn-sm btn-soft-primary">
                                            <i class="ri-edit-box-line align-bottom"></i>
                                        </a>
                                        <form action="{{ route('admin.notifications.destroy', $history->id) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <a href="javascript:void(0);" class="btn btn-sm btn-soft-danger confirm-delete">
                                                <i class="ri-delete-bin-fill align-bottom"></i>
                                            </a>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">Chưa có lịch sử thông báo nào.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $histories->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
