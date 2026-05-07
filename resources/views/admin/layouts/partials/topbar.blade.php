<header id="page-topbar">
    <div class="layout-width">
        <div class="navbar-header">
            <div class="d-flex">
                <!-- LOGO -->
                <div class="navbar-brand-box horizontal-logo">
                    <a href="{{ route('admin.dashboard') }}" class="logo logo-dark">
                        <span class="logo-sm">
                            <img src="{{ asset('assets/admin/images/logo-sm.png') }}" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="{{ asset('assets/admin/images/logo-dark.png') }}" alt="" height="17">
                        </span>
                    </a>
                    <a href="{{ route('admin.dashboard') }}" class="logo logo-light">
                        <span class="logo-sm">
                            <img src="{{ asset('assets/admin/images/logo-sm.png') }}" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="{{ asset('assets/admin/images/logo-light.png') }}" alt="" height="17">
                        </span>
                    </a>
                </div>

                <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger shadow-none" id="topnav-hamburger-icon">
                    <span class="hamburger-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>

                <!-- App Search-->
                <form class="app-search d-none d-md-block">
                    <div class="position-relative">
                        <input type="text" class="form-control" placeholder="Search menu, users, etc..." autocomplete="off" id="search-options" value="">
                        <span class="mdi mdi-magnify search-widget-icon"></span>
                        <span class="mdi mdi-close-circle search-widget-icon search-widget-icon-close d-none" id="search-close-options"></span>
                    </div>
                    <div class="dropdown-menu dropdown-menu-lg" id="search-dropdown">
                        <div data-simplebar style="max-height: 320px;">
                            <!-- Menu Links Search Results -->
                            <div class="dropdown-header">
                                <h6 class="text-overflow text-muted mb-0 text-uppercase">Menu & Pages</h6>
                            </div>
                            <div id="menu-search-results">
                                <!-- JS will populate this -->
                            </div>

                            <!-- Data Search Results (Optional AJAX) -->
                            <div class="dropdown-header mt-2">
                                <h6 class="text-overflow text-muted mb-0 text-uppercase">Quick Actions</h6>
                            </div>
                            <div id="data-search-results">
                                <a href="{{ route('admin.users.create') }}" class="dropdown-item notify-item">
                                    <i class="ri-user-add-line align-middle fs-18 text-muted me-2"></i>
                                    <span>Thêm người dùng mới</span>
                                </a>
                                <a href="{{ route('admin.blog.create') }}" class="dropdown-item notify-item">
                                    <i class="ri-article-line align-middle fs-18 text-muted me-2"></i>
                                    <span>Viết bài mới</span>
                                </a>
                            </div>
                        </div>

                        <div class="text-center pt-3 pb-1" id="search-no-result" style="display: none;">
                            <a href="#" class="btn btn-primary btn-sm">View All Results <i class="ri-arrow-right-line ms-1"></i></a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="d-flex align-items-center">

                <div class="dropdown d-md-none topbar-head-dropdown header-item">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle shadow-none" id="page-header-search-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="bx bx-search fs-22"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-search-dropdown">
                        <form class="p-3">
                            <div class="form-group m-0">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Search ..." aria-label="Recipient's username">
                                    <button class="btn btn-primary" type="submit"><i class="mdi mdi-magnify"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="dropdown ms-1 topbar-head-dropdown header-item">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle shadow-none" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img id="header-lang-img" src="{{ asset('assets/admin/images/flags/us.svg') }}" alt="Header Language" height="20" class="rounded">
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a href="javascript:void(0);" class="dropdown-item notify-item language py-2" data-lang="en" title="English">
                            <img src="{{ asset('assets/admin/images/flags/us.svg') }}" alt="user-image" class="me-2 rounded" height="18">
                            <span class="align-middle">English</span>
                        </a>
                        <a href="javascript:void(0);" class="dropdown-item notify-item language py-2" data-lang="vi" title="Vietnamese">
                            <img src="{{ asset('assets/admin/images/flags/vn.svg') }}" alt="user-image" class="me-2 rounded" height="18">
                            <span class="align-middle">Tiếng Việt</span>
                        </a>
                    </div>
                </div>

                <div class="dropdown topbar-head-dropdown ms-1 header-item">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle shadow-none" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class='bx bx-category-alt fs-22'></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg p-0 dropdown-menu-end">
                        <div class="p-3 border-top-0 border-start-0 border-end-0 border-dashed border">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="m-0 fw-semibold fs-15"> Web Apps </h6>
                                </div>
                                <div class="col-auto">
                                    <a href="#!" class="btn btn-sm btn-soft-info shadow-none"> View All Apps
                                        <i class="ri-arrow-right-s-line align-middle"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dropdown topbar-head-dropdown ms-1 header-item">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle shadow-none" id="page-header-cart-dropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
                        <i class='bx bx-shopping-bag fs-22'></i>
                        <span class="position-absolute topbar-badge cartitem-badge fs-10 translate-middle badge rounded-pill bg-info">5</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-xl dropdown-menu-end p-0 dropdown-menu-cart" aria-labelledby="page-header-cart-dropdown">
                        <div class="p-3 border-top-0 border-start-0 border-end-0 border-dashed border">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="m-0 fs-16 fw-semibold"> My Cart</h6>
                                </div>
                                <div class="col-auto">
                                    <span class="badge bg-warning-subtle text-warning fs-13"><span class="cartitem-badge">5</span> items</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ms-1 header-item d-none d-sm-flex">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle shadow-none" data-toggle="fullscreen">
                        <i class='bx bx-fullscreen fs-22'></i>
                    </button>
                </div>

                <div class="ms-1 header-item d-none d-sm-flex">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle light-dark-mode shadow-none">
                        <i class='bx bx-moon fs-22'></i>
                    </button>
                </div>

                <div class="dropdown topbar-head-dropdown ms-1 header-item" id="notificationDropdown">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle shadow-none" id="page-header-notifications-dropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
                        <i class='bx bx-bell fs-22'></i>
                        @if(auth()->user()->unreadNotifications->count() > 0)
                        <span class="position-absolute topbar-badge fs-10 translate-middle badge rounded-pill bg-danger">{{ auth()->user()->unreadNotifications->count() }}<span class="visually-hidden">unread messages</span></span>
                        @endif
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-notifications-dropdown">

                        <div class="dropdown-head bg-primary bg-pattern rounded-top">
                            <div class="p-3">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="m-0 fs-16 fw-semibold text-white"> Notifications </h6>
                                    </div>
                                    <div class="col-auto dropdown-tabs">
                                        <span class="badge bg-light-subtle text-body fs-13"> {{ auth()->user()->unreadNotifications->count() }} New</span>
                                    </div>
                                </div>
                            </div>

                            <div class="px-2 pt-2">
                                <ul class="nav nav-tabs dropdown-tabs nav-tabs-custom" data-dropdown-tabs="true" id="notificationItemsTab" role="tablist">
                                    <li class="nav-item waves-effect waves-light">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#all-noti-tab" role="tab" aria-selected="true">
                                            All (<span id="all-notifications-count">{{ auth()->user()->notifications->count() }}</span>)
                                        </a>
                                    </li>
                                </ul>
                            </div>

                        </div>

                        <div class="tab-content position-relative" id="notificationItemsTabContent">
                            <div class="tab-pane fade show active py-2 ps-2" id="all-noti-tab" role="tabpanel">
                                <div data-simplebar style="max-height: 300px;" class="pe-2">
                                    @forelse(auth()->user()->notifications->take(10) as $notification)
                                    <div class="text-reset notification-item d-block dropdown-item position-relative {{ $notification->read_at ? '' : 'bg-light' }}">
                                        <div class="d-flex">
                                            <div class="avatar-xs me-3 flex-shrink-0">
                                                <span class="avatar-title bg-info-subtle text-info rounded-circle fs-16">
                                                    <i class="bx bx-badge-check"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <a href="{{ route('notifications.show', $notification->id) }}" class="stretched-link">
                                                    <h6 class="mt-0 mb-2 lh-base">{!! $notification->data['title'] ?? 'Thông báo hệ thống' !!}</h6>
                                                </a>
                                                <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                    <span><i class="mdi mdi-clock-outline"></i> {{ $notification->created_at->diffForHumans() }}</span>
                                                </p>
                                            </div>
                                            <div class="px-2 fs-15">
                                                <div class="form-check notification-check">
                                                    <input class="form-check-input" type="checkbox" value="{{ $notification->id }}" onchange="updateNotificationCount()">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="empty-notification-elem">
                                        <div class="w-25 w-sm-50 pt-3 mx-auto">
                                            <img src="{{ asset('assets/admin/images/svg/bell.svg') }}" class="img-fluid" alt="user-pic">
                                        </div>
                                        <div class="pb-5 mt-2 text-center">
                                            <p class="fs-18 fw-semibold lh-base">Hey! You have no any notifications </p>
                                        </div>
                                    </div>
                                    @endforelse

                                    <div class="my-3 text-center view-all">
                                        <a href="{{ route('notifications.userList') }}" class="btn btn-soft-success waves-effect waves-light">View All Notifications <i class="ri-arrow-right-line align-middle"></i></a>
                                    </div>
                                </div>

                                <div class="p-3 border-top text-center" id="notification-actions" style="display: none;">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="text-muted">Select <span id="select-count">0</span> Result</div>
                                        <button type="button" class="btn btn-link link-danger p-0 shadow-none" onclick="bulkDeleteNotifications()">Remove</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

<script>
function updateNotificationCount() {
    const checkboxes = document.querySelectorAll('.notification-check input:checked');
    const count = checkboxes.length;
    const actionsDiv = document.getElementById('notification-actions');
    const countSpan = document.getElementById('select-count');
    const viewAllDiv = document.querySelector('.view-all');

    if (count > 0) {
        actionsDiv.style.display = 'block';
        countSpan.innerText = count;
        if (viewAllDiv) viewAllDiv.classList.add('d-none');
    } else {
        actionsDiv.style.display = 'none';
        if (viewAllDiv) viewAllDiv.classList.remove('d-none');
    }
}

function bulkDeleteNotifications() {
    const checkboxes = document.querySelectorAll('.notification-check input:checked');
    const ids = Array.from(checkboxes).map(cb => cb.value);
    
    if (ids.length === 0) return;

    Swal.fire({
        title: 'Xác nhận xóa?',
        text: `Bạn có chắc chắn muốn xóa ${ids.length} thông báo đã chọn?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'btn btn-primary w-xs me-2 mt-2',
        cancelButtonClass: 'btn btn-danger w-xs mt-2',
        confirmButtonText: 'Đúng, xóa nó!',
        cancelButtonText: 'Hủy bỏ',
        buttonsStyling: false,
        showCloseButton: true
    }).then(async (result) => {
        if (result.isConfirmed) {
            fetch('{{ route('notifications.bulkDeletePersonal') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ ids: ids })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    ids.forEach(id => {
                        const checkbox = Array.from(document.querySelectorAll('.notification-check input')).find(cb => cb.value === id);
                        if (checkbox) {
                            const item = checkbox.closest('.notification-item');
                            item.remove();
                        }
                    });
                    updateNotificationCount();
                    
                    // Cập nhật badge
                    const badges = document.querySelectorAll('.topbar-badge');
                    badges.forEach(badge => {
                        if (data.unread_count > 0) {
                            badge.innerText = data.unread_count;
                            badge.style.display = 'block';
                        } else {
                            badge.style.display = 'none';
                        }
                    });

                    // Cập nhật tổng số lượng trên tab All (x)
                    const allCountSpan = document.getElementById('all-notifications-count');
                    if (allCountSpan) {
                        let currentTotal = parseInt(allCountSpan.innerText);
                        allCountSpan.innerText = Math.max(0, currentTotal - ids.length);
                    }

                    Swal.fire({
                        title: 'Đã xóa!',
                        text: 'Các thông báo đã được loại bỏ.',
                        icon: 'success',
                        confirmButtonClass: 'btn btn-primary w-xs mt-2',
                        buttonsStyling: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: data.message || 'Không thể xóa thông báo.',
                        confirmButtonClass: 'btn btn-primary w-xs mt-2',
                        buttonsStyling: false
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Lỗi', 'Lỗi kết nối máy chủ.', 'error');
            });
        }
    });
}
</script>

                <div class="dropdown ms-sm-3 header-item topbar-user">
                    <button type="button" class="btn shadow-none" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="d-flex align-items-center">
                            <img class="rounded-circle header-profile-user" src="{{ auth()->user()->avatar_url }}" alt="Header Avatar">
                            <span class="text-start ms-xl-2">
                                <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">{{ auth()->user()->name ?? 'Admin' }}</span>
                                <span class="d-none d-xl-block ms-1 fs-12 text-muted user-name-sub-text">{{ auth()->user()->role_name ?? 'Founder' }}</span>
                            </span>
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <h6 class="dropdown-header">Welcome {{ explode(' ', auth()->user()->name ?? 'Admin')[0] }}!</h6>
                        <a class="dropdown-item" href="{{ route('admin.users.edit', auth()->id()) }}"><i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Profile</span></a>
                        <a class="dropdown-item" href="#"><i class="mdi mdi-message-text-outline text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Messages</span></a>
                        <a class="dropdown-item" href="#"><i class="mdi mdi-calendar-check-outline text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Taskboard</span></a>
                        <a class="dropdown-item" href="#"><i class="mdi mdi-lifebuoy text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Help</span></a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#"><i class="mdi mdi-wallet text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Balance : <b>$5971.67</b></span></a>
                        <a class="dropdown-item" href="#"><span class="badge bg-success-subtle text-success mt-1 float-end">New</span><i class="mdi mdi-cog-outline text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Settings</span></a>
                        <a class="dropdown-item" href="#"><i class="mdi mdi-lock text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Lock screen</span></a>
                        <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span class="align-middle" data-key="t-logout">Logout</span></a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</header>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById("search-options");
    const searchDropdown = document.getElementById("search-dropdown");
    const menuResultsContainer = document.getElementById("menu-search-results");
    const searchClose = document.getElementById("search-close-options");
    
    // Indexing function
    function indexMenu() {
        let items = [];
        // Lấy tất cả các link trong sidebar (kể cả menu con)
        document.querySelectorAll("#navbar-nav .nav-link").forEach(link => {
            const text = link.innerText.replace(/\n/g, "").trim();
            const href = link.getAttribute("href");
            // Bỏ qua các link collapse (để mở menu con) và link trống
            if (href && href !== "#" && !href.startsWith("javascript") && link.getAttribute("data-bs-toggle") !== "collapse") {
                items.push({
                    text: text,
                    href: href,
                    icon: link.querySelector("i") ? link.querySelector("i").className : "ri-link"
                });
            }
        });
        return items;
    }

    let menuItems = indexMenu();

    // Re-index when menu items might change (optional)
    setTimeout(() => { menuItems = indexMenu(); }, 2000);

    searchInput.addEventListener("keyup", function() {
        const query = searchInput.value.toLowerCase().trim();
        
        if (query.length > 0) {
            searchDropdown.classList.add("show");
            searchClose.classList.remove("d-none");
            
            const filtered = menuItems.filter(item => item.text.toLowerCase().includes(query));
            renderResults(filtered);
        } else {
            searchDropdown.classList.remove("show");
            searchClose.classList.add("d-none");
        }
    });

    function renderResults(results) {
        menuResultsContainer.innerHTML = "";
        if (results.length === 0) {
            menuResultsContainer.innerHTML = '<div class="dropdown-item text-muted">Không tìm thấy kết quả...</div>';
            return;
        }

        results.slice(0, 10).forEach(item => {
            const div = document.createElement("a");
            div.href = item.href;
            div.className = "dropdown-item notify-item";
            div.innerHTML = `<i class="${item.icon} align-middle fs-18 text-muted me-2"></i><span>${item.text}</span>`;
            menuResultsContainer.appendChild(div);
        });
    }

    searchClose.addEventListener("click", function() {
        searchInput.value = "";
        searchDropdown.classList.remove("show");
        this.classList.add("d-none");
    });

    document.addEventListener("click", function(e) {
        if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
            searchDropdown.classList.remove("show");
        }
    });
});
</script>
