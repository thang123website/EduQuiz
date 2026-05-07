<div class="card question-card" data-id="{{ $question->id }}">
    <div class="card-header d-flex align-items-center">
        <div class="flex-grow-1">
            <span class="badge bg-soft-info text-info me-2">{{ strtoupper($question->type) }}</span>
            <span class="fw-medium">Câu hỏi {{ isset($index) ? $index : '' }}: </span>
            <span class="text-muted">{{ Str::limit(strip_tags($question->content), 100) }}</span>
        </div>
        <div class="flex-shrink-0">
            <span class="badge bg-light text-dark me-2">{{ $question->grade }} điểm</span>
            @if(auth()->user()->isAdmin() || auth()->user()->hasPermission('exams.update'))
            <button type="button" class="btn btn-soft-primary btn-sm" onclick="editQuestion('{{ $question->id }}')">
                <i class="ri-pencil-line"></i>
            </button>
            @endif
            @if(auth()->user()->isAdmin() || auth()->user()->hasPermission('exams.delete'))
            <form action="{{ route('admin.questions.destroy', $question->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="button" class="btn btn-soft-danger btn-sm confirm-delete">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </form>
            @endif
        </div>
    </div>
    <div class="card-body">
        @if($question->media_url)
            <div class="mb-3">
                @if($question->media_type == 'image')
                    <img src="{{ get_image_url($question->media_url) }}" class="img-fluid rounded border" style="max-height: 150px;">
                @elseif($question->media_type == 'audio')
                    <audio controls class="w-100"><source src="{{ get_image_url($question->media_url) }}" type="audio/mpeg"></audio>
                @endif
            </div>
        @endif

        <div class="row">
            @foreach($question->options as $index => $option)
                <div class="col-md-6">
                    <div class="option-item {{ $option->is_correct ? 'correct' : '' }}">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-2">
                                <span class="avatar-title bg-light text-body rounded-circle" style="width: 24px; height: 24px; font-size: 12px;">
                                    {{ chr(65 + $index) }}
                                </span>
                            </div>
                            <div class="flex-grow-1">{{ $option->text }}</div>
                            @if($option->is_correct)
                                <div class="flex-shrink-0"><i class="ri-checkbox-circle-fill text-success fs-18"></i></div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($question->explanation)
            <div class="mt-2 p-2 bg-light rounded fs-13">
                <strong>Giải thích:</strong> {{ $question->explanation }}
            </div>
        @endif
    </div>
</div>
