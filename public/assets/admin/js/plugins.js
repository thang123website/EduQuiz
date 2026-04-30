(
    document.querySelectorAll("[data-choices]") || document.querySelectorAll("[data-provider]")) && (
    document.writeln(`<script type='text/javascript' src='${PATH_ROOT}/assets/admin/libs/choices.js/public/assets/scripts/choices.min.js'><\/script>`),
    document.writeln(`<script type='text/javascript' src='${PATH_ROOT}/assets/admin/libs/flatpickr/flatpickr.min.js'><\/script>`)
);
