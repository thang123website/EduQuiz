@stack('plugin-scripts')

<!-- JAVASCRIPT -->
<script src="{{ asset('assets/admin/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/admin/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/admin/libs/node-waves/waves.min.js') }}"></script>
<script src="{{ asset('assets/admin/libs/feather-icons/feather.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
<script src="{{ asset('assets/admin/js/plugins.js') }}?v=1.0.3"></script>
<!-- SweetAlert2 JS -->
<script src="{{ asset('assets/admin/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<!-- Toastify JS -->
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

@stack('scripts')

<script>
    // Global delete confirmation handler
    document.addEventListener('click', function (e) {
        if (e.target.closest('.confirm-delete')) {
            e.preventDefault();
            const btn = e.target.closest('.confirm-delete');
            const form = btn.closest('form');
            if (!form) return;

            const modalEl = document.getElementById('deleteRecordModal');
            if (modalEl) {
                // Sử dụng getOrCreateInstance để tránh tạo nhiều backdrop
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();

                const confirmBtn = document.getElementById('confirm-delete-btn');
                if (confirmBtn) {
                    confirmBtn.onclick = function () {
                        form.submit();
                    };
                }
            } else {
                if (confirm('Bạn có chắc chắn muốn xóa?')) {
                    form.submit();
                }
            }
        }
    });
</script>

<!-- App js -->
<script src="{{ asset('assets/admin/js/app.js') }}?v=1.0.3"></script>
