<div class="comments-container mt-5">
    <h4 class="mb-4">Bình luận ({{ $model->activeComments->count() }})</h4>

    @auth
    <div class="comment-form mb-5">
        <form id="commentForm">
            @csrf
            <input type="hidden" name="commentable_id" value="{{ $model->id }}">
            <input type="hidden" name="commentable_type" value="{{ get_class($model) }}">
            <div class="mb-3">
                <textarea name="content" class="form-control" rows="3" placeholder="Viết bình luận của bạn..."></textarea>
            </div>
            <div class="text-end">
                <button type="submit" class="btn btn-primary">Gửi bình luận</button>
            </div>
        </form>
    </div>
    @else
    <div class="alert alert-info">
        Vui lòng <a href="{{ route('login') }}">đăng nhập</a> để bình luận.
    </div>
    @endauth

    <div class="comments-list" id="commentsList">
        @foreach($model->activeComments as $comment)
            <div class="comment-item mb-4 pb-3 border-bottom" id="comment-{{ $comment->id }}">
                <div class="d-flex">
                    <img src="{{ $comment->user->avatar_url }}" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="mb-0 fw-bold">{{ $comment->user->name }}</h6>
                            <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                        </div>
                        <p class="text-muted mb-2">{{ $comment->content }}</p>
                        
                        @auth
                        <a href="javascript:void(0)" class="text-primary fs-12 fw-medium" onclick="showReplyForm('{{ $comment->id }}')">
                            <i class="ri-reply-line me-1"></i> Trả lời
                        </a>
                        @endauth

                        <!-- Replies -->
                        <div class="replies-list ms-5 mt-3">
                            @foreach($comment->replies as $reply)
                            <div class="reply-item mb-3">
                                <div class="d-flex">
                                    <img src="{{ $reply->user->avatar_url }}" class="rounded-circle me-2" style="width: 35px; height: 35px; object-fit: cover;">
                                    <div>
                                        <div class="d-flex align-items-center mb-1">
                                            <h6 class="mb-0 fs-13 fw-bold">{{ $reply->user->name }}</h6>
                                            <small class="text-muted ms-2 fs-11">{{ $reply->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p class="text-muted mb-0 fs-13">{{ $reply->content }}</p>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Reply Form (Hidden) -->
                        <div class="reply-form-container ms-5 mt-3" id="reply-form-{{ $comment->id }}" style="display: none;">
                            <form class="replyForm">
                                @csrf
                                <input type="hidden" name="commentable_id" value="{{ $model->id }}">
                                <input type="hidden" name="commentable_type" value="{{ get_class($model) }}">
                                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                <div class="mb-2">
                                    <textarea name="content" class="form-control form-control-sm" rows="2" placeholder="Viết phản hồi..."></textarea>
                                </div>
                                <div class="text-end">
                                    <button type="button" class="btn btn-light btn-sm me-2" onclick="showReplyForm('{{ $comment->id }}')">Hủy</button>
                                    <button type="submit" class="btn btn-primary btn-sm">Gửi</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const commentForm = document.getElementById('commentForm');
    if(commentForm) {
        commentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitComment(this);
        });
    }

    document.querySelectorAll('.replyForm').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            submitComment(this);
        });
    });
});

function showReplyForm(id) {
    const form = document.getElementById(`reply-form-${id}`);
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
}

function submitComment(form) {
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Đang gửi...';

    fetch('{{ route('comments.store') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert(data.message);
            form.reset();
            if(form.classList.contains('replyForm')) {
                form.closest('.reply-form-container').style.display = 'none';
            }
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra, vui lòng thử lại.');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = form.classList.contains('replyForm') ? 'Gửi' : 'Gửi bình luận';
    });
}
</script>
