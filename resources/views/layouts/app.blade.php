<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ITP System') - Mini LNG</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #0a0f1e;
            --primary-light: #111827;
            --accent: #3b82f6;
            --accent-hover: #2563eb;
            --accent-glow: rgba(59, 130, 246, 0.1);
            --bg: #f0f4f8;
            --card: #ffffff;
            --border: #e5e7eb;
            --text: #111827;
            --text-muted: #6b7280;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --sidebar-w: 260px;
            --radius: 1rem;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: var(--bg);
            color: var(--text);
            margin: 0;
            -webkit-font-smoothing: antialiased;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            position: fixed;
            left: 0; top: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: linear-gradient(180deg, #0a0f1e 0%, #0f172a 40%, #131c33 100%);
            color: #fff;
            z-index: 1000;
            transition: width 0.35s cubic-bezier(0.4,0,0.2,1), transform 0.35s cubic-bezier(0.4,0,0.2,1);
            display: flex;
            flex-direction: column;
            border-right: 1px solid rgba(255,255,255,0.04);
            overflow: hidden;
        }

        /* Collapsed state (desktop icon-only) */
        .sidebar.collapsed {
            width: 64px;
        }
        .sidebar-brand h4,
        .sidebar-brand small,
        .sidebar-nav a span,
        .sidebar-nav .nav-label,
        .sidebar-footer .btn-logout span {
            transition: opacity 0.25s ease, max-width 0.25s ease;
            opacity: 1;
            max-width: 200px;
            overflow: hidden;
            white-space: nowrap;
        }
        .sidebar.collapsed .sidebar-brand h4,
        .sidebar.collapsed .sidebar-brand small,
        .sidebar.collapsed .sidebar-nav a span,
        .sidebar.collapsed .sidebar-nav .nav-label,
        .sidebar.collapsed .sidebar-footer .btn-logout span {
            opacity: 0;
            max-width: 0;
            pointer-events: none;
        }
        .sidebar.collapsed .sidebar-brand {
            padding: 1.25rem 0.5rem;
        }
        .sidebar.collapsed .sidebar-nav a {
            justify-content: center;
            padding: 0.7rem 0;
            position: relative;
        }
        .sidebar.collapsed .sidebar-nav a i {
            margin-right: 0;
            font-size: 1.1rem;
        }
        /* Tooltip on hover for collapsed sidebar */
        .sidebar.collapsed .sidebar-nav a[title]:hover::after {
            content: attr(title);
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%);
            margin-left: 8px;
            background: #1e293b;
            color: #fff;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            white-space: nowrap;
            z-index: 2000;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            pointer-events: none;
        }
        .sidebar.collapsed .sidebar-footer .btn-logout {
            padding: 0.6rem;
            justify-content: center;
        }
        .sidebar.collapsed + .sidebar-overlay + .main-content,
        .sidebar.collapsed + .main-content {
            margin-left: 64px;
        }

        /* Mobile sidebar overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15,23,42,0.5);
            backdrop-filter: blur(2px);
            z-index: 999;
            transition: opacity 0.3s ease;
        }
        .sidebar-overlay.active {
            display: block;
        }

        .sidebar-brand {
            padding: 1.75rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            text-align: center;
            position: relative;
        }

        .sidebar-brand::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 20%;
            width: 60%;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(59,130,246,0.4), transparent);
        }

        .sidebar-brand h4 {
            margin: 0;
            font-weight: 900;
            letter-spacing: 2px;
            background: linear-gradient(135deg, #38bdf8, #818cf8, #c084fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 1.25rem;
        }

        .sidebar-brand small {
            display: block;
            color: #475569;
            font-size: 0.6rem;
            letter-spacing: 3.5px;
            text-transform: uppercase;
            margin-top: 6px;
        }

        .sidebar-nav {
            flex: 1;
            padding: 1.25rem 0;
            overflow-y: auto;
        }

        .sidebar-nav .nav-label {
            padding: 0.75rem 1.5rem 0.25rem;
            font-size: 0.55rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2.5px;
            color: #374151;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            padding: 0.7rem 1.5rem;
            color: #64748b;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.85rem;
            border-left: 3px solid transparent;
            transition: all 0.25s;
            margin: 2px 0;
        }

        .sidebar-nav a i {
            width: 20px;
            margin-right: 12px;
            font-size: 0.95rem;
            text-align: center;
        }

        .sidebar-nav a:hover, .sidebar-nav a.active {
            color: #fff;
            background: linear-gradient(90deg, rgba(59,130,246,0.1), transparent);
            border-left-color: #3b82f6;
        }

        .sidebar-nav a.active {
            font-weight: 600;
        }

        .sidebar-footer {
            padding: 1.25rem 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.06);
        }

        .sidebar-footer .btn-logout {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 0.6rem;
            border-radius: 0.75rem;
            background: rgba(239,68,68,0.08);
            border: 1px solid rgba(239,68,68,0.15);
            color: #f87171;
            font-weight: 600;
            font-size: 0.8rem;
            text-decoration: none;
            transition: all 0.25s;
        }

        .sidebar-footer .btn-logout:hover {
            background: rgba(239,68,68,0.15);
            border-color: rgba(239,68,68,0.3);
            color: #ef4444;
        }

        /* ===== MAIN ===== */
        .main-content {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            transition: margin 0.35s;
        }

        .topbar {
            background: rgba(255,255,255,0.8);
            backdrop-filter: blur(12px);
            padding: 0.875rem 1.5rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .topbar-title {
            font-weight: 700;
            font-size: 0.95rem;
            color: var(--text);
        }

        .topbar .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 38px; height: 38px;
            border-radius: 0.75rem;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.85rem;
            box-shadow: 0 4px 12px rgba(59,130,246,0.25);
        }

        .role-badge {
            font-size: 0.55rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 3px 8px;
            border-radius: 6px;
        }

        .role-admin { background: #fef3c7; color: #92400e; }
        .role-yard { background: #dbeafe; color: #1e40af; }
        .role-class { background: #dcfce7; color: #166534; }
        .role-os { background: #f3e8ff; color: #7c3aed; }
        .role-stat { background: #ffe4e6; color: #be123c; }

        .page-content {
            padding: 1.5rem;
        }

        /* ===== CARDS ===== */
        .stat-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.5rem;
            transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 28px -6px rgba(0,0,0,0.08);
        }

        .stat-icon {
            width: 48px; height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }

        .content-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }

        .content-card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .content-card-body { padding: 1.5rem; }

        /* ===== BUTTONS ===== */
        .btn-accent {
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            color: #fff;
            border: none;
            border-radius: 0.625rem;
            font-weight: 600;
            padding: 0.5rem 1.25rem;
            transition: all 0.25s;
            font-size: 0.85rem;
        }

        .btn-accent:hover {
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(59,130,246,0.3);
        }

        .btn-back {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 0.625rem;
            padding: 0.5rem 1rem;
            color: var(--text-muted);
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
            transition: all 0.25s;
        }

        .btn-back:hover {
            color: var(--accent);
            border-color: var(--accent);
            box-shadow: 0 4px 12px rgba(59,130,246,0.1);
        }

        /* ===== TABLE ===== */
        .table-custom thead th {
            background: #f8fafc;
            color: var(--text-muted);
            font-weight: 700;
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0.875rem 1rem;
            border-bottom: 2px solid var(--border);
        }

        .table-custom tbody td {
            padding: 0.875rem 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #f3f4f6;
            font-size: 0.85rem;
        }

        .table-custom tbody tr { transition: background 0.2s; }
        .table-custom tbody tr:hover { background: #f8fafc; }

        /* ===== PROGRESS BAR ===== */
        .progress-mini {
            height: 6px;
            background: #f1f5f9;
            border-radius: 100px;
            overflow: hidden;
        }
        .progress-mini .fill {
            height: 100%;
            border-radius: 100px;
            transition: width 0.6s ease;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 992px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .sidebar.collapsed + .sidebar-overlay + .main-content { margin-left: 0; }
        }

        /* ===== ANIMATIONS ===== */
        .fade-up { animation: fadeUp 0.5s ease-out; }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ===== DEADLINE BADGE ===== */
        .deadline-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 0.7rem;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 6px;
        }
        .deadline-ok { background: #dcfce7; color: #166534; }
        .deadline-warn { background: #fef3c7; color: #92400e; }
        .deadline-danger { background: #fee2e2; color: #991b1b; }
        .deadline-over { background: #ef4444; color: #fff; }
    </style>
    @yield('styles')
</head>
<body>
    @php $user = session('user'); @endphp

    <!-- SIDEBAR -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <h4>ITP SYSTEM</h4>
            <small>Mini LNG Vessel</small>
        </div>

        <nav class="sidebar-nav">
            @if($user->role === 'admin')
                <div class="nav-label"><span>Management</span></div>
                <a href="/admin/dashboard" title="Dashboard" class="{{ request()->is('admin/dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-pie"></i> <span>Dashboard</span>
                </a>
                <a href="/admin/users/create" title="Buat Akun" class="{{ request()->is('admin/users*') ? 'active' : '' }}">
                    <i class="fas fa-user-plus"></i> <span>Buat Akun</span>
                </a>
                <a href="/admin/projects/create" title="Start Project" class="{{ request()->is('admin/projects/create') ? 'active' : '' }}">
                    <i class="fas fa-rocket"></i> <span>Start Project</span>
                </a>
            @else
                <div class="nav-label"><span>Navigasi</span></div>
                <a href="/dashboard" title="Pilih Project" class="{{ request()->is('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-th-large"></i> <span>Pilih Project</span>
                </a>
            @endif

            <div class="nav-label"><span>Komunikasi</span></div>
            <a href="/notifications" title="Notifikasi" class="{{ request()->is('notifications*') ? 'active' : '' }}" style="position:relative">
                <i class="fas fa-bell"></i> <span>Notifikasi</span>
                <span id="notifSidebarBadge" style="display:none;position:absolute;right:12px;top:50%;transform:translateY(-50%);background:#ef4444;color:#fff;font-size:0.5rem;font-weight:700;width:18px;height:18px;border-radius:50%;align-items:center;justify-content:center"></span>
            </a>
            <a href="/messages" title="Pesan" class="{{ request()->is('messages*') ? 'active' : '' }}" style="position:relative">
                <i class="fas fa-comments"></i> <span>Pesan</span>
                <span id="msgBadge" style="display:none;position:absolute;right:12px;top:50%;transform:translateY(-50%);background:#ef4444;color:#fff;font-size:0.5rem;font-weight:700;width:18px;height:18px;border-radius:50%;align-items:center;justify-content:center"></span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <a href="/logout" title="Logout" class="btn-logout">
                <i class="fas fa-sign-out-alt"></i><span>Logout</span>
            </a>
        </div>
    </div>

    <!-- SIDEBAR OVERLAY (mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <!-- MAIN -->
    <div class="main-content">
        <div class="topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-link text-dark p-0" id="sidebarToggleBtn" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <span class="topbar-title">@yield('page-title', 'Dashboard')</span>
            </div>
            <div class="user-info">
                <a href="/notifications" class="position-relative me-2" style="color:var(--text-muted);font-size:1.1rem" title="Notifikasi">
                    <i class="fas fa-bell"></i>
                    <span id="notifTopBadge" style="display:none;position:absolute;top:-4px;right:-6px;background:#ef4444;color:#fff;font-size:0.5rem;font-weight:700;min-width:16px;height:16px;border-radius:50%;align-items:center;justify-content:center;padding:0 3px"></span>
                </a>
                <div class="text-end d-none d-sm-block">
                    <div class="fw-bold" style="font-size:0.85rem">{{ $user->name }}</div>
                    <span class="role-badge role-{{ $user->role }}">{{ strtoupper($user->role) }}</span>
                </div>
                <div class="user-avatar">{{ strtoupper(substr($user->name,0,1)) }}</div>
            </div>
        </div>

        <div class="page-content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert" style="font-size:0.85rem">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert" style="font-size:0.85rem">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <!-- Global Toast Container -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1055;">
        <div id="msgToast" class="toast align-items-center text-bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
            <div class="d-flex">
                <div class="toast-body" style="font-size:0.85rem">
                    <strong><i class="fas fa-comments me-2"></i>Pesan Baru</strong><br>
                    <span id="msgToastSender" class="fw-bold d-block mt-1"></span>
                    <span id="msgToastText" class="text-truncate d-block" style="max-width:200px"></span>
                    <a href="/messages" class="btn btn-sm btn-light mt-2 py-1 px-3 text-primary fw-bold" style="font-size:0.75rem">Lihat</a>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
        <!-- Notification Toast -->
        <div id="notifToast" class="toast align-items-center border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="6000" style="background:linear-gradient(135deg,#1e293b,#334155);color:#fff">
            <div class="d-flex">
                <div class="toast-body" style="font-size:0.85rem">
                    <strong><i class="fas fa-bell me-2" id="notifToastIcon"></i><span id="notifToastTitle">Notifikasi Baru</span></strong><br>
                    <span id="notifToastMsg" class="d-block mt-1" style="max-width:250px;font-size:0.8rem;color:#cbd5e1"></span>
                    <a href="/notifications" class="btn btn-sm mt-2 py-1 px-3 fw-bold" style="font-size:0.75rem;background:rgba(59,130,246,0.2);color:#93c5fd;border:1px solid rgba(59,130,246,0.3);border-radius:0.5rem">Lihat Semua</a>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Global Message Notification Polling
        const msgBadge = document.getElementById('msgBadge');
        const toastEl = document.getElementById('msgToast');
        const toast = new bootstrap.Toast(toastEl);
        
        // Use user-specific storage key so different accounts on same device don't conflict
        const storageKey = 'last_msg_id_user_' + {{ session('user') ? session('user')->id : 0 }};
        let globalLastMsgId = localStorage.getItem(storageKey) || 0;

        function checkNewMessages() {
            fetch('/messages/unread-count')
                .then(r => r.json())
                .then(res => {
                    // Update badge based on unread count
                    if (res.unread_count > 0) {
                        if (msgBadge) {
                            msgBadge.style.display = 'flex';
                            msgBadge.innerText = res.unread_count > 99 ? '99+' : res.unread_count;
                        }
                    } else {
                        if (msgBadge) msgBadge.style.display = 'none';
                    }

                    // Show toast only for genuinely new unnotified messages
                    if (res.latest_id > 0 && res.latest_id > globalLastMsgId) {
                        document.getElementById('msgToastSender').innerText = res.sender + ' (' + res.project_name + ')';
                        document.getElementById('msgToastText').innerText = res.message;
                        toast.show();

                        globalLastMsgId = res.latest_id;
                        localStorage.setItem(storageKey, res.latest_id);
                    }
                })
                .catch(err => console.error('Poll error', err));
        }

        // === Notification Polling ===
        const notifTopBadge = document.getElementById('notifTopBadge');
        const notifSidebarBadge = document.getElementById('notifSidebarBadge');
        const notifToastEl = document.getElementById('notifToast');
        const notifToast = new bootstrap.Toast(notifToastEl);
        const notifStorageKey = 'last_notif_id_user_' + {{ session('user') ? session('user')->id : 0 }};
        let globalLastNotifId = parseInt(localStorage.getItem(notifStorageKey) || '0');

        function checkNotifications() {
            fetch('/notifications/unread-count')
                .then(r => r.json())
                .then(res => {
                    if (res.count > 0) {
                        const label = res.count > 99 ? '99+' : res.count;
                        if (notifTopBadge) { notifTopBadge.style.display = 'flex'; notifTopBadge.innerText = label; }
                        if (notifSidebarBadge) { notifSidebarBadge.style.display = 'flex'; notifSidebarBadge.innerText = label; }
                    } else {
                        if (notifTopBadge) notifTopBadge.style.display = 'none';
                        if (notifSidebarBadge) notifSidebarBadge.style.display = 'none';
                    }

                    // Show toast for new notifications
                    if (res.latest_id > 0 && res.latest_id > globalLastNotifId && res.latest) {
                        const typeIcons = {
                            submit: 'fa-file-upload',
                            approved: 'fa-check-circle',
                            needs_revision: 'fa-times-circle',
                            rejected: 'fa-times-circle'
                        };
                        const iconEl = document.getElementById('notifToastIcon');
                        iconEl.className = 'fas ' + (typeIcons[res.latest.type] || 'fa-bell') + ' me-2';
                        document.getElementById('notifToastTitle').innerText = res.latest.title || 'Notifikasi Baru';
                        document.getElementById('notifToastMsg').innerText = res.latest.message || '';
                        notifToast.show();

                        globalLastNotifId = res.latest_id;
                        localStorage.setItem(notifStorageKey, res.latest_id);
                    }
                })
                .catch(() => {});
        }

        // Only poll if user is logged in
        @if(session()->has('user'))
            setInterval(checkNewMessages, 5000);
            setInterval(checkNotifications, 6000);
            setTimeout(checkNewMessages, 1000);
            setTimeout(checkNotifications, 1500);
        @endif
    </script>
    
    @yield('scripts')

    <script>
        // Sidebar collapse toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const isMobile = window.innerWidth <= 992;
            if (isMobile) {
                const isOpen = sidebar.classList.toggle('open');
                if (overlay) overlay.classList.toggle('active', isOpen);
            } else {
                sidebar.classList.toggle('collapsed');
                localStorage.setItem('sidebar_collapsed', sidebar.classList.contains('collapsed') ? '1' : '0');
            }
        }

        // Close sidebar (called by overlay click)
        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.remove('open');
            if (overlay) overlay.classList.remove('active');
        }

        // Restore sidebar state on page load
        (function() {
            const collapsed = localStorage.getItem('sidebar_collapsed');
            if (collapsed === '1' && window.innerWidth > 992) {
                document.getElementById('sidebar').classList.add('collapsed');
            }
        })();
    </script>
</body>
</html>
