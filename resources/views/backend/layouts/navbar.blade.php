@php
    $user = auth()->user();
@endphp

<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme">

    <div class="layout-menu-toggle navbar-nav me-3 d-xl-none">
        <a class="nav-link px-0">
            <i class="bx bx-menu bx-sm"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center">
        <ul class="navbar-nav flex-row align-items-center ms-auto gap-1 gap-sm-2">

            <!-- Dark Mode -->
            <li class="nav-item">
                <button id="darkModeToggle" class="btn btn-icon btn-light">
                    <i class="bx bx-moon"></i>
                </button>
            </li>

            <!-- NOTIFICATIONS -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <i class="bx bx-bell fs-4"></i>
                    @php
                        $initialCount = $notificationCount ?? 0;
                    @endphp
                    <span id="notificationBadge"
                        class="badge bg-danger rounded-pill badge-notifications {{ $initialCount > 0 ? '' : 'd-none' }}">
                        {{ $initialCount > 99 ? '99+' : $initialCount }}
                    </span>
                </a>
                <ul id="notificationList" class="dropdown-menu dropdown-menu-end notification-menu"
                    data-poll-url="{{ route('notifications.recent') }}"
                    data-read-all-url="{{ route('notifications.read-all') }}"
                    data-dismiss-base-url="{{ url('/notifications') }}">
                    <li class="dropdown-header px-2 py-1 d-flex justify-content-between align-items-center gap-2">
                        <span>Request Notifications</span>
                        <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 notification-read-all">
                            Read all
                        </button>
                    </li>
                    @forelse (($notifications ?? collect()) as $notification)
                        @php
                            $requestRoute =
                                $user?->role === 'admin'
                                    ? route('admin.requests.show', $notification->service_request_id)
                                    : ($user?->role === 'service_staff'
                                        ? route('staff.requests.show', $notification->service_request_id)
                                        : route('requests.show', $notification->service_request_id));
                            $isUnread = !$user?->notifications_last_read_at || $notification->created_at->gt($user->notifications_last_read_at);
                        @endphp
                        <li>
                            <div class="notification-item">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <a href="{{ $requestRoute }}" class="notification-link">
                                        <strong class="small {{ $isUnread ? '' : 'text-muted' }}">
                                            {{ ucfirst(str_replace('_', ' ', $notification->new_status)) }}
                                        </strong>
                                    </a>
                                    <div class="d-flex align-items-center gap-2">
                                        @if ($isUnread)
                                            <span class="notification-unread-dot" title="Unread"></span>
                                        @endif
                                        <button type="button"
                                            class="btn btn-sm btn-icon btn-outline-secondary notification-delete"
                                            data-id="{{ $notification->id }}" title="Delete notification">
                                            <i class="bx bx-x"></i>
                                        </button>
                                    </div>
                                </div>
                                <a href="{{ $requestRoute }}" class="notification-link">
                                    <div class="small text-muted">
                                        {{ $notification->updatedBy->name ?? 'System' }} ·
                                        {{ $notification->serviceRequest->title ?? 'Request #' . $notification->service_request_id }}
                                    </div>
                                    <div class="small text-muted">
                                        {{ $notification->created_at->format('d M H:i') }}
                                    </div>
                                    @if ($notification->note)
                                        <div class="notification-note mt-1">{{ $notification->note }}</div>
                                    @endif
                                </a>
                            </div>
                        </li>
                    @empty
                        <li class="px-2 py-2">
                            <div class="notification-item mb-0 text-muted">
                                No notifications yet.
                            </div>
                        </li>
                    @endforelse
                </ul>
            </li>

            <!-- USER DROPDOWN -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown">

                    <!-- Avatar -->
                    <div class="avatar avatar-online me-2">
                        <img src="{{ asset('sneat_backend/assets/img/avatars/1.png') }}" class="rounded-circle"
                            width="40" height="40" />
                    </div>

                    <!-- NAME + ROLE -->
                    <span class="fw-semibold d-none d-sm-inline">
                        {{ $user->name ?? 'User' }} ({{ ucfirst($user->role ?? 'user') }})
                    </span>

                </a>

                <ul class="dropdown-menu dropdown-menu-end">

                    <li class="px-3 py-2">
                        <strong>{{ $user->name ?? 'User' }}</strong><br>
                        <small>{{ ucfirst($user->role ?? 'user') }}</small>
                    </li>

                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    <!-- Profile -->
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.show') }}">
                            <i class="bx bx-user me-2"></i> Profile
                        </a>
                    </li>

                    <!-- Logout -->
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="dropdown-item">
                                <i class="bx bx-power-off me-2"></i> Logout
                            </button>
                        </form>
                    </li>

                </ul>
            </li>

        </ul>
    </div>
</nav>

<!-- DARK MODE SCRIPT -->
<script>
    const toggle = document.getElementById('darkModeToggle');
    const body = document.body;

    if (localStorage.getItem('theme') === 'dark') {
        body.classList.add('dark-mode');
    }

    toggle.addEventListener('click', () => {
        body.classList.toggle('dark-mode');
        localStorage.setItem('theme', body.classList.contains('dark-mode') ? 'dark' : 'light');
    });

    const notificationList = document.getElementById('notificationList');
    const notificationBadge = document.getElementById('notificationBadge');

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function renderNotifications(data) {
        const count = data?.count ?? 0;
        const items = Array.isArray(data?.notifications) ? data.notifications : [];

        if (count > 0) {
            notificationBadge.classList.remove('d-none');
            notificationBadge.textContent = count > 99 ? '99+' : String(count);
        } else {
            notificationBadge.classList.add('d-none');
            notificationBadge.textContent = '0';
        }

        let html = `
            <li class="dropdown-header px-2 py-1 d-flex justify-content-between align-items-center gap-2">
                <span>Request Notifications</span>
                <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 notification-read-all">
                    Read all
                </button>
            </li>
        `;

        if (!items.length) {
            html += `
                <li class="px-2 py-2">
                    <div class="notification-item mb-0 text-muted">No notifications yet.</div>
                </li>
            `;
            notificationList.innerHTML = html;
            return;
        }

        for (const item of items) {
            html += `
                <li>
                    <div class="notification-item">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <a href="${escapeHtml(item.url)}" class="notification-link">
                                <strong class="small ${item.is_unread ? '' : 'text-muted'}">${escapeHtml(item.status)}</strong>
                            </a>
                            <div class="d-flex align-items-center gap-2">
                                ${item.is_unread ? '<span class="notification-unread-dot" title="Unread"></span>' : ''}
                                <button type="button" class="btn btn-sm btn-icon btn-outline-secondary notification-delete" data-id="${escapeHtml(item.id)}" title="Delete notification">
                                    <i class="bx bx-x"></i>
                                </button>
                            </div>
                        </div>
                        <a href="${escapeHtml(item.url)}" class="notification-link">
                            <div class="small text-muted">
                                ${escapeHtml(item.updated_by)} · ${escapeHtml(item.request_title)}
                            </div>
                            <div class="small text-muted">${escapeHtml(item.time)}</div>
                            ${item.note ? `<div class="notification-note mt-1">${escapeHtml(item.note)}</div>` : ''}
                        </a>
                    </div>
                </li>
            `;
        }

        notificationList.innerHTML = html;
    }

    async function refreshNotifications() {
        if (!notificationList) return;

        const pollUrl = notificationList.dataset.pollUrl;
        if (!pollUrl) return;

        try {
            const response = await fetch(pollUrl, {
                headers: {
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            });

            if (!response.ok) return;

            const payload = await response.json();
            renderNotifications(payload);
        } catch (error) {
            // silently skip failed refresh, keep current notifications visible
        }
    }

    async function markAllNotificationsAsRead() {
        if (!notificationList) return;
        const readAllUrl = notificationList.dataset.readAllUrl;
        if (!readAllUrl) return;

        try {
            await fetch(readAllUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                credentials: 'same-origin'
            });
            await refreshNotifications();
        } catch (error) {
            // no-op
        }
    }

    async function dismissNotification(notificationId) {
        if (!notificationList) return;
        const baseUrl = notificationList.dataset.dismissBaseUrl;
        if (!baseUrl || !notificationId) return;

        try {
            await fetch(`${baseUrl}/${notificationId}/dismiss`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                credentials: 'same-origin'
            });
            await refreshNotifications();
        } catch (error) {
            // no-op
        }
    }

    notificationList?.addEventListener('click', (event) => {
        const readAllButton = event.target.closest('.notification-read-all');
        if (readAllButton) {
            event.preventDefault();
            markAllNotificationsAsRead();
            return;
        }

        const deleteButton = event.target.closest('.notification-delete');
        if (deleteButton) {
            event.preventDefault();
            event.stopPropagation();
            dismissNotification(deleteButton.dataset.id);
        }
    });

    refreshNotifications();
    setInterval(refreshNotifications, 10000);
</script>
