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
                                    <small class="text-muted">{{ $attempt->quiz->category->name }}</small>
                                </td>
                                <td>
                                    <div class="text-{{ $attempt->status == 'passed' ? 'success' : ($attempt->status == 'failed' ? 'danger' : 'info') }}">
                                        <span class="fw-bold fs-14">{{ $attempt->score }}%</span>
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
                                <td>{{ $attempt->created_at->format('H:i d/m/Y') }}</td>
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
                                <td colspan="7" class="text-center py-5">
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
