<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <a href="#" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ asset('assets/admin/images/logo-sm.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('assets/admin/images/logo-dark.png') }}" alt="" height="17">
            </span>
        </a>
        <a href="#" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ asset('assets/admin/images/logo-sm.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('assets/admin/images/logo-light.png') }}" alt="" height="17">
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
        </div>
        <!-- Sidebar -->
    </div>
    <div class="sidebar-background"></div>
</div>
<!-- Left Sidebar End -->
<!-- Vertical Overlay-->
<div class="vertical-overlay"></div>
