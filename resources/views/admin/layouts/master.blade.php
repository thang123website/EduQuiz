<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">

<head>
    <meta charset="utf-8" />
    <title>@yield('title') | EduQuiz Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="EduQuiz Admin Dashboard" name="description" />
    <meta content="EduQuiz" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/admin/images/favicon.ico') }}">

    @include('admin.layouts.head-css')
</head>

<body>

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

    @include('admin.layouts.vendor-scripts')
</body>

</html>
