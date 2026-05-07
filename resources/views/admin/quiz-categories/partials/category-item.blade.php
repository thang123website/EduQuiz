<div class="list-group-item" data-id="{{ $category->id }}">
    <div class="d-flex align-items-center">
        <div class="flex-shrink-0">
            <i class="ri-drag-move-fill handle"></i>
        </div>
        <div class="flex-grow-1">
            <div class="d-flex align-items-center">
                @if($category->icon)
                    <i class="{{ $category->icon }} fs-18 text-primary me-2"></i>
                @else
                    <i class="ri-folder-line fs-18 text-primary me-2"></i>
                @endif
                <h6 class="mb-0">{{ $category->name }}</h6>
                <span class="badge bg-soft-info text-info ms-2">{{ strtoupper($category->type) }}</span>
                @if(!$category->is_active)
                    <span class="badge bg-soft-danger text-danger ms-1">Tắt</span>
                @endif
                <small class="text-muted ms-2">({{ $category->quizzes_count ?? 0 }} quiz)</small>
            </div>
        </div>
        <div class="flex-shrink-0">
            <button class="btn btn-soft-primary btn-sm edit-item" data-item="{{ json_encode($category) }}">
                <i class="ri-pencil-line"></i>
            </button>
            <form action="{{ route('admin.quiz-categories.destroy', $category->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="button" class="btn btn-soft-danger btn-sm confirm-delete">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </form>
        </div>
    </div>

    @if($category->children->count() > 0)
        <div class="children">
            @foreach($category->children as $child)
                @include('admin.quiz-categories.partials.category-item', ['category' => $child])
            @endforeach
        </div>
    @endif
</div>
