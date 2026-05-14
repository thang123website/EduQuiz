<!-- Question Modal -->
<div class="modal fade" id="questionModal" tabindex="-1" aria-labelledby="questionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light p-3">
                <h5 class="modal-title" id="questionModalLabel">Thêm câu hỏi mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>
            <form id="question-form" action="{{ route('admin.questions.store') }}" method="POST">
                @csrf
                <input type="hidden" name="part_id" id="q-part-id" value="">
                <input type="hidden" name="quiz_id" id="q-quiz-id" value="{{ $quiz->id }}">
                <input type="hidden" name="_method" id="q-method" value="POST">
                <input type="hidden" name="id" id="q-id">
                <input type="hidden" name="parent_id" id="q-parent-id" value="">

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-lg-12">
                            <label for="question-type" class="form-label">Loại câu hỏi</label>
                            <select class="form-select" id="question-type" name="type">
                                <option value="single_choice">Một đáp án đúng (Single Choice)</option>
                                <option value="multiple_answer">Nhiều đáp án đúng (Multiple Choice)</option>
                                <option value="descriptive">Tự luận/Mô tả</option>
                                <option value="group">Nhóm câu hỏi (Chỉ chứa Audio/Passage chung)</option>
                            </select>
                        </div>
                        <div class="col-lg-12">
                            <label class="form-label">Nội dung câu hỏi <span class="text-danger">*</span></label>
                            <textarea id="question-content-editor" name="content"></textarea>
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label">Media (URL)</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="media_url" id="q-media-url" placeholder="https://...">
                                <button class="btn btn-outline-primary" type="button" onclick="openMediaPicker('q-media-url', 'q-media-preview')">Chọn</button>
                            </div>
                            <div id="q-media-preview-wrap" class="mt-2 d-none">
                                <img src="" id="q-media-preview" class="img-fluid rounded border" style="max-height: 100px;">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <label class="form-label">Loại Media</label>
                            <select class="form-select" name="media_type">
                                <option value="none">None</option>
                                <option value="image">Image</option>
                                <option value="audio">Audio</option>
                            </select>
                        </div>
                        <div class="col-lg-3" id="mark-section">
                            <label class="form-label">Điểm mặc định</label>
                            <input type="number" step="0.5" class="form-control" name="default_mark" id="q-default-mark" value="1.0">
                        </div>

                        <!-- Options Section -->
                        <div class="col-lg-12" id="options-section">
                            <div class="d-flex align-items-center mb-2">
                                <h6 class="flex-grow-1 mb-0">Danh sách đáp án</h6>
                                <button type="button" class="btn btn-soft-info btn-sm" onclick="addOption()">
                                    <i class="ri-add-line"></i> Thêm đáp án
                                </button>
                            </div>
                            <div id="options-container">
                                <!-- Options will be added here via JS -->
                            </div>
                        </div>

                        <div class="col-lg-12" id="explanation-section">
                            <label class="form-label">Giải thích đáp án</label>
                            <textarea class="form-control" name="explanation" id="q-explanation" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-success" id="add-btn">Lưu câu hỏi</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let optionIndex = 0;

    function addOption(text = '', isCorrect = false, id = null) {
        const container = document.getElementById('options-container');
        const div = document.createElement('div');
        div.className = 'option-item-input row g-2 mb-2 align-items-center';
        div.innerHTML = `
            <div class="col-auto">
                <div class="form-check">
                    <input class="form-check-input q-is-correct-check" type="checkbox" ${isCorrect ? 'checked' : ''} onchange="this.nextElementSibling.value = this.checked ? 1 : 0">
                    <input type="hidden" name="options[${optionIndex}][is_correct]" value="${isCorrect ? 1 : 0}">
                </div>
            </div>
            <div class="col">
                <input type="text" class="form-control form-control-sm" name="options[${optionIndex}][text]" value="${text}" placeholder="Nhập đáp án..." required>
                ${id ? `<input type="hidden" name="options[${optionIndex}][id]" value="${id}">` : ''}
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-soft-danger btn-sm" onclick="this.closest('.option-item-input').remove()">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </div>
        `;
        container.appendChild(div);
        optionIndex++;
    }

    // Handle Form Submission
    document.getElementById('question-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const formData = new FormData(form);
        const url = form.action;
        const method = document.getElementById('q-method').value;

        // Ensure markdown content is updated from SimpleMDE
        const content = mdeEditor.value();
        if (!content.trim()) {
            Toastify({
                text: "Vui lòng nhập nội dung câu hỏi",
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "#f06548",
            }).showToast();
            return;
        }
        formData.set('content', content);

        fetch(url, {
            method: 'POST', // We use POST with _method spoofing if needed, or PUT if method is PUT
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Toastify({
                    text: data.message,
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#0ab39c",
                }).showToast();
                
                bootstrap.Modal.getInstance(document.getElementById('questionModal')).hide();
                location.reload(); // Simple reload for now, can be optimized to inject HTML
            } else {
                alert('Có lỗi xảy ra: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi lưu câu hỏi.');
        });
    });

    function editQuestion(id) {
        // Fetch question data via AJAX
        fetch(PATH_ROOT + '/admin/questions/' + id, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const q = data.data;
                document.getElementById('questionModalLabel').innerText = 'Chỉnh sửa câu hỏi';
                document.getElementById('q-id').value = q.id;
                document.getElementById('q-method').value = 'PUT';
                document.getElementById('question-form').action = PATH_ROOT + '/admin/questions/' + q.id;
                
                const typeSelect = document.getElementById('question-type');
                typeSelect.value = q.type;
                typeSelect.dispatchEvent(new Event('change'));
                
                mdeEditor.value(q.content);
                document.getElementById('q-media-url').value = q.media_url || '';
                document.querySelector('select[name="media_type"]').value = q.media_type;
                document.querySelector('input[name="default_mark"]').value = q.default_mark;
                document.querySelector('textarea[name="explanation"]').value = q.explanation || '';
                
                // Clear and repopulate options
                const container = document.getElementById('options-container');
                container.innerHTML = '';
                optionIndex = 0;
                q.options.forEach(opt => {
                    addOption(opt.text, opt.is_correct, opt.id);
                });
                
                const modal = new bootstrap.Modal(document.getElementById('questionModal'));
                modal.show();
            }
        });
    }

    // Reset form when modal is closed
    document.getElementById('questionModal').addEventListener('hidden.bs.modal', function () {
        document.getElementById('question-form').reset();
        document.getElementById('question-form').action = "{{ route('admin.questions.store') }}";
        document.getElementById('q-method').value = 'POST';
        document.getElementById('q-id').value = '';
        document.getElementById('q-parent-id').value = '';
        mdeEditor.value('');
        document.getElementById('options-container').innerHTML = '';
        optionIndex = 0;
        document.getElementById('questionModalLabel').innerText = 'Thêm câu hỏi mới';
        
        // Reset visibility
        document.getElementById('options-section').classList.remove('d-none');
        document.getElementById('mark-section').classList.remove('d-none');
        document.getElementById('explanation-section').classList.remove('d-none');
    });

    // Enforce single choice rule when checking options
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('q-is-correct-check')) {
            const type = document.getElementById('question-type').value;
            if (type === 'single_choice' && e.target.checked) {
                document.querySelectorAll('.q-is-correct-check').forEach(cb => {
                    if (cb !== e.target) {
                        cb.checked = false;
                        cb.nextElementSibling.value = 0;
                    }
                });
            }
        }
    });

    // Enforce single choice rule and handle visibility when changing question type
    document.getElementById('question-type').addEventListener('change', function(e) {
        const type = e.target.value;
        const optionsSection = document.getElementById('options-section');
        const markSection = document.getElementById('mark-section');
        const explanationSection = document.getElementById('explanation-section');

        if (type === 'group') {
            optionsSection.classList.add('d-none');
            markSection.classList.add('d-none');
            explanationSection.classList.add('d-none');
            document.getElementById('q-default-mark').value = 0;

            // Vô hiệu hóa các input bên trong để bỏ qua lỗi HTML5 validation (required) khi form bị ẩn
            optionsSection.querySelectorAll('input, button').forEach(el => el.disabled = true);
        } else {
            optionsSection.classList.remove('d-none');
            markSection.classList.remove('d-none');
            explanationSection.classList.remove('d-none');
            if(document.getElementById('q-default-mark').value == 0) {
                document.getElementById('q-default-mark').value = 1;
            }

            // Mở khóa lại các input
            optionsSection.querySelectorAll('input, button').forEach(el => el.disabled = false);
        }

        if (type === 'single_choice') {
            let found = false;
            document.querySelectorAll('.q-is-correct-check').forEach(cb => {
                if (cb.checked) {
                    if (found) {
                        cb.checked = false;
                        cb.nextElementSibling.value = 0;
                    } else {
                        found = true;
                    }
                }
            });
        }
    });
</script>
