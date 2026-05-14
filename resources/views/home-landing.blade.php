<!doctype html>
<html lang="vi" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">

    <head>

        <meta charset="utf-8" />
        <title>Trang chủ | EduQuiz - Nền tảng Đánh giá & Học tập thông minh</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Nền tảng thi trắc nghiệm trực tuyến" name="description" />
        <meta content="EduQuiz" name="author" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="{{ get_image_url(\App\Models\Setting::get('site_favicon')) ?: asset('ui_velzon_admin/assets/images/favicon.ico') }}">

        <!--Swiper slider css-->
        <link href="{{ asset('ui_velzon_admin/assets/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet" type="text/css" />

        <!-- Layout config Js -->
        <script src="{{ asset('ui_velzon_admin/assets/js/layout.js') }}"></script>
        <!-- Bootstrap Css -->
        <link href="{{ asset('ui_velzon_admin/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <!-- Icons Css -->
        <link href="{{ asset('ui_velzon_admin/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="{{ asset('ui_velzon_admin/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
        <!-- custom Css-->
        <link href="{{ asset('ui_velzon_admin/assets/css/custom.min.css') }}" rel="stylesheet" type="text/css" />

    </head>

    <body data-bs-spy="scroll" data-bs-target="#navbar-example">

        <!-- Begin page -->
        <div class="layout-wrapper landing">
            <nav class="navbar navbar-expand-lg navbar-landing fixed-top job-navbar" id="navbar">
                <div class="container-fluid custom-container">
                    <a class="navbar-brand" href="{{ url('/') }}">
                        <img src="{{ get_image_url(\App\Models\Setting::get('site_logo_light')) ?: asset('ui_velzon_admin/assets/images/logo-light.png') }}" class="card-logo card-logo-dark" alt="logo dark" height="28">
                        <img src="{{ get_image_url(\App\Models\Setting::get('site_logo_dark')) ?: asset('ui_velzon_admin/assets/images/logo-dark.png') }}" class="card-logo card-logo-light" alt="logo light" height="28">
                    </a>
                    <button class="navbar-toggler py-0 fs-20 text-body" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="mdi mdi-menu"></i>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav mx-auto mt-2 mt-lg-0" id="navbar-example">
                            <li class="nav-item">
                                <a class="nav-link active" href="#hero">Trang chủ</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#process">Quy trình</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#categories">Danh mục</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#findJob">Bài thi</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#candidates">Giáo viên</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#blog">Blog</a>
                            </li>
                        </ul>

                        <div class="">
                            @if (Route::has('login'))
                                @auth
                                    <a href="{{ route('admin.dashboard') }}" class="btn btn-soft-primary"><i class="ri-dashboard-line align-bottom me-1"></i> Trang quản trị</a>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-soft-primary"><i class="ri-user-3-line align-bottom me-1"></i> Đăng nhập</a>
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="btn btn-primary ms-1"><i class="ri-user-add-line align-bottom me-1"></i> Đăng ký</a>
                                    @endif
                                @endauth
                            @endif
                        </div>
                    </div>

                </div>
            </nav>
            <!-- end navbar -->

            <!-- start hero section -->
            <section class="section job-hero-section bg-light pb-0" id="hero">
                <div class="container">
                    <div class="row justify-content-between align-items-center">
                        <div class="col-lg-6">
                            <div>
                                <h1 class="display-6 fw-semibold text-capitalize mb-3 lh-base">Đánh giá năng lực - Mở khóa tương lai với <span class="text-primary">EduQuiz</span></h1>
                                <p class="lead text-muted lh-base mb-4">Hệ thống thi trắc nghiệm trực tuyến hàng đầu, cung cấp hàng ngàn bài tập từ các chuyên gia, giúp bạn củng cố kiến thức và chuẩn bị tốt nhất cho mọi kỳ thi.</p>
                                <form action="#" class="job-panel-filter">
                                    <div class="row g-md-0 g-2">
                                        <div class="col-md-5">
                                            <div>
                                                <input type="search" id="job-title" class="form-control filter-input-box" placeholder="Tên bài thi, chủ đề...">
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-md-4">
                                            <div>
                                                <select class="form-control" data-choices>
                                                    <option value="">Chọn danh mục</option>
                                                    <option value="Toán học">Toán học</option>
                                                    <option value="Ngoại ngữ">Ngoại ngữ</option>
                                                    <option value="Tin học">Tin học</option>
                                                    <option value="Khoa học">Khoa học</option>
                                                </select>
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-md-3">
                                            <div class="h-100">
                                                <button class="btn btn-primary submit-btn w-100 h-100" type="submit"><i class="ri-search-2-line align-bottom me-1"></i> Tìm bài thi</button>
                                            </div>
                                        </div>
                                        <!--end col-->
                                    </div>
                                    <!--end row-->
                                </form>

                                <ul class="treding-keywords list-inline mb-0 mt-3 fs-13">
                                    <li class="list-inline-item text-danger fw-semibold"><i class="mdi mdi-tag-multiple-outline align-middle"></i> Từ khóa nổi bật:</li>
                                    <li class="list-inline-item"><a href="javascript:void(0)">Toán THPT,</a></li>
                                    <li class="list-inline-item"><a href="javascript:void(0)">TOEIC,</a></li>
                                    <li class="list-inline-item"><a href="javascript:void(0)">Lập trình Web,</a></li>
                                    <li class="list-inline-item"><a href="javascript:void(0)">IELTS</a></li>
                                </ul>
                            </div>
                        </div>
                        <!--end col-->
                        <div class="col-lg-4">
                            <div class="position-relative home-img text-center mt-5 mt-lg-0">
                                <div class="card p-3 rounded shadow-lg inquiry-box">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm flex-shrink-0 me-3">
                                            <div class="avatar-title bg-warning-subtle text-warning rounded fs-18">
                                                <i class="ri-trophy-line"></i>
                                            </div>
                                        </div>
                                        <h5 class="fs-15 lh-base mb-0">Hơn 50,000 học viên đạt điểm cao</h5>
                                    </div>
                                </div>

                                <div class="card p-3 rounded shadow-lg application-box">
                                    <h5 class="fs-15 lh-base mb-3">Người dùng tiêu biểu</h5>
                                    <div class="avatar-group">
                                        <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Brent Gonzalez">
                                            <div class="avatar-xs">
                                                <img src="{{ asset('ui_velzon_admin/assets/images/users/avatar-3.jpg') }}" alt="" class="rounded-circle img-fluid">
                                            </div>
                                        </a>
                                        <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ellen Smith">
                                            <div class="avatar-xs">
                                                <div class="avatar-title rounded-circle bg-danger">
                                                    S
                                                </div>
                                            </div>
                                        </a>
                                        <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ellen Smith">
                                            <div class="avatar-xs">
                                                <img src="{{ asset('ui_velzon_admin/assets/images/users/avatar-10.jpg') }}" alt="" class="rounded-circle img-fluid">
                                            </div>
                                        </a>
                                        <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top">
                                            <div class="avatar-xs">
                                                <div class="avatar-title rounded-circle bg-success">
                                                    Z
                                                </div>
                                            </div>
                                        </a>
                                        <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Brent Gonzalez">
                                            <div class="avatar-xs">
                                                <img src="{{ asset('ui_velzon_admin/assets/images/users/avatar-9.jpg') }}" alt="" class="rounded-circle img-fluid">
                                            </div>
                                        </a>
                                        <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Hơn 10k+ học viên">
                                            <div class="avatar-xs">
                                                <div class="avatar-title fs-13 rounded-circle bg-light border-dashed border text-primary">
                                                    10k+
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <img src="{{ asset('ui_velzon_admin/assets/images/job-profile2.png') }}" alt="" class="user-img">

                                <div class="circle-effect">
                                    <div class="circle"></div>
                                    <div class="circle2"></div>
                                    <div class="circle3"></div>
                                    <div class="circle4"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end row -->
                </div>
                <!-- end container -->
            </section>
            <!-- end hero section -->

            <section class="section" id="process">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <div class="text-center mb-5">
                                <h1 class="mb-3 ff-secondary fw-semibold lh-base">Quy trình <span class="text-primary">tham gia</span> trên hệ thống</h1>
                                <p class="text-muted">Với EduQuiz, việc tham gia các kỳ thi trắc nghiệm trở nên dễ dàng và thuận tiện hơn bao giờ hết. Chỉ với vài bước đơn giản, bạn đã có thể bắt đầu đánh giá kiến thức của mình.</p>
                            </div>
                        </div>
                        <!-- end col -->
                    </div>
                    <!--end row-->
                    <div class="row">
                        <div class="col-lg-3 col-md-6">
                            <div class="card shadow-lg">
                                <div class="card-body p-4">
                                    <h1 class="fw-bold display-5 ff-secondary mb-4 text-success position-relative">
                                        <div class="job-icon-effect"></div>
                                        <span>1</span>
                                    </h1>
                                    <h6 class="fs-17 mb-2">Đăng ký tài khoản</h6>
                                    <p class="text-muted mb-0 fs-15">Tạo tài khoản học viên để lưu trữ lịch sử thi và tiến độ.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card shadow-none">
                                <div class="card-body p-4">
                                    <h1 class="fw-bold display-5 ff-secondary mb-4 text-success position-relative">
                                        <div class="job-icon-effect"></div>
                                        <span>2</span>
                                    </h1>
                                    <h6 class="fs-17 mb-2">Chọn chủ đề</h6>
                                    <p class="text-muted mb-0 fs-15">Lựa chọn lĩnh vực bạn muốn kiểm tra từ hàng ngàn bài thi.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card shadow-none">
                                <div class="card-body p-4">
                                    <h1 class="fw-bold display-5 ff-secondary mb-4 text-success position-relative">
                                        <div class="job-icon-effect"></div>
                                        <span>3</span>
                                    </h1>

                                    <h6 class="fs-17 mb-2">Làm bài trắc nghiệm</h6>
                                    <p class="text-muted mb-0 fs-15">Trải nghiệm bài thi với thời gian thực và giao diện thân thiện.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card shadow-none">
                                <div class="card-body p-4">
                                    <h1 class="fw-bold display-5 ff-secondary mb-4 text-success position-relative">
                                        <div class="job-icon-effect"></div>
                                        <span>4</span>
                                    </h1>
                                    <h6 class="fs-17 mb-2">Xem kết quả chi tiết</h6>
                                    <p class="text-muted mb-0 fs-15">Nhận đánh giá năng lực ngay sau khi nộp bài để cải thiện.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end container-->
            </section>

            <!-- start features -->
            <section class="section">
                <div class="container">
                    <div class="row align-items-center justify-content-lg-between justify-content-center gy-4">
                        <div class="col-lg-5 col-sm-7">
                            <div class="about-img-section mb-5 mb-lg-0 text-center">
                                <div class="card rounded shadow-lg inquiry-box d-none d-lg-block">
                                    <div class="card-body d-flex align-items-center">
                                        <div class="avatar-sm flex-shrink-0 me-3">
                                            <div class="avatar-title bg-secondary-subtle text-secondary rounded-circle fs-18">
                                                <i class="ri-book-open-line"></i>
                                            </div>
                                        </div>
                                        <h5 class="fs-15 lh-base mb-0">Truy cập hơn <span class="text-secondary fw-semibold">10,000+</span> Bộ câu hỏi</h5>
                                    </div>
                                </div>

                                <div class="card feedback-box">
                                    <div class="card-body d-flex shadow-lg">
                                        <div class="flex-shrink-0 me-3">
                                            <img src="{{ asset('ui_velzon_admin/assets/images/users/avatar-10.jpg') }}" alt="" class="avatar-sm rounded-circle">
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="fs-14 lh-base mb-0">Nguyễn Văn A</h5>
                                            <p class="text-muted fs-11 mb-1">Sinh viên CNTT</p>

                                            <div class="text-warning">
                                                <i class="ri-star-s-fill"></i>
                                                <i class="ri-star-s-fill"></i>
                                                <i class="ri-star-s-fill"></i>
                                                <i class="ri-star-s-fill"></i>
                                                <i class="ri-star-s-fill"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <img src="{{ asset('ui_velzon_admin/assets/images/about.jpg') }}" alt="" class="img-fluid mx-auto rounded-3" />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="text-muted">
                                <h1 class="mb-3 lh-base">Khám phá <span class="text-primary">Tiềm Năng</span> Của Bạn Tại Một Nơi</h1>
                                <p class="ff-secondary fs-16 mb-2">Bước đầu tiên để nâng cao kiến thức là đánh giá chính xác năng lực hiện tại của mình thông qua các bài test đa dạng.</p>
                                <p class="ff-secondary fs-16">EduQuiz mang đến cho bạn một môi trường luyện tập hiệu quả, chấm điểm tức thì và phân tích chi tiết kết quả từng phần.</p>

                                <div class="vstack gap-2 mb-4 pb-1">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-2">
                                            <div class="avatar-xs icon-effect">
                                                <div class="avatar-title bg-transparent text-success rounded-circle h2">
                                                    <i class="ri-check-fill"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <p class="mb-0">Ngân hàng câu hỏi cập nhật liên tục.</p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-2">
                                            <div class="avatar-xs icon-effect">
                                                <div class="avatar-title bg-transparent text-success rounded-circle h2">
                                                    <i class="ri-check-fill"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <p class="mb-0">Hệ thống chấm điểm tự động & báo cáo phân tích.</p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-2">
                                            <div class="avatar-xs icon-effect">
                                                <div class="avatar-title bg-transparent text-success rounded-circle h2">
                                                    <i class="ri-check-fill"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <p class="mb-0">Lưu lại lịch sử thi và quá trình rèn luyện.</p>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <a href="#!" class="btn btn-primary">Bắt Đầu Thi Ngay <i class="ri-arrow-right-line align-bottom ms-1"></i></a>
                                </div>
                            </div>
                        </div>
                        <!-- end col -->
                    </div>
                    <!-- end row -->
                </div>
                <!-- end container -->
            </section>
            <!-- end features -->

            <!-- start services -->
            <section class="section bg-light" id="categories">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-7">
                            <div class="text-center mb-5">
                                <h1 class="mb-3 ff-secondary fw-semibold text-capitalize lh-base">Khám phá các <span class="text-primary">Chủ Đề</span> nổi bật</h1>
                                <p class="text-muted">Chọn từ các lĩnh vực phổ biến nhất trên nền tảng của chúng tôi để bắt đầu ôn luyện.</p>
                            </div>
                        </div>
                        <!-- end col -->
                    </div>
                    <!-- end row -->

                    <div class="row justify-content-center">
                        <div class="col-lg-3 col-md-6">
                            <div class="card shadow-none text-center py-3">
                                <div class="card-body py-4">
                                    <div class="avatar-sm position-relative mb-4 mx-auto">
                                        <div class="job-icon-effect"></div>
                                        <div class="avatar-title bg-transparent text-success rounded-circle">
                                            <i class="ri-pencil-ruler-2-line fs-1"></i>
                                        </div>
                                    </div>
                                    <a href="#!" class="stretched-link">
                                        <h5 class="fs-17 pt-1">Công Nghệ Thông Tin</h5>
                                    </a>
                                    <p class="mb-0 text-muted">1543 Bài thi</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card shadow-none text-center py-3">
                                <div class="card-body py-4">
                                    <div class="avatar-sm position-relative mb-4 mx-auto">
                                        <div class="job-icon-effect"></div>
                                        <div class="avatar-title bg-transparent text-success rounded-circle">
                                            <i class="ri-global-line fs-1"></i>
                                        </div>
                                    </div>
                                    <a href="#!" class="stretched-link">
                                        <h5 class="fs-17 pt-1">Ngoại Ngữ</h5>
                                    </a>
                                    <p class="mb-0 text-muted">3241 Bài thi</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card shadow-none text-center py-3">
                                <div class="card-body py-4">
                                    <div class="avatar-sm mb-4 mx-auto position-relative">
                                        <div class="job-icon-effect"></div>
                                        <div class="avatar-title bg-transparent text-success rounded-circle">
                                            <i class="ri-calculator-line fs-1"></i>
                                        </div>
                                    </div>
                                    <a href="#!" class="stretched-link">
                                        <h5 class="fs-17 pt-1">Toán Học</h5>
                                    </a>
                                    <p class="mb-0 text-muted">876 Bài thi</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card shadow-none text-center py-3">
                                <div class="card-body py-4">
                                    <div class="avatar-sm position-relative mb-4 mx-auto">
                                        <div class="job-icon-effect"></div>
                                        <div class="avatar-title bg-transparent text-success rounded-circle">
                                            <i class="ri-microscope-line fs-1"></i>
                                        </div>
                                    </div>
                                    <a href="#!" class="stretched-link">
                                        <h5 class="fs-17 pt-1">Khoa Học Tự Nhiên</h5>
                                    </a>
                                    <p class="mb-0 text-muted">465 Bài thi</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card shadow-none text-center py-3">
                                <div class="card-body py-4">
                                    <div class="avatar-sm position-relative mb-4 mx-auto">
                                        <div class="job-icon-effect"></div>
                                        <div class="avatar-title bg-transparent text-success rounded-circle">
                                            <i class="ri-book-3-line fs-1"></i>
                                        </div>
                                    </div>
                                    <a href="#!" class="stretched-link">
                                        <h5 class="fs-17 pt-1">Lịch Sử & Địa Lý</h5>
                                    </a>
                                    <p class="mb-0 text-muted">105 Bài thi</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card shadow-none text-center py-3">
                                <div class="card-body py-4">
                                    <div class="avatar-sm position-relative mb-4 mx-auto">
                                        <div class="job-icon-effect"></div>
                                        <div class="avatar-title bg-transparent text-success rounded-circle">
                                            <i class="ri-line-chart-line fs-1"></i>
                                        </div>
                                    </div>
                                    <a href="#!" class="stretched-link">
                                        <h5 class="fs-17 pt-1">Kinh Tế Học</h5>
                                    </a>
                                    <p class="mb-0 text-muted">377 Bài thi</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card shadow-none text-center py-3">
                                <div class="card-body py-4">
                                    <div class="avatar-sm position-relative mb-4 mx-auto">
                                        <div class="job-icon-effect"></div>
                                        <div class="avatar-title bg-transparent text-success rounded-circle">
                                            <i class="ri-briefcase-2-line fs-1"></i>
                                        </div>
                                    </div>
                                    <a href="#!" class="stretched-link">
                                        <h5 class="fs-17 pt-1">Kỹ Năng Mềm</h5>
                                    </a>
                                    <p class="mb-0 text-muted">85 Bài thi</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end row -->
                </div>
                <!-- end container -->
            </section>
            <!-- end services -->

            <!-- start cta -->
            <section class="py-5 bg-primary position-relative">
                <div class="bg-overlay bg-overlay-pattern opacity-50"></div>
                <div class="container">
                    <div class="row align-items-center gy-4">
                        <div class="col-sm">
                            <div>
                                <h4 class="text-white mb-2">Bạn đã sẵn sàng để bắt đầu?</h4>
                                <p class="text-white-50 mb-0">Đăng ký tài khoản mới và rèn luyện kiến thức ngay hôm nay.</p>
                            </div>
                        </div>
                        <!-- end col -->
                        <div class="col-sm-auto">
                            <div>
                                <a href="{{ Route::has('register') ? route('register') : 'javascript:void(0);' }}" class="btn bg-gradient btn-danger">Tạo Tài Khoản Miễn Phí</a>
                            </div>
                        </div>
                        <!-- end col -->
                    </div>
                    <!-- end row -->
                </div>
                <!-- end container -->
            </section>
            <!-- end cta -->

            <section class="section" id="findJob">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-7">
                            <div class="text-center mb-5">
                                <h1 class="mb-3 ff-secondary fw-semibold text-capitalize lh-base">Tìm Các Bài Thi <span class="text-primary">Mới Nhất</span></h1>
                                <p class="text-muted">Dưới đây là một số bài thi nổi bật được hệ thống cập nhật và lựa chọn giúp bạn đánh giá sát thực nhất năng lực hiện tại.</p>
                            </div>
                        </div>
                        <!-- end col -->
                    </div>
                    <!-- end row -->

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card shadow-lg">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div class="avatar-sm">
                                            <div class="avatar-title bg-warning-subtle rounded">
                                                <img src="{{ asset('ui_velzon_admin/assets/images/companies/img-3.png') }}" alt="" class="avatar-xxs">
                                            </div>
                                        </div>
                                        <div class="ms-3 flex-grow-1">
                                            <a href="#!">
                                                <h5>Đề thi thử TOEIC 2026 (Format Mới)</h5>
                                            </a>
                                            <ul class="list-inline text-muted mb-3">
                                                <li class="list-inline-item">
                                                    <i class="ri-building-line align-bottom me-1"></i> Ngoại Ngữ
                                                </li>
                                                <li class="list-inline-item">
                                                    <i class="ri-file-text-line align-bottom me-1"></i> 200 câu hỏi
                                                </li>
                                                <li class="list-inline-item">
                                                    <i class="ri-time-line align-bottom me-1"></i> 120 phút
                                                </li>
                                            </ul>
                                            <div class="hstack gap-2">
                                                <span class="badge bg-success-subtle text-success">Tiếng Anh</span>
                                                <span class="badge bg-danger-subtle text-danger">Khó</span>
                                                <span class="badge bg-primary-subtle text-primary">Chứng chỉ</span>
                                            </div>
                                        </div>
                                        <div>
                                            <button type="button" class="btn btn-ghost-primary btn-icon custom-toggle" data-bs-toggle="button">
                                                <span class="icon-on"><i class="ri-bookmark-line"></i></span>
                                                <span class="icon-off"><i class="ri-bookmark-fill"></i></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="card shadow-lg">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div class="avatar-sm">
                                            <div class="avatar-title bg-primary-subtle rounded">
                                                <img src="{{ asset('ui_velzon_admin/assets/images/companies/img-2.png') }}" alt="" class="avatar-xxs">
                                            </div>
                                        </div>
                                        <div class="ms-3 flex-grow-1">
                                            <a href="#!">
                                                <h5>Đề cương Ôn thi môn Lịch sử THPT Quốc Gia</h5>
                                            </a>
                                            <ul class="list-inline text-muted mb-3">
                                                <li class="list-inline-item">
                                                    <i class="ri-building-line align-bottom me-1"></i> Khoa học Xã hội
                                                </li>
                                                <li class="list-inline-item">
                                                    <i class="ri-file-text-line align-bottom me-1"></i> 40 câu hỏi
                                                </li>
                                                <li class="list-inline-item">
                                                    <i class="ri-time-line align-bottom me-1"></i> 50 phút
                                                </li>
                                            </ul>
                                            <div class="hstack gap-2">
                                                <span class="badge bg-primary-subtle text-primary">Lịch sử</span>
                                                <span class="badge bg-secondary-subtle text-secondary">THPT</span>
                                            </div>
                                        </div>
                                        <div>
                                            <button type="button" class="btn btn-ghost-primary btn-icon custom-toggle active" data-bs-toggle="button">
                                                <span class="icon-on"><i class="ri-bookmark-line"></i></span>
                                                <span class="icon-off"><i class="ri-bookmark-fill"></i></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="card shadow-lg">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div class="avatar-sm">
                                            <div class="avatar-title bg-danger-subtle rounded">
                                                <img src="{{ asset('ui_velzon_admin/assets/images/companies/img-4.png') }}" alt="" class="avatar-xxs">
                                            </div>
                                        </div>
                                        <div class="ms-3 flex-grow-1">
                                            <a href="#!">
                                                <h5>Bài test Đánh giá Năng lực Đại học QG Hà Nội</h5>
                                            </a>
                                            <ul class="list-inline text-muted mb-3">
                                                <li class="list-inline-item">
                                                    <i class="ri-building-line align-bottom me-1"></i> Tổng hợp
                                                </li>
                                                <li class="list-inline-item">
                                                    <i class="ri-file-text-line align-bottom me-1"></i> 150 câu hỏi
                                                </li>
                                                <li class="list-inline-item">
                                                    <i class="ri-time-line align-bottom me-1"></i> 195 phút
                                                </li>
                                            </ul>
                                            <div class="hstack gap-2">
                                                <span class="badge bg-warning-subtle text-warning">Toán học</span>
                                                <span class="badge bg-info-subtle text-info">Ngữ văn</span>
                                                <span class="badge bg-danger-subtle text-danger">ĐGNL</span>
                                            </div>
                                        </div>
                                        <div>
                                            <button type="button" class="btn btn-ghost-primary btn-icon custom-toggle active" data-bs-toggle="button">
                                                <span class="icon-on"><i class="ri-bookmark-line"></i></span>
                                                <span class="icon-off"><i class="ri-bookmark-fill"></i></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="card shadow-lg">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div class="avatar-sm">
                                            <div class="avatar-title bg-success-subtle rounded">
                                                <img src="{{ asset('ui_velzon_admin/assets/images/companies/img-9.png') }}" alt="" class="avatar-xxs">
                                            </div>
                                        </div>
                                        <div class="ms-3 flex-grow-1">
                                            <a href="#!">
                                                <h5>Trắc nghiệm Ngôn ngữ lập trình PHP Cơ bản</h5>
                                            </a>
                                            <ul class="list-inline text-muted mb-3">
                                                <li class="list-inline-item">
                                                    <i class="ri-building-line align-bottom me-1"></i> Công Nghệ TT
                                                </li>
                                                <li class="list-inline-item">
                                                    <i class="ri-file-text-line align-bottom me-1"></i> 50 câu hỏi
                                                </li>
                                                <li class="list-inline-item">
                                                    <i class="ri-time-line align-bottom me-1"></i> 45 phút
                                                </li>
                                            </ul>
                                            <div class="hstack gap-2">
                                                <span class="badge bg-success-subtle text-success">PHP</span>
                                                <span class="badge bg-danger-subtle text-danger">Web</span>
                                            </div>
                                        </div>
                                        <div>
                                            <button type="button" class="btn btn-ghost-primary btn-icon custom-toggle" data-bs-toggle="button">
                                                <span class="icon-on"><i class="ri-bookmark-line"></i></span>
                                                <span class="icon-off"><i class="ri-bookmark-fill"></i></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-12">
                            <div class="text-center mt-4">
                                <a href="#!" class="btn btn-ghost-primary">Xem Thêm Bài Thi <i class="ri-arrow-right-line align-bottom"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- start candidates -->
            <section class="section bg-light" id="candidates">
                <div class="bg-overlay bg-overlay-pattern"></div>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <div class="text-center mb-5">
                                <h1 class="mb-3 ff-secondary fw-semibold text-capitalize lh-base">Giáo viên <span class="text-primary">Tiêu Biểu</span></h1>
                                <p class="text-muted mb-4">Các bộ đề thi được biên soạn và kiểm duyệt bởi các thầy cô và chuyên gia đầu ngành giúp đảm bảo chất lượng giảng dạy tốt nhất.</p>
                            </div>
                        </div>
                        <!-- end col -->
                    </div>
                    <!-- end row -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="swiper candidate-swiper">
                                <div class="swiper-wrapper">
                                    <div class="swiper-slide">
                                        <div class="card text-center">
                                            <div class="card-body p-4">
                                                <img src="{{ asset('ui_velzon_admin/assets/images/users/avatar-2.jpg') }}" alt="" class="rounded-circle avatar-md mx-auto d-block">
                                                <h5 class="fs-17 mt-3 mb-2">Thầy Nguyễn Văn Trọng</h5>
                                                <p class="text-muted fs-13 mb-3">Giáo viên chuyên Toán</p>

                                                <p class="text-muted mb-4 fs-14">
                                                    <i class="ri-building-line text-primary me-1 align-bottom"></i> Đại học Sư Phạm
                                                </p>

                                                <a href="#!" class="btn btn-primary w-100">Xem Hồ Sơ</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="card text-center">
                                            <div class="card-body p-4">
                                                <img src="{{ asset('ui_velzon_admin/assets/images/users/avatar-3.jpg') }}" alt="" class="rounded-circle avatar-md mx-auto d-block">
                                                <h5 class="fs-17 mt-3 mb-2">Cô Lê Hương Lài</h5>
                                                <p class="text-muted fs-13 mb-3">Giáo viên Ngoại Ngữ</p>
                                        
                                                <p class="text-muted mb-4 fs-14">
                                                    <i class="ri-building-line text-primary me-1 align-bottom"></i> Đại học Ngoại Ngữ
                                                </p>
                                        
                                                <a href="#!" class="btn btn-primary w-100">Xem Hồ Sơ</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="card text-center">
                                            <div class="card-body p-4">
                                                <img src="{{ asset('ui_velzon_admin/assets/images/users/avatar-10.jpg') }}" alt="" class="rounded-circle avatar-md mx-auto d-block">
                                                <h5 class="fs-17 mt-3 mb-2">Thầy Trần Minh Hoàng</h5>
                                                <p class="text-muted fs-13 mb-3">Chuyên gia Lập trình</p>
                                        
                                                <p class="text-muted mb-4 fs-14">
                                                    <i class="ri-building-line text-primary me-1 align-bottom"></i> Đại học Bách Khoa
                                                </p>
                                        
                                                <a href="#!" class="btn btn-primary w-100">Xem Hồ Sơ</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="card text-center">
                                            <div class="card-body p-4">
                                                <img src="{{ asset('ui_velzon_admin/assets/images/users/avatar-8.jpg') }}" alt="" class="rounded-circle avatar-md mx-auto d-block" />
                                                <h5 class="fs-17 mt-3 mb-2">Cô Phạm Thị Bích</h5>
                                                <p class="text-muted fs-13 mb-3">Giáo viên Ngữ Văn</p>
                                        
                                                <p class="text-muted mb-4 fs-14">
                                                    <i class="ri-building-line text-primary me-1 align-bottom"></i> Trường THPT Chuyên
                                                </p>
                                        
                                                <a href="#!" class="btn btn-primary w-100">Xem Hồ Sơ</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end container -->
            </section>
            <!-- end candidates -->

            <!-- start blog -->
            <section class="section" id="blog">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <div class="text-center mb-5">
                                <h1 class="mb-3 ff-secondary fw-semibold text-capitalize lh-base">Tin Tức <span class="text-primary">Blog</span></h1>
                                <p class="text-muted mb-4">Cập nhật những thông tin mới nhất về kỳ thi, mẹo học tập và hướng dẫn sử dụng hệ thống một cách hiệu quả.</p>
                            </div>
                        </div>
                    </div>
                    <!-- end row -->

                    <div class="row">
                        <div class="col-lg-4 col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <img src="{{ asset('ui_velzon_admin/assets/images/small/img-8.jpg') }}" alt="" class="img-fluid rounded" />
                                </div>
                                <div class="card-body">
                                    <ul class="list-inline fs-14 text-muted">
                                        <li class="list-inline-item">
                                            <i class="ri-calendar-line align-bottom me-1"></i> 30 Tháng 10, 2026
                                        </li>
                                        <li class="list-inline-item">
                                            <i class="ri-message-2-line align-bottom me-1"></i> 364 Bình luận
                                        </li>
                                    </ul>
                                    <a href="javascript:void(0);">
                                        <h5>Mẹo ôn thi đại học đạt điểm tối đa môn Toán</h5>
                                    </a>
                                    <p class="text-muted fs-14">Học tập một cách khoa học giúp bạn tránh sai sót ở những câu hỏi trắc nghiệm dễ và biết cách phân bổ thời gian hiệu quả.</p>
                                    
                                    <div>
                                        <a href="#!" class="link-success">Xem thêm <i class="ri-arrow-right-line align-bottom ms-1"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <img src="{{ asset('ui_velzon_admin/assets/images/small/img-6.jpg') }}" alt="" class="img-fluid rounded" />
                                </div>
                                <div class="card-body">
                                    <ul class="list-inline fs-14 text-muted">
                                        <li class="list-inline-item">
                                            <i class="ri-calendar-line align-bottom me-1"></i> 02 Tháng 10, 2026
                                        </li>
                                        <li class="list-inline-item">
                                            <i class="ri-message-2-line align-bottom me-1"></i> 245 Bình luận
                                        </li>
                                    </ul>
                                    <a href="javascript:void(0);">
                                        <h5>Giới thiệu tính năng Phân tích kết quả bài thi</h5>
                                    </a>
                                    <p class="text-muted fs-14">Hệ thống phân tích mới của EduQuiz sẽ chỉ ra những điểm yếu của bạn và gợi ý nội dung ôn tập sát thực nhất.</p>
                        
                                    <div>
                                        <a href="#!" class="link-success">Xem thêm <i class="ri-arrow-right-line align-bottom ms-1"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <img src="{{ asset('ui_velzon_admin/assets/images/small/img-9.jpg') }}" alt="" class="img-fluid rounded" />
                                </div>
                                <div class="card-body">
                                    <ul class="list-inline fs-14 text-muted">
                                        <li class="list-inline-item">
                                            <i class="ri-calendar-line align-bottom me-1"></i> 23 Tháng 9, 2026
                                        </li>
                                        <li class="list-inline-item">
                                            <i class="ri-message-2-line align-bottom me-1"></i> 354 Bình luận
                                        </li>
                                    </ul>
                                    <a href="javascript:void(0);">
                                        <h5>Cách quản lý thời gian khi làm trắc nghiệm</h5>
                                    </a>
                                    <p class="text-muted fs-14">Để vượt qua những kỳ thi gắt gao, thời gian chính là mấu chốt. Tìm hiểu cách tối ưu hoá từng phút khi làm bài trắc nghiệm.</p>
                        
                                    <div>
                                        <a href="#!" class="link-success">Xem thêm <i class="ri-arrow-right-line align-bottom ms-1"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- end container -->
            </section>
            <!-- end blog -->

            <!-- start cta -->
            <section class="py-5 bg-primary position-relative">
                <div class="bg-overlay bg-overlay-pattern opacity-50"></div>
                <div class="container">
                    <div class="row align-items-center gy-4">
                        <div class="col-sm">
                            <div>
                                <h4 class="text-white fw-semibold">Nhận Thông Báo Mới Nhất!</h4>
                                <p class="text-white text-opacity-75 mb-0">Đăng ký nhận email về các cuộc thi và kỳ thi thử miễn phí.</p>
                            </div>
                        </div>
                        <!-- end col -->
                        <div class="col-sm-auto">
                            <button class="btn btn-danger" type="button">Đăng ký Ngay <i class="ri-arrow-right-line align-bottom"></i></button>
                        </div>
                        <!-- end col -->
                    </div>
                    <!-- end row -->
                </div>
                <!-- end container -->
            </section>
            <!-- end cta -->

            <!-- Start footer -->
            <footer class="custom-footer bg-dark py-5 position-relative">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-4 mt-4">
                            <div>
                                <div>
                                    <img src="{{ get_image_url(\App\Models\Setting::get('site_logo_light')) ?: asset('ui_velzon_admin/assets/images/logo-light.png') }}" alt="logo light" height="28">
                                </div>
                                <div class="mt-4 fs-13">
                                    <p>Nền tảng thi trắc nghiệm trực tuyến ưu việt.</p>
                                    <p>Hệ thống tự động chấm điểm, thống kê chi tiết, giúp học viên ôn thi hiệu quả và giáo viên quản lý ngân hàng câu hỏi một cách dễ dàng.</p>
                                    <ul class="list-inline mb-0 footer-social-link">
                                        <li class="list-inline-item">
                                            <a href="javascript: void(0);" class="avatar-xs d-block">
                                                <div class="avatar-title rounded-circle">
                                                    <i class="ri-facebook-fill"></i>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="list-inline-item">
                                            <a href="javascript: void(0);" class="avatar-xs d-block">
                                                <div class="avatar-title rounded-circle">
                                                    <i class="ri-github-fill"></i>
                                                </div>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-7 ms-lg-auto">
                            <div class="row">
                                <div class="col-sm-4 mt-4">
                                    <h5 class="text-white mb-0">Về chúng tôi</h5>
                                    <div class="text-muted mt-3">
                                        <ul class="list-unstyled ff-secondary footer-list">
                                            <li><a href="#hero">Giới thiệu</a></li>
                                            <li><a href="#candidates">Đội ngũ giáo viên</a></li>
                                            <li><a href="#blog">Blog Tin Tức</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-sm-4 mt-4">
                                    <h5 class="text-white mb-0">Khám Phá</h5>
                                    <div class="text-muted mt-3">
                                        <ul class="list-unstyled ff-secondary footer-list">
                                            <li><a href="#categories">Danh Mục Thi</a></li>
                                            <li><a href="#findJob">Bài Thi Nổi Bật</a></li>
                                            <li><a href="#process">Quy trình</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-sm-4 mt-4">
                                    <h5 class="text-white mb-0">Hỗ trợ</h5>
                                    <div class="text-muted mt-3">
                                        <ul class="list-unstyled ff-secondary footer-list">
                                            <li><a href="javascript:void(0);">Câu hỏi thường gặp</a></li>
                                            <li><a href="javascript:void(0);">Liên hệ</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row text-center text-sm-start align-items-center mt-5">
                        <div class="col-sm-6">
                            <div>
                                <p class="copy-rights mb-0">
                                    <script> document.write(new Date().getFullYear()) </script> © EduQuiz. All Rights Reserved.
                                </p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end mt-3 mt-sm-0">
                                <ul class="list-inline mb-0 footer-list gap-4 fs-13">
                                    <li class="list-inline-item">
                                        <a href="javascript:void(0);">Chính sách bảo mật</a>
                                    </li>
                                    <li class="list-inline-item">
                                        <a href="javascript:void(0);">Điều khoản sử dụng</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
            <!-- end footer -->

            <!--start back-to-top-->
            <button onclick="topFunction()" class="btn btn-info btn-icon landing-back-top" id="back-to-top">
                <i class="ri-arrow-up-line"></i>
            </button>
            <!--end back-to-top-->

        </div>
        <!-- end layout wrapper -->


        <!-- JAVASCRIPT -->
        <script src="{{ asset('ui_velzon_admin/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('ui_velzon_admin/assets/libs/simplebar/simplebar.min.js') }}"></script>
        <script src="{{ asset('ui_velzon_admin/assets/libs/node-waves/waves.min.js') }}"></script>
        <script src="{{ asset('ui_velzon_admin/assets/libs/feather-icons/feather.min.js') }}"></script>
        <script src="{{ asset('ui_velzon_admin/assets/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
        <script src="{{ asset('ui_velzon_admin/assets/js/plugins.js') }}"></script>

        <!--Swiper slider js-->
        <script src="{{ asset('ui_velzon_admin/assets/libs/swiper/swiper-bundle.min.js') }}"></script>

        <!--job landing init -->
        <script src="{{ asset('ui_velzon_admin/assets/js/pages/job-lading.init.js') }}"></script>
    </body>

</html>
