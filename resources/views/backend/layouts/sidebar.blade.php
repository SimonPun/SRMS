    @php
        $role = auth()->user()?->role;
        $isAdmin = $role === 'admin';
        $isStaff = $role === 'service_staff';
        $dashboardRoute = match ($role) {
            'admin' => route('admin.dashboard'),
            'service_staff' => route('staff.dashboard'),
            default => route('user.dashboard'),
        };
    @endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ $dashboardRoute }}" class="app-brand-link d-flex align-items-center gap-2 px-3 py-3 overflow-hidden">
            <span class="app-brand-logo demo d-inline-flex align-items-center justify-content-center rounded-3 bg-primary text-white fw-bold"
                style="width: 46px; height: 46px; font-size: 0.9rem; letter-spacing: 0.06em; flex-shrink: 0;">
                SRMS
            </span>
            <span class="app-brand-text demo menu-text lh-sm">
                <span class="d-block fw-bold text-uppercase text-truncate" style="font-size: 0.78rem; letter-spacing: 0.08em;">
                    SRMS
                </span>
                <span class="d-block text-muted text-truncate" style="font-size: 0.68rem;">
                    Service Desk
                </span>
            </span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>
    <ul class="menu-inner py-1">
        <!-- Dashboards -->
        <li class="menu-item {{ request()->routeIs('admin.dashboard') || request()->routeIs('user.dashboard') || request()->routeIs('staff.dashboard') ? 'active open' : '' }}">
            <a href="{{ $dashboardRoute }}" class="menu-link text-wrap">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Dashboards">Dashboard</div>
            </a>
        </li>

        @if ($isAdmin)
            <li class="menu-item {{ request()->is('admin/manage-users') ? 'active open' : '' }}">
                <a href="#" class="menu-link menu-toggle">
                    <i class="menu-icon bx bx-user"></i>
                    <div data-i18n="Layouts">User Management</div>
                </a>

                <ul class="menu-sub {{ request()->is('admin/manage-users*') ? 'show' : '' }}">
                    <li class="menu-item {{ request()->routeIs('admin.manage-users') ? 'active' : '' }}">
                        <a href="{{ route('admin.manage-users') }}" class="menu-link">
                            User List
                        </a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('admin.users.create') ? 'active' : '' }}">
                        <a href="{{ route('admin.users.create') }}" class="menu-link">
                            Add Staff
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu-item {{ request()->routeIs('admin.requests') ? 'active open' : '' }}">
                <a href="{{ route('admin.requests') }}" class="menu-link">
                    <i class="menu-icon bx bx-list-ul"></i>
                    <div data-i18n="Requests">All Requests</div>
                </a>
            </li>

            <li class="menu-item {{ request()->routeIs('admin.categories.*') ? 'active open' : '' }}">
                <a href="{{ route('admin.categories.index') }}" class="menu-link">
                    <i class="menu-icon bx bx-category"></i>
                    <div data-i18n="Categories">Categories</div>
                </a>
            </li>
        @elseif ($isStaff)
            <li class="menu-item {{ request()->routeIs('staff.requests') ? 'active open' : '' }}">
                <a href="{{ route('staff.requests') }}" class="menu-link">
                    <i class="menu-icon bx bx-list-ul"></i>
                    <div data-i18n="Requests">Assigned Requests</div>
                </a>
            </li>
        @else
            <li class="menu-item {{ request()->routeIs('requests.index') ? 'active open' : '' }}">
                <a href="{{ route('requests.index') }}" class="menu-link">
                    <i class="menu-icon bx bx-list-ul"></i>
                    <div data-i18n="Requests">My Requests</div>
                </a>
            </li>

            <li class="menu-item {{ request()->routeIs('requests.create') ? 'active open' : '' }}">
                <a href="{{ route('requests.create') }}" class="menu-link">
                    <i class="menu-icon bx bx-plus-circle"></i>
                    <div data-i18n="Create Request">Create a Request</div>
                </a>
            </li>
        @endif


    </ul>
</aside>
