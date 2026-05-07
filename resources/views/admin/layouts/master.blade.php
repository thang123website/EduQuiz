<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">

<head>
    <meta charset="utf-8" />
    <title>@yield('title') | {{ \App\Models\Setting::get('site_name', 'EduQuiz') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="EduQuiz Admin Dashboard" name="description" />
    <script>var PATH_ROOT = "{{ url('/') }}";</script>
    <meta content="EduQuiz" name="author" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/admin/images/favicon.ico') }}">

    @include('admin.layouts.head-css')
</head>

<body>

    <!-- Preloader -->
    <div id="preloader">
        <div id="status">
            <div class="spinner-border text-primary avatar-sm" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>

    <!-- Begin page -->
    <div id="layout-wrapper">

        @include('admin.layouts.partials.topbar')
        @include('admin.layouts.partials.sidebar')

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">
                    @yield('content')
                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->

            @include('admin.layouts.partials.footer')
        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->

    @include('admin.layouts.partials.modals')

    <!-- Back to Top Button -->
    <button onclick="topFunction()" class="btn btn-danger btn-icon" id="back-to-top">
        <i class="ri-arrow-up-line"></i>
    </button>

    @include('admin.layouts.partials.customizer')

    <!-- Vertical Overlay-->
    <div class="vertical-overlay"></div>

    <script>
        function topFunction() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
        window.onscroll = function() {
            const btn = document.getElementById("back-to-top");
            if (btn) {
                if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
                    btn.style.display = "block";
                } else {
                    btn.style.display = "none";
                }
            }
        };
    </script>

    @include('admin.layouts.vendor-scripts')
</body>

</html>
