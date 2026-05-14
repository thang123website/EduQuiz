<div class="card question-card {{ $question->type == 'group' ? 'border-primary' : '' }}" data-id="{{ $question->id }}">
    <div class="card-header d-flex flex-column flex-sm-row align-items-start align-items-sm-center {{ $question->type == 'group' ? 'bg-soft-primary' : '' }}">
        <div class="flex-grow-1 mb-2 mb-sm-0">
            @if($question->type == 'group')
                <span class="badge bg-primary text-white me-2 mb-1">NHÓM (PASSAGE)</span>
            @else
                <span class="badge bg-soft-info text-info me-2 mb-1">{{ strtoupper($question->type) }}</span>
                <span class="fw-medium">Câu hỏi {{ isset($index) ? $index : '' }}: </span>
            @endif
            @php
                // Loại bỏ Markdown Image syntax ![alt](url) để hiển thị văn bản gọn gàng
                $cleanContent = preg_replace('/\!\[.*?\]\(.*?\)/', '', $question->content);
            @endphp
            <span class="text-muted d-block d-sm-inline mt-1 mt-sm-0">{{ Str::limit(strip_tags($cleanContent), 100) }}</span>
        </div>
        <div class="flex-shrink-0 d-flex align-items-center">
            @if($question->type != 'group')
            <span class="badge bg-light text-dark me-2">{{ $question->default_mark ?? 1 }} điểm</span>
            @else
            <button type="button" class="btn btn-sm btn-success me-2" onclick="openQuestionModal(null, '{{ $question->parts->first()->id ?? '' }}', '{{ $question->id }}')">
                <i class="ri-add-line"></i> Thêm câu hỏi con
            </button>
            @endif
            
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

        @if($question->type != 'group')
            <div class="row">
                @foreach($question->options as $idx => $option)
                    <div class="col-md-6">
                        <div class="option-item {{ $option->is_correct ? 'correct' : '' }}">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-2">
                                    <span class="avatar-title bg-light text-body rounded-circle" style="width: 24px; height: 24px; font-size: 12px;">
                                        {{ chr(65 + $idx) }}
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
        @else
            <!-- Render children if it's a group -->
            <div class="ps-4 mt-3 border-start border-2 border-primary">
                @foreach($question->children as $child)
                    @include('admin.quizzes.partials.question-card', ['question' => $child, 'index' => $loop->iteration])
                @endforeach
            </div>
        @endif
    </div>
</div>
