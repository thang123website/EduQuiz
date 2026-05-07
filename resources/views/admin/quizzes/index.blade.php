@extends('admin.layouts.master')

@section('title', 'Danh sách Đề thi')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Quản lý Đề thi</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Danh sách Quiz</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Thống kê nhanh -->
<div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1 overflow-hidden">
                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Tổng số đề thi</p>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-4">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">{{ $stats['total'] }}</h4>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-soft-info rounded fs-3">
                            <i class="ri-file-list-3-line text-info"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1 overflow-hidden">
                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Đã xuất bản</p>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-4">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">{{ $stats['published'] }}</h4>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-soft-success rounded fs-3">
                            <i class="ri-checkbox-circle-line text-success"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1 overflow-hidden">
                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Lượt thi</p>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-4">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">{{ $stats['total_attempts'] }}</h4>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-soft-warning rounded fs-3">
                            <i class="ri-user-star-line text-warning"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1 overflow-hidden">
                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Bản nháp</p>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-4">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">{{ $stats['draft'] }}</h4>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-soft-danger rounded fs-3">
                            <i class="ri-edit-2-line text-danger"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header border-0">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">Tất cả đề thi</h5>
                    <div class="flex-shrink-0">
                        <a href="{{ route('admin.quizzes.create') }}" class="btn btn-primary add-btn">
                            <i class="ri-add-line align-bottom me-1"></i> Thêm mới
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body border border-dashed border-end-0 border-start-0">
                <form action="{{ route('admin.quizzes.index') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-xxl-5 col-sm-6">
                            <div class="search-box">
                                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Tìm kiếm theo tiêu đề...">
                                <i class="ri-search-line search-icon"></i>
                            </div>
                        </div>
                        <div class="col-xxl-3 col-sm-4">
                            <select class="form-select" name="category_id">
                                <option value="">Tất cả danh mục</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xxl-2 col-sm-4">
                            <select class="form-select" name="status">
                                <option value="">Tất cả trạng thái</option>
                                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                        </div>
                        <div class="col-xxl-2 col-sm-4">
                            <button type="submit" class="btn btn-info w-100">
                                <i class="ri-equalizer-fill me-1 align-bottom"></i> Lọc
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-body">
                <div class="table-responsive table-card mb-4">
                    <table class="table align-middle table-nowrap mb-0">
                        <thead class="table-light text-muted">
                            <tr>
                                <th>Tiêu đề</th>
                                <th>Danh mục</th>
                                <th>Thời gian</th>
                                <th>Câu hỏi</th>
                                <th>Lượt thi</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($quizzes as $quiz)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($quiz->thumbnail)
                                            <img src="{{ get_image_url($quiz->thumbnail) }}" class="avatar-xs rounded me-2" alt="">
                                        @else
                                            <div class="avatar-xs flex-shrink-0 me-2">
                                                <div class="avatar-title bg-soft-primary text-primary rounded">Q</div>
                                            </div>
                                        @endif
                                        <div>
                                            <h6 class="mb-0">{{ $quiz->title }}</h6>
                                            <small class="text-muted">{{ $quiz->difficulty }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $quiz->category->name }}</td>
                                <td>{{ $quiz->duration }} phút</td>
                                <td>{{ $quiz->question_count }}</td>
                                <td>{{ $quiz->attempts_count }}</td>
                                <td>
                                    @if($quiz->status == 'published')
                                        <span class="badge bg-success text-uppercase">Published</span>
                                    @elseif($quiz->status == 'draft')
                                        <span class="badge bg-warning text-uppercase">Draft</span>
                                    @else
                                        <span class="badge bg-danger text-uppercase">Archived</span>
                                    @endif
                                </td>
                                <td>
                                    <ul class="list-inline hstack gap-2 mb-0">
                                        <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Lịch sử thi">
                                            <a href="{{ route('admin.quiz-attempts.index', ['quiz_id' => $quiz->id]) }}" class="text-info d-inline-block">
                                                <i class="ri-history-line fs-16"></i>
                                            </a>
                                        </li>
                                        <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Chỉnh sửa">
                                            <a href="{{ route('admin.quizzes.edit', $quiz->id) }}" class="text-primary d-inline-block">
                                                <i class="ri-pencil-fill fs-16"></i>
                                            </a>
                                        </li>
                                        <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Xóa">
                                            <form action="{{ route('admin.quizzes.destroy', $quiz->id) }}" method="POST" class="d-inline">
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
                                <td colspan="7" class="text-center py-5">
                                    <h5 class="mt-2 text-muted">Không tìm thấy đề thi nào</h5>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end">
                    {{ $quizzes->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
