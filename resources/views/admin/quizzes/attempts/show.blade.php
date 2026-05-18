@extends('admin.layouts.master')

@section('title', 'Chi tiết lượt thi')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Chi tiết lượt thi</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.quiz-attempts.index') }}">Lịch sử thi</a></li>
                    <li class="breadcrumb-item active">Chi tiết</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center">
                <img src="{{ $attempt->user->avatar_url }}" class="rounded-circle avatar-lg img-thumbnail mb-3" alt="">
                <h5 class="mb-1">{{ $attempt->user->name }}</h5>
                <p class="text-muted">{{ $attempt->user->email }}</p>
                <div class="d-flex justify-content-around mt-4">
                    <div>
                        <h4 class="mb-0">{{ $attempt->score }}%</h4>
                        <p class="text-muted mb-0">Điểm số</p>
                    </div>
                    <div>
                        <h4 class="mb-0">{{ $attempt->correct_count }}/{{ $attempt->total_count }}</h4>
                        <p class="text-muted mb-0">Câu đúng</p>
                    </div>
                </div>
                <hr class="my-4">
                <div class="text-start">
                    <p class="mb-2"><span class="fw-bold">Đề thi:</span> {{ $attempt->quiz->title }}</p>
                    <p class="mb-2"><span class="fw-bold">Thời gian:</span> 
                        @php
                            $min = floor($attempt->time_spent / 60);
                            $sec = $attempt->time_spent % 60;
                        @endphp
                        {{ $min > 0 ? $min . ' phút ' : '' }}{{ $sec }} giây
                    </p>
                    <p class="mb-2"><span class="fw-bold">Ngày thực hiện:</span> {{ display_datetime($attempt->created_at, 'H:i d/m/Y') }}</p>
                    <p class="mb-0"><span class="fw-bold">Trạng thái:</span> 
                        @if($attempt->status == 'passed')
                            <span class="badge bg-success">PASSED</span>
                        @elseif($attempt->status == 'failed')
                            <span class="badge bg-danger">FAILED</span>
                        @else
                            <span class="badge bg-info">COMPLETED</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Chi tiết câu trả lời</h5>
            </div>
            <div class="card-body">
                @foreach($attempt->responses as $index => $response)
                    <div class="mb-4 p-3 border rounded {{ $response->is_correct ? 'border-success bg-soft-success' : 'border-danger bg-soft-danger' }}">
                        <div class="d-flex mb-2">
                            <span class="badge {{ $response->is_correct ? 'bg-success' : 'bg-danger' }} me-2">Câu {{ $index + 1 }}</span>
                            <div class="fw-bold">{!! $response->question->content !!}</div>
                        </div>
                        
                        <div class="ms-4">
                            @foreach($response->question->options as $option)
                                <div class="form-check mb-1">
                                    <input class="form-check-input" type="radio" disabled 
                                        {{ $response->selected_option_id == $option->id ? 'checked' : '' }}>
                                    <label class="form-check-label {{ $option->is_correct ? 'text-success fw-bold' : ($response->selected_option_id == $option->id ? 'text-danger' : '') }}">
                                        {{ $option->text }}
                                        @if($option->is_correct)
                                            <i class="ri-checkbox-circle-fill ms-1"></i> (Đáp án đúng)
                                        @endif
                                        @if($response->selected_option_id == $option->id && !$option->is_correct)
                                            <i class="ri-close-circle-fill ms-1"></i> (Đã chọn)
                                        @endif
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        
                        @if($response->question->explanation)
                            <div class="mt-2 small text-muted">
                                <strong>Giải thích:</strong> {!! $response->question->explanation !!}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
