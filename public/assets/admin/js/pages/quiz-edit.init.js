document.addEventListener('DOMContentLoaded', function() {
    // 1. Khôi phục Tab sau khi reload
    const storageKey = 'activeTab_' + window.location.pathname;
    const activeTab = localStorage.getItem(storageKey);
    
    if (activeTab) {
        const tabEl = document.querySelector(`a[href="${activeTab}"]`);
        if (tabEl) {
            const tab = new bootstrap.Tab(tabEl);
            tab.show();
        }
    }

    // 2. Lưu Tab khi người dùng chuyển tab
    const tabLinks = document.querySelectorAll('a[data-bs-toggle="tab"]');
    tabLinks.forEach(function(tabLink) {
        tabLink.addEventListener('shown.bs.tab', function(e) {
            localStorage.setItem(storageKey, e.target.getAttribute('href'));
        });
    });

    // 3. Khởi tạo Markdown Editor cho Modal (nếu có id question-content-editor)
    var editorEl = document.getElementById("question-content-editor");
    if (editorEl && typeof SimpleMDE !== 'undefined') {
        window.mdeEditor = new SimpleMDE({ element: editorEl });
    }

    // 4. Khởi tạo Sortable cho danh sách câu hỏi (Kéo thả)
    var listEl = document.getElementById('questions-list');
    if (listEl && typeof Sortable !== 'undefined') {
        Sortable.create(listEl, {
            handle: '.card-header',
            animation: 150,
            onEnd: function() {
                // Logic to save order via AJAX can be implemented here
            }
        });
    }

    // 5. Khởi tạo Sortable cho danh sách Parts
    var partsListEl = document.getElementById('parts-list');
    if (partsListEl && typeof Sortable !== 'undefined') {
        Sortable.create(partsListEl, {
            handle: '.part-drag-handle',
            animation: 150,
            onEnd: function() {
                const orderedIds = Array.from(partsListEl.querySelectorAll('.part-item')).map(item => item.getAttribute('data-id'));
                const rootPath = typeof PATH_ROOT !== 'undefined' ? PATH_ROOT : '';
                
                fetch(rootPath + '/admin/quiz-parts/reorder', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ ordered_ids: orderedIds })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success && typeof Toastify !== 'undefined') {
                        Toastify({
                            text: "Đã cập nhật thứ tự Part",
                            duration: 2000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#0ab39c",
                        }).showToast();
                    }
                });
            }
        });
    }
});

// Các hàm Global được gọi từ sự kiện onclick trong HTML
window.openQuestionModal = function(questionId = null, partId = null, parentId = null) {
    if (partId) {
        document.getElementById('q-part-id').value = partId;
    }
    if (parentId) {
        document.getElementById('q-parent-id').value = parentId;
    }
    if (!questionId) {
        // Thêm sẵn 2 đáp án mặc định cho câu hỏi mới
        const container = document.getElementById('options-container');
        if (container && container.children.length === 0) {
            if (typeof addOption === 'function') {
                addOption('', false);
                addOption('', false);
            }
        }
        
        // Reset type to single_choice and trigger change
        const typeSelect = document.getElementById('question-type');
        if(typeSelect && !parentId) {
            typeSelect.value = 'single_choice';
            typeSelect.dispatchEvent(new Event('change'));
        }
    }
    const modalEl = document.getElementById('questionModal');
    if (modalEl) {
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
};

window.openEditPartModal = function(partId, title, description) {
    const form = document.getElementById('editPartForm');
    if (form) {
        const rootPath = typeof PATH_ROOT !== 'undefined' ? PATH_ROOT : '';
        form.action = rootPath + '/admin/quiz-parts/' + partId;
    }
    
    const titleInput = document.getElementById('editPartTitle');
    if (titleInput) titleInput.value = title;
    
    const descInput = document.getElementById('editPartDescription');
    if (descInput) descInput.value = description;
    
    const modalEl = document.getElementById('editPartModal');
    if (modalEl) {
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
};

let currentImportPartId = null;

window.openBulkImport = function(partId) {
    currentImportPartId = partId;
    const modalEl = document.getElementById('importQuestionsModal');
    if (modalEl) {
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
};

// Xử lý Form Submit cho việc Import bằng AJAX
const importForm = document.getElementById('importQuestionsForm');
if (importForm) {
    importForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!currentImportPartId) {
            alert('Lỗi: Không tìm thấy Part ID!');
            return;
        }

        const fileInput = document.getElementById('importExcelFile');
        if (!fileInput.files.length) {
            alert('Vui lòng chọn file Excel.');
            return;
        }

        const btnSubmit = document.getElementById('btnImportSubmit');
        const originalText = btnSubmit.innerHTML;
        btnSubmit.innerHTML = '<i class="mdi mdi-spin mdi-loading align-middle me-1"></i> Đang xử lý...';
        btnSubmit.disabled = true;

        // Giao diện thanh trình độ (Progress Bar Animation)
        const progressContainer = document.getElementById('importProgressContainer');
        const progressBar = document.getElementById('importProgressBar');
        const progressText = document.getElementById('importPercentage');
        progressContainer.classList.remove('d-none');
        progressBar.style.width = '0%';
        progressText.innerText = '0%';

        let progress = 0;
        const progressInterval = setInterval(() => {
            if (progress < 90) {
                progress += Math.floor(Math.random() * 5) + 1;
                progressBar.style.width = progress + '%';
                progressText.innerText = progress + '%';
            }
        }, 800);

        const formData = new FormData();
        formData.append('file', fileInput.files[0]);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        // Cần đảm bảo có biến PATH_ROOT, nếu không có thì fallback về root /
        const rootPath = typeof PATH_ROOT !== 'undefined' ? PATH_ROOT : '';

        fetch(rootPath + '/admin/quizzes/parts/' + currentImportPartId + '/import-questions', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            clearInterval(progressInterval);
            
            if (data.success) {
                progressBar.style.width = '100%';
                progressText.innerText = '100%';
                document.getElementById('importStatusText').innerText = 'Hoàn tất!';
                
                if (typeof Toastify !== 'undefined') {
                    Toastify({
                        text: data.message,
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#0ab39c",
                    }).showToast();
                }
                
                setTimeout(() => {
                    const modalInst = bootstrap.Modal.getInstance(document.getElementById('importQuestionsModal'));
                    if (modalInst) modalInst.hide();
                    location.reload();
                }, 1000);
            } else {
                progressContainer.classList.add('d-none');
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi Import',
                        text: data.message
                    });
                } else {
                    alert(data.message);
                }
            }
        })
        .catch(error => {
            clearInterval(progressInterval);
            progressContainer.classList.add('d-none');
            console.error('Error:', error);
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi hệ thống',
                    text: 'Có lỗi xảy ra khi gọi API Import.'
                });
            } else {
                alert('Có lỗi xảy ra khi gọi API Import.');
            }
        })
        .finally(() => {
            btnSubmit.innerHTML = originalText;
            btnSubmit.disabled = false;
        });
    });
}

// Xử lý Toggle Icon cho Nút Thu gọn / Mở rộng Part
document.addEventListener('show.bs.collapse', function (e) {
    if (e.target.id && e.target.id.startsWith('collapsePart')) {
        const btnIcon = document.querySelector('[data-bs-target="#' + e.target.id + '"] i');
        if (btnIcon) {
            btnIcon.classList.remove('ri-arrow-down-s-line');
            btnIcon.classList.add('ri-arrow-up-s-line');
        }
    }
});

document.addEventListener('hide.bs.collapse', function (e) {
    if (e.target.id && e.target.id.startsWith('collapsePart')) {
        const btnIcon = document.querySelector('[data-bs-target="#' + e.target.id + '"] i');
        if (btnIcon) {
            btnIcon.classList.remove('ri-arrow-up-s-line');
            btnIcon.classList.add('ri-arrow-down-s-line');
        }
    }
});
