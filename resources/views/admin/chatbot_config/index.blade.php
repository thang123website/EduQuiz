@extends('admin.layouts.master')

@section('title', 'Cấu hình Chatbot AI')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Cấu hình Chatbot AI</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Hệ thống</a></li>
                    <li class="breadcrumb-item active">Cấu hình Chatbot AI</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header align-items-center d-flex bg-primary-subtle">
                <h4 class="card-title mb-0 flex-grow-1 text-primary"><i class="ri-robot-2-line align-middle me-1"></i> Live Test Chatbot</h4>
            </div>
            <div class="card-body p-0">
                <div class="chat-conversation p-3" id="chat-conversation" data-simplebar style="height: 520px; overflow-y: auto;">
                    <ul class="list-unstyled chat-conversation-list" id="users-conversation">
                        <li class="chat-list left">
                            <div class="conversation-list">
                                <div class="chat-avatar">
                                    <div class="avatar-xs">
                                        <span class="avatar-title rounded-circle bg-primary"><i class="ri-robot-2-fill"></i></span>
                                    </div>
                                </div>
                                <div class="user-chat-content">
                                    <div class="ctext-wrap">
                                        <div class="ctext-wrap-content">
                                            <p class="mb-0 ctext-content">Xin chào! Hệ thống đã sẵn sàng. Bạn hãy nhập một câu tiếng Anh vào bên dưới để test thử nhé!</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="chat-input-section p-3 border-top border-top-dashed">
                    <form id="chatinput-form">
                        <div class="row g-0 align-items-center">
                            <div class="col-12 mb-2 d-none" id="attachment-preview-container">
                                <div class="position-relative d-inline-block">
                                    <img src="" id="attachment-preview" class="rounded border" style="max-height: 80px; max-width: 100px; object-fit: cover;">
                                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 start-100 translate-middle rounded-circle p-1" id="remove-attachment-btn" style="width: 24px; height: 24px; line-height: 1;">
                                        <i class="ri-close-line"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="chat-input-links">
                                    <input type="file" id="chat-attachment" accept="image/png, image/jpeg, image/jpg, image/webp" class="d-none">
                                    <button type="button" class="btn btn-soft-primary chat-send waves-effect waves-light" onclick="document.getElementById('chat-attachment').click()">
                                        <i class="ri-attachment-2 align-bottom"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col">
                                <input type="text" class="form-control chat-input bg-light border-light" id="chat-input" placeholder="Ví dụ: He goes to school (sai ở đâu?)" autocomplete="off">
                            </div>
                            <div class="col-auto">
                                <div class="chat-input-links ms-2">
                                    <button type="submit" class="btn btn-success chat-send waves-effect waves-light" id="send-btn">
                                        <i class="ri-send-plane-2-fill align-bottom"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header align-items-center d-flex bg-dark text-white">
                <h4 class="card-title mb-0 flex-grow-1 text-white"><i class="ri-code-box-line align-middle me-1"></i> Hướng Dẫn Tích Hợp API</h4>
            </div>
            <div class="card-body bg-light">
                <p class="text-muted fs-13 mb-3">Sử dụng thông số dưới đây để kết nối Chatbot với Frontend (NextJS) hoặc App.</p>
                
                <div class="mb-3">
                    <span class="badge bg-success fs-12">POST</span> 
                    <code class="fs-13 text-primary fw-bold">/api/v1/chatbot/message</code>
                </div>

                <h6 class="fs-13 fw-bold mb-2">1. Request Headers</h6>
                <pre class="bg-dark text-white p-2 rounded mb-0" style="font-size: 12px; font-family: monospace;">
{
  "Content-Type": "application/json",
  "X-API-KEY": "{{ \App\Models\Setting::get('api_key', 'api') ?? 'lấy_từ_cấu_hình_api' }}",
  "Authorization": "Bearer {token}"
}</pre>

                <h6 class="fs-13 fw-bold mb-2 mt-3">2. Request Body (JSON)</h6>
                <pre class="bg-dark text-white p-2 rounded mb-0" style="font-size: 12px; font-family: monospace;">
{
  "session_token": "chuỗi_unique_phiên_chat",
  "question_text": "He goes to school (sai ở đâu?)",
  "user_answer": "Có thể truyền rỗng",
  "correct_answer": "Có thể truyền rỗng"
}</pre>

                <h6 class="fs-13 fw-bold mb-2 mt-3">3. Response Mẫu (JSON)</h6>
                <pre class="bg-dark text-white p-2 rounded mb-0" style="font-size: 12px; font-family: monospace;">
{
  "status": "success",
  "data": {
    "is_correct": true,
    "explanation": "Giải thích chi tiết từ AI...",
    "suggested_tips": [
      "Mẹo số 1",
      "Mẹo số 2"
    ]
  }
}</pre>
                <div class="mt-3 alert alert-warning fs-12 mb-0 border-0 shadow-none">
                    <i class="ri-information-line me-1 align-middle fs-14"></i> 
                    <b>Lưu ý:</b> Tham số <code>session_token</code> dùng để lưu lại ngữ cảnh (context) hội thoại. Nếu user hỏi câu mới trong cùng một bài làm, hãy giữ nguyên token này để Bot nhớ mạch chat.
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-8">
        @if(session('success'))
            <div class="alert alert-success alert-border-left alert-dismissible fade show mb-4" role="alert">
                <i class="ri-check-double-line me-3 align-middle"></i> <strong>Thành công</strong> - {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.chatbot-config.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card card-animate bg-primary-subtle border-0 h-100 mb-0">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0"> Lượt Yêu Cầu (Hôm nay)</p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-end justify-content-between mt-4">
                                        <div>
                                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">{{ number_format($stats['today_requests']) }} <span class="fs-13 text-muted">/ {{ number_format($stats['total_requests']) }} Tổng</span></h4>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-primary rounded fs-3">
                                                <i class="ri-message-3-line"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card card-animate bg-success-subtle border-0 h-100 mb-0">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0"> Tokens Đã Dùng</p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-end justify-content-between mt-4">
                                        <div>
                                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">{{ number_format($stats['total_tokens']) }}</h4>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-success rounded fs-3">
                                                <i class="ri-coin-line"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card card-animate bg-warning-subtle border-0 h-100 mb-0">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0"> Trạng Thái</p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-end justify-content-between mt-4">
                                        <div>
                                            <h4 class="fs-22 fw-semibold ff-secondary mb-4 text-success">Hoạt Động</h4>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-warning rounded fs-3">
                                                <i class="ri-pulse-line"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border rounded p-3 bg-light mb-4">
                        <h6 class="fs-13 fw-bold mb-1">Cấu hình Nền tảng (Google Gemini)</h6>
                        <p class="text-muted fs-13 mb-3">Key kết nối và phiên bản Model đang sử dụng.</p>
                        
                        <div class="row">
                            <div class="col-md-5 mb-2">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label fw-semibold mb-0">API Key (Hỗ trợ tự động chuyển Key)</label>
                                    <button type="button" class="btn btn-sm btn-soft-primary py-0 px-2" id="add-key-btn"><i class="ri-add-line align-bottom"></i> Thêm</button>
                                </div>
                                <div id="api-keys-container">
                                    @foreach($apiKeys as $index => $key)
                                        <div class="input-group mb-2 key-item">
                                            <input type="text" class="form-control" name="gemini_api_key[]" value="{{ $key }}" placeholder="AIzaSy...">
                                            @if($index > 0)
                                            <button type="button" class="btn btn-danger remove-key-btn"><i class="ri-delete-bin-line"></i></button>
                                            @else
                                            <button type="button" class="btn btn-light" disabled><i class="ri-key-2-line"></i></button>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-1 text-muted fs-13">Thêm nhiều Key để tránh lỗi hết hạn mức miễn phí.</div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label fw-semibold" for="frontend_url">Domain Frontend (App/Web)</label>
                                <input type="url" class="form-control" id="frontend_url" name="frontend_url" value="{{ $frontendUrl }}" placeholder="https://eduquiz.vn">
                                <div class="mt-1 text-muted fs-13">Domain để Bot tạo link đề thi.</div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label fw-semibold" for="gemini_model">Model Version</label>
                                <select class="form-select" id="gemini_model" name="gemini_model">
                                    <optgroup label="Thế hệ 2.5 (Mới nhất)">
                                        <option value="gemini-2.5-flash" {{ $selectedModel == 'gemini-2.5-flash' ? 'selected' : '' }}>2.5 Flash</option>
                                        <option value="gemini-2.5-flash-lite" {{ $selectedModel == 'gemini-2.5-flash-lite' ? 'selected' : '' }}>2.5 Flash Lite</option>
                                        <option value="gemini-2.5-pro" {{ $selectedModel == 'gemini-2.5-pro' ? 'selected' : '' }}>2.5 Pro</option>
                                    </optgroup>
                                    <optgroup label="Thế hệ 2.0 (Ổn định)">
                                        <option value="gemini-2.0-flash" {{ $selectedModel == 'gemini-2.0-flash' ? 'selected' : '' }}>2.0 Flash</option>
                                        <option value="gemini-2.0-flash-lite" {{ $selectedModel == 'gemini-2.0-flash-lite' ? 'selected' : '' }}>2.0 Flash Lite</option>
                                    </optgroup>
                                    <optgroup label="Thế hệ 1.5 (Cũ)">
                                        <option value="gemini-1.5-flash" {{ $selectedModel == 'gemini-1.5-flash' ? 'selected' : '' }}>1.5 Flash</option>
                                        <option value="gemini-1.5-pro" {{ $selectedModel == 'gemini-1.5-pro' ? 'selected' : '' }}>1.5 Pro</option>
                                    </optgroup>
                                </select>
                                <div class="mt-1 text-muted fs-13">Phiên bản AI.</div>
                            </div>
                        </div>
                    </div>

                    <div class="border rounded p-3 mb-4">
                        <h6 class="fs-13 fw-bold mb-1">Cấu hình Hành vi & Đầu ra (In-context Learning)</h6>
                        <p class="text-muted fs-13 mb-3">Điều chỉnh System Prompt và Temperature để hướng dẫn AI trả lời chuẩn xác nhất.</p>

                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="system_instruction">System Instruction (Luật ép cho Bot)</label>
                            <textarea class="form-control" id="system_instruction" name="system_instruction" rows="6" placeholder="You are a strict TOEIC tutor...">{{ $botConfig->system_instruction }}</textarea>
                            <div class="mt-1 text-muted fs-13">Nhập hướng dẫn bắt buộc cho AI, ví dụ: "Bạn là giáo viên tiếng Anh chấm điểm TOEIC. Hãy tập trung giải thích ngữ pháp...".</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="temperature">Độ sáng tạo (Temperature)</label>
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <input type="range" class="form-range" id="temperature" name="temperature" min="0" max="2" step="0.1" value="{{ $botConfig->temperature }}" oninput="document.getElementById('temp_val').innerText = this.value">
                                </div>
                                <div class="col-4">
                                    <span class="badge bg-primary fs-14" id="temp_val">{{ $botConfig->temperature }}</span>
                                </div>
                            </div>
                            <div class="mt-1 text-muted fs-13">Khuyến cáo: Nên để mức thấp (0.1 - 0.3) để AI giải thích chính xác ngữ pháp, không bịa đặt (hallucination).</div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label fw-semibold" for="response_schema">JSON Output Schema (Chỉ đọc)</label>
                            <textarea class="form-control bg-light" id="response_schema" rows="4" readonly>Tự động ép kiểu theo cấu trúc: 
{
  "is_correct": boolean,
  "explanation": "string",
  "suggested_tips": ["string"]
}</textarea>
                            <div class="mt-1 text-muted fs-13">Cấu trúc đầu ra được cố định trong source code để đảm bảo Frontend không bị lỗi hiển thị.</div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line align-bottom me-1"></i> Lưu cấu hình Chatbot
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Sinh token mới mỗi lần tải trang để làm mới lịch sử chat
    let currentAdminSessionToken = 'admin_test_' + Date.now();

    let chatAttachmentInput = document.getElementById('chat-attachment');
    let attachmentPreviewContainer = document.getElementById('attachment-preview-container');
    let attachmentPreview = document.getElementById('attachment-preview');
    let removeAttachmentBtn = document.getElementById('remove-attachment-btn');
    let currentAttachmentFile = null;

    chatAttachmentInput.addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            let file = e.target.files[0];
            let reader = new FileReader();
            reader.onload = function(e) {
                attachmentPreview.src = e.target.result;
                attachmentPreviewContainer.classList.remove('d-none');
                currentAttachmentFile = file;
            }
            reader.readAsDataURL(file);
        }
    });

    removeAttachmentBtn.addEventListener('click', function() {
        chatAttachmentInput.value = '';
        currentAttachmentFile = null;
        attachmentPreviewContainer.classList.add('d-none');
        attachmentPreview.src = '';
    });

    document.getElementById('chatinput-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        let inputField = document.getElementById('chat-input');
        let message = inputField.value.trim();
        if (!message && !currentAttachmentFile) return;

        let chatBox = document.getElementById('users-conversation');
        let btn = document.getElementById('send-btn');
        let container = document.getElementById('chat-conversation');

        // Render attachment in user chat history visually
        let attachmentHtml = '';
        if (currentAttachmentFile) {
            attachmentHtml = `<div class="mb-2"><img src="${attachmentPreview.src}" class="rounded" style="max-width: 150px; max-height: 150px; object-fit: cover;"></div>`;
        }

        // Append User Message
        chatBox.innerHTML += `
            <li class="chat-list right">
                <div class="conversation-list">
                    <div class="user-chat-content">
                        <div class="ctext-wrap">
                            <div class="ctext-wrap-content bg-success-subtle border-success-subtle">
                                ${attachmentHtml}
                                <p class="mb-0 ctext-content">${message}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        `;
        inputField.value = '';
        // Clear file after adding to UI
        let fileToSend = currentAttachmentFile;
        removeAttachmentBtn.click(); // Reset upload state

        container.scrollTop = container.scrollHeight;

        // Show typing indicator
        let typingId = 'typing-' + Date.now();
        chatBox.innerHTML += `
            <li class="chat-list left" id="${typingId}">
                <div class="conversation-list">
                    <div class="chat-avatar">
                        <div class="avatar-xs"><span class="avatar-title rounded-circle bg-primary"><i class="ri-robot-2-fill"></i></span></div>
                    </div>
                    <div class="user-chat-content">
                        <div class="ctext-wrap">
                            <div class="ctext-wrap-content">
                                <p class="mb-0 ctext-content">
                                    <span class="typing-indicator"><span>.</span><span>.</span><span>.</span></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        `;
        container.scrollTop = container.scrollHeight;

        btn.disabled = true;

        let formData = new FormData();
        formData.append('message', message);
        formData.append('session_token', currentAdminSessionToken);
        if (fileToSend) {
            formData.append('attachment', fileToSend);
        }

        fetch('{{ route("admin.chatbot-config.test") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(res => res.json())
        .then(res => {
            document.getElementById(typingId).remove();
            btn.disabled = false;

            if (res.success) {
                let data = res.data;
                
                // Parse nội dung AI (Markdown cơ bản và tự động link)
                let explanationHtml = data.explanation.replace(/</g, "&lt;").replace(/>/g, "&gt;");
                explanationHtml = explanationHtml.replace(/\*\*(.*?)\*\*/g, '<b>$1</b>'); // In đậm
                let generalUrlRegex = /(https?:\/\/[^\s\)<>]+)/g;
                explanationHtml = explanationHtml.replace(generalUrlRegex, '<a href="$1" target="_blank" class="text-danger fw-bold text-decoration-underline"><i class="ri-external-link-line"></i> Bấm vào đây để mở</a>');
                explanationHtml = explanationHtml.replace(/\n/g, '<br>'); // Xuống dòng
                
                let htmlContent = `<div class="mb-2 text-dark">${explanationHtml}</div>`;
                if (data.suggested_tips && data.suggested_tips.length > 0) {
                    htmlContent += `<div class="mt-2 pt-2 border-top border-top-dashed"><strong class="fs-12 text-muted"><i class="ri-lightbulb-flash-line text-warning align-bottom"></i> Gợi ý học tập:</strong><ul class="mb-0 ps-3 fs-13 text-muted mt-1">`;
                    data.suggested_tips.forEach(tip => {
                        let urlRegex = /(https?:\/\/[^\s\)]+)/g;
                        if (urlRegex.test(tip)) {
                            // Nếu có chứa URL, tách riêng URL thành link màu đỏ để click mở tab mới
                            let tipHtml = tip.replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(urlRegex, '<a href="$1" target="_blank" class="text-danger fw-bold text-decoration-underline"><i class="ri-external-link-line"></i> Bấm vào đây để mở</a>');
                            htmlContent += `<li class="mb-1">${tipHtml}</li>`;
                        } else {
                            // Nếu là text thường, cho phép click để hỏi tiếp AI
                            htmlContent += `<li class="mb-1"><a href="javascript:void(0);" onclick="sendSuggestedMessage(this.innerText)" class="text-info text-decoration-underline">${tip}</a></li>`;
                        }
                    });
                    htmlContent += `</ul></div>`;
                }

                chatBox.innerHTML += `
                    <li class="chat-list left">
                        <div class="conversation-list">
                            <div class="chat-avatar">
                                <div class="avatar-xs"><span class="avatar-title rounded-circle bg-primary"><i class="ri-robot-2-fill"></i></span></div>
                            </div>
                            <div class="user-chat-content">
                                <div class="ctext-wrap">
                                    <div class="ctext-wrap-content">
                                        <div class="mb-0 ctext-content fs-13" style="font-family: inherit;">${htmlContent}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                `;
            } else {
                chatBox.innerHTML += `
                    <li class="chat-list left">
                        <div class="conversation-list">
                            <div class="chat-avatar">
                                <div class="avatar-xs"><span class="avatar-title rounded-circle bg-danger"><i class="ri-error-warning-fill"></i></span></div>
                            </div>
                            <div class="user-chat-content">
                                <div class="ctext-wrap">
                                    <div class="ctext-wrap-content bg-danger-subtle text-danger border-danger-subtle">
                                        <p class="mb-0 ctext-content"><b>Lỗi:</b> ${res.message}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                `;
            }
            container.scrollTop = container.scrollHeight;
        })
        .catch(err => {
            document.getElementById(typingId).remove();
            btn.disabled = false;
            console.error(err);
        });
    });

    // Hàm gọi khi User click vào một Gợi ý học tập
    window.sendSuggestedMessage = function(message) {
        let inputField = document.getElementById('chat-input');
        inputField.value = message;
        // Tự động kích hoạt sự kiện submit form
        document.getElementById('chatinput-form').dispatchEvent(new Event('submit', { cancelable: true }));
    };

    // Logic xử lý Thêm/Xóa ô nhập API Key
    const addKeyBtn = document.getElementById('add-key-btn');
    const apiKeysContainer = document.getElementById('api-keys-container');

    if (addKeyBtn && apiKeysContainer) {
        addKeyBtn.addEventListener('click', function() {
            const div = document.createElement('div');
            div.className = 'input-group mb-2 key-item';
            div.innerHTML = `
                <input type="text" class="form-control" name="gemini_api_key[]" value="" placeholder="AIzaSy...">
                <button type="button" class="btn btn-danger remove-key-btn"><i class="ri-delete-bin-line"></i></button>
            `;
            apiKeysContainer.appendChild(div);
        });

        apiKeysContainer.addEventListener('click', function(e) {
            if (e.target.closest('.remove-key-btn')) {
                e.target.closest('.key-item').remove();
            }
        });
    }
</script>
<style>
    .typing-indicator span { animation: blink 1.4s infinite both; height: 5px; width: 5px; display: inline-block; background-color: #3b3b3b; border-radius: 50%; margin: 0 2px; }
    .typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
    .typing-indicator span:nth-child(3) { animation-delay: 0.4s; }
    @keyframes blink { 0% { opacity: 0.2; } 20% { opacity: 1; } 100% { opacity: 0.2; } }
</style>
@endpush
@endsection
