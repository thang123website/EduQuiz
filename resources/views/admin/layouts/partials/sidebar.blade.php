<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <a href="{{ route('admin.dashboard') }}" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ get_image_url(\App\Models\Setting::get('site_favicon')) ?: asset('assets/admin/images/logo-sm.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ get_image_url(\App\Models\Setting::get('site_logo_dark')) ?: asset('assets/admin/images/logo-dark.png') }}" alt="" height="22">
            </span>
        </a>
        <a href="{{ route('admin.dashboard') }}" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ get_image_url(\App\Models\Setting::get('site_favicon')) ?: asset('assets/admin/images/logo-sm.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ get_image_url(\App\Models\Setting::get('site_logo_light')) ?: asset('assets/admin/images/logo-light.png') }}" alt="" height="22">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">
            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title"><span data-key="t-menu">Menu</span></li>
                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="ri-dashboard-2-line"></i> <span data-key="t-dashboards">Dashboards</span>
                    </a>
                </li> <!-- end Dashboard Menu -->

                @can('media.view')
                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->routeIs('admin.media.*') ? 'active' : '' }}" href="{{ route('admin.media.index') }}">
                        <i class="ri-image-2-line"></i> <span>Thư viện Media</span>
                    </a>
                </li>
                @endcan

                <li class="menu-title"><i class="ri-more-fill"></i> <span data-key="t-system">Quản lý người dùng</span></li>
                
                @can('users.view')
                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                        <i class="ri-user-line"></i> <span data-key="t-users">Người dùng</span>
                    </a>
                </li>
                @endcan

                @can('roles.view')
                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}" href="{{ route('admin.roles.index') }}">
                        <i class="ri-shield-keyhole-line"></i> <span data-key="t-roles">Phân quyền</span>
                    </a>
                </li>
                @endcan

                @canany(['blog.view', 'blog_category.view'])
                <li class="menu-title"><i class="ri-more-fill"></i> <span>Quản lý Blog</span></li>

                @can('blog.view')
                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->routeIs('admin.blog.*') ? 'active' : '' }}" href="{{ route('admin.blog.index') }}">
                        <i class="ri-article-line"></i> <span>Bài viết</span>
                    </a>
                </li>
                @endcan

                @can('blog_category.view')
                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->routeIs('admin.blog-categories.*') ? 'active' : '' }}" href="{{ route('admin.blog-categories.index') }}">
                        <i class="ri-folder-2-line"></i> <span>Danh mục</span>
                    </a>
                </li>
                @endcan
                @endcanany

                @can('slider.view')
                <li class="menu-title"><i class="ri-more-fill"></i> <span>Nội dung Website</span></li>
                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->routeIs('admin.sliders.*') ? 'active' : '' }}" href="{{ route('admin.sliders.index') }}">
                        <i class="ri-slideshow-line"></i> <span>Slider / Banner</span>
                    </a>
                </li>
                @endcan

                <li class="menu-title"><i class="ri-more-fill"></i> <span>Thông báo</span></li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarNotifications" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->routeIs('admin.notifications.*') ? 'true' : 'false' }}" aria-controls="sidebarNotifications">
                        <i class="ri-notification-3-line"></i> <span>Thông báo</span>
                    </a>
                    <div class="collapse menu-dropdown {{ request()->routeIs('admin.notifications.*') || request()->routeIs('notifications.*') ? 'show' : '' }}" id="sidebarNotifications">
                        <ul class="nav nav-sm flex-column">
                            @if(auth()->user()->role_name == 'Quản trị viên' || auth()->user()->role_name == 'admin')
                            <li class="nav-item">
                                <a href="{{ route('admin.notifications.index') }}" class="nav-link {{ request()->routeIs('admin.notifications.index') ? 'active' : '' }}"> Lịch sử gửi </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.notifications.create') }}" class="nav-link {{ request()->routeIs('admin.notifications.create') ? 'active' : '' }}"> Gửi thông báo </a>
                            </li>
                            @endif
                            <li class="nav-item">
                                <a href="{{ route('notifications.userList') }}" class="nav-link {{ request()->routeIs('notifications.userList') ? 'active' : '' }}"> Thông báo của tôi </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="menu-title"><i class="ri-more-fill"></i> <span data-key="t-system">Cài đặt chung</span></li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarSettings" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->routeIs('admin.settings.*') ? 'true' : 'false' }}" aria-controls="sidebarSettings">
                        <i class="ri-settings-3-line"></i> <span>Hệ thống</span>
                    </a>
                    <div class="collapse menu-dropdown {{ request()->routeIs('admin.settings.*') ? 'show' : '' }}" id="sidebarSettings">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('admin.settings.general') }}" class="nav-link {{ request()->routeIs('admin.settings.general') ? 'active' : '' }}"> Cấu hình Website </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.index') ? 'active' : '' }}"> Cấu hình Media </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.settings.mail') }}" class="nav-link {{ request()->routeIs('admin.settings.mail') ? 'active' : '' }}"> Cấu hình Email </a>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
    <div class="sidebar-background"></div>
</div>
<!-- Left Sidebar End -->


