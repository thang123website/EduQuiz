@extends('admin.layouts.master')

@section('title', 'Quản lý Bình luận')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Quản lý Bình luận</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Hệ thống</a></li>
                    <li class="breadcrumb-item active">Bình luận</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Danh sách bình luận</h4>
                <div class="flex-shrink-0">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCommentModal">
                        <i class="ri-add-line align-bottom me-1"></i> Thêm mới
                    </button>
                </div>
            </div><!-- end card header -->

            <!-- Modal Thêm bình luận mới -->
            <div class="modal fade" id="createCommentModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Thêm bình luận mới</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('admin.comments.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="commentable_type" value="App\Models\Blog">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Chọn bài viết</label>
                                    <select name="commentable_id" class="form-control" required>
                                        <option value="">-- Chọn bài viết --</option>
                                        @foreach($blogs as $blog)
                                            <option value="{{ $blog->id }}">{{ $blog->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Nội dung bình luận</label>
                                    <textarea name="content" class="form-control" rows="4" required placeholder="Nhập nội dung bình luận..."></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
                                <button type="submit" class="btn btn-primary">Lưu bình luận</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-body border border-dashed border-end-0 border-start-0">
                <form action="{{ route('admin.comments.index') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-xxl-3 col-sm-6">
                            <div class="search-box">
                                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm nội dung..." value="{{ request('search') }}">
                                <i class="ri-search-line search-icon"></i>
                            </div>
                        </div>
                        <div class="col-xxl-2 col-sm-4">
                            <select class="form-control" name="status" onchange="this.form.submit()">
                                <option value="all" {{ $status == 'all' ? 'selected' : '' }}>Tất cả trạng thái</option>
                                <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                                <option value="active" {{ $status == 'active' ? 'selected' : '' }}>Đã hiển thị</option>
                            </select>
                        </div>
                        <div class="col-xxl-1 col-sm-2">
                            <button type="submit" class="btn btn-primary w-100"> <i class="ri-equalizer-fill me-1 align-bottom"></i> Lọc </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-nowrap align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Người bình luận</th>
                                <th scope="col">Nội dung</th>
                                <th scope="col">Tại</th>
                                <th scope="col">Ngày gửi</th>
                                <th scope="col">Trạng thái</th>
                                <th scope="col">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($comments as $comment)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <img src="{{ $comment->user->avatar_url }}" alt="" class="avatar-xs rounded-circle shadow">
                                        </div>
                                        <div class="flex-grow-1 ms-2 name">{{ $comment->user->name }}</div>
                                    </div>
                                </td>
                                <td style="max-width: 300px; white-space: normal;">
                                    <span class="text-muted">{{ Str::limit($comment->content, 100) }}</span>
                                    @if($comment->replies->count() > 0)
                                        <div class="mt-1"><span class="badge bg-soft-info text-info">{{ $comment->replies->count() }} phản hồi</span></div>
                                    @endif
                                </td>
                                <td>
                                    @if($comment->commentable)
                                        <a href="#" class="text-primary">{{ $comment->commentable->title ?? 'Nội dung' }}</a>
                                        <div class="text-muted fs-11">{{ class_basename($comment->commentable_type) }}</div>
                                    @else
                                        <span class="text-danger">Đã bị xóa</span>
                                    @endif
                                </td>
                                <td>{{ $comment->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($comment->status == 'active')
                                        <span class="badge bg-success-subtle text-success text-uppercase">Đã duyệt</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning text-uppercase">Chờ duyệt</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-sm {{ $comment->status == 'active' ? 'btn-soft-warning' : 'btn-soft-success' }}" 
                                                onclick="toggleCommentStatus('{{ $comment->id }}', this)">
                                            <i class="ri-check-line"></i> {{ $comment->status == 'active' ? 'Ẩn' : 'Duyệt' }}
                                        </button>
                                        <button type="button" class="btn btn-sm btn-soft-primary" data-bs-toggle="modal" data-bs-target="#replyModal{{ $comment->id }}">
                                            <i class="ri-reply-line"></i> Trả lời
                                        </button>
                                        <form action="{{ route('admin.comments.destroy', $comment->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-soft-danger confirm-delete"><i class="ri-delete-bin-line"></i></button>
                                        </form>
                                    </div>

                                    <!-- Modal Trả lời -->
                                    <div class="modal fade" id="replyModal{{ $comment->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Phản hồi bình luận của {{ $comment->user->name }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('admin.comments.reply', $comment->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="bg-light p-3 rounded mb-3">
                                                            <p class="mb-0 italic">"{{ $comment->content }}"</p>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Nội dung phản hồi</label>
                                                            <textarea name="content" class="form-control" rows="4" required placeholder="Nhập nội dung phản hồi..."></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
                                                        <button type="submit" class="btn btn-primary">Gửi phản hồi</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">Chưa có bình luận nào.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $comments->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleCommentStatus(id, btn) {
    fetch(`{{ url('admin/comments') }}/${id}/toggle`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            Toastify({
                text: data.message,
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                className: data.status === 'active' ? "bg-success" : "bg-warning",
            }).showToast();
            setTimeout(() => location.reload(), 1000);
        }
    });
}
</script>
@endpush
