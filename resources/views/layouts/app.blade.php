<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Auction XI') | Auction XI</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --primary: #6c3fc5;
            --primary-dark: #4e2d8f;
            --primary-light: #8b5cf6;
            --accent: #f59e0b;
            --accent-dark: #d97706;
            --success: #10b981;
            --danger: #ef4444;
            --dark: #0f0f1a;
            --dark2: #1a1a2e;
            --dark3: #16213e;
            --sidebar-width: 260px;
            --topbar-height: 65px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f0f2f8;
            color: #1e1e2e;
            min-height: 100vh;
        }

        /* ── SIDEBAR ── */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, var(--dark) 0%, var(--dark2) 50%, var(--dark3) 100%);
            z-index: 1050;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.3);
        }

        .sidebar-brand {
            padding: 20px 24px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .sidebar-brand .brand-icon {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            box-shadow: 0 4px 15px rgba(108, 63, 197, 0.4);
        }

        .sidebar-brand .brand-text {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1.2rem;
            color: #fff;
            line-height: 1.1;
        }

        .sidebar-brand .brand-text span {
            display: block;
            font-size: 0.65rem;
            font-weight: 400;
            color: rgba(255, 255, 255, 0.5);
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .sidebar-nav {
            flex: 1;
            padding: 16px 0;
            overflow-y: auto;
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-nav::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 2px;
        }

        .nav-section-title {
            font-size: 0.65rem;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.3);
            padding: 16px 24px 6px;
        }

        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 24px;
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
            margin: 2px 0;
        }

        .sidebar-nav .nav-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.07);
            border-left-color: var(--primary-light);
        }

        .sidebar-nav .nav-link.active {
            color: #fff;
            background: linear-gradient(90deg, rgba(108, 63, 197, 0.3), rgba(108, 63, 197, 0.05));
            border-left-color: var(--primary-light);
        }

        .sidebar-nav .nav-link i {
            font-size: 1rem;
            width: 20px;
            text-align: center;
        }

        .sidebar-nav .nav-link .badge {
            margin-left: auto;
            font-size: 0.65rem;
        }

        .sidebar-footer {
            padding: 16px 24px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-user .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.875rem;
            color: #fff;
            overflow: hidden;
            flex-shrink: 0;
        }

        .sidebar-user .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .sidebar-user .user-info .name {
            font-size: 0.8rem;
            font-weight: 600;
            color: #fff;
            line-height: 1.2;
        }

        .sidebar-user .user-info .role {
            font-size: 0.65rem;
            color: rgba(255, 255, 255, 0.4);
        }

        /* ── TOPBAR ── */
        .topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--topbar-height);
            background: #fff;
            border-bottom: 1px solid #e8eaf0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            z-index: 1040;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: left 0.3s ease;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .topbar-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 1.1rem;
            color: #1e1e2e;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .topbar-btn {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: #f0f2f8;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6b7280;
            transition: all 0.2s;
            cursor: pointer;
        }

        .topbar-btn:hover {
            background: var(--primary);
            color: #fff;
        }

        .sidebar-toggle {
            display: none;
        }

        /* ── MAIN CONTENT ── */
        .main-content {
            margin-left: var(--sidebar-width);
            padding-top: var(--topbar-height);
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }

        .content-wrapper {
            padding: 28px;
        }

        /* ── PAGE HEADER ── */
        .page-header {
            margin-bottom: 24px;
        }

        .page-header h1 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1.6rem;
            color: #1e1e2e;
            margin-bottom: 4px;
        }

        .page-header p {
            color: #6b7280;
            font-size: 0.875rem;
        }

        /* ── CARDS ── */
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            box-shadow: 0 6px 24px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid #f0f2f8;
            padding: 18px 24px;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .card-body {
            padding: 24px;
        }

        /* ── STAT CARDS ── */
        .stat-card {
            border-radius: 16px;
            padding: 24px;
            color: #fff;
            position: relative;
            overflow: hidden;
            border: none;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            top: -20px;
            right: -20px;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            bottom: -30px;
            right: 20px;
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
        }

        .stat-card .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            margin-bottom: 16px;
        }

        .stat-card .stat-value {
            font-family: 'Poppins', sans-serif;
            font-size: 2rem;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 4px;
        }

        .stat-card .stat-label {
            font-size: 0.8rem;
            opacity: 0.8;
            font-weight: 500;
        }

        .stat-purple {
            background: linear-gradient(135deg, #6c3fc5, #8b5cf6);
        }

        .stat-amber {
            background: linear-gradient(135deg, #d97706, #f59e0b);
        }

        .stat-green {
            background: linear-gradient(135deg, #059669, #10b981);
        }

        .stat-blue {
            background: linear-gradient(135deg, #1d4ed8, #3b82f6);
        }

        /* ── BUTTONS ── */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border: none;
            border-radius: 10px;
            font-weight: 600;
            padding: 10px 22px;
            transition: all 0.2s;
            box-shadow: 0 4px 15px rgba(108, 63, 197, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(108, 63, 197, 0.4);
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
        }

        .btn-warning {
            background: linear-gradient(135deg, var(--accent-dark), var(--accent));
            border: none;
            border-radius: 10px;
            font-weight: 600;
            color: #fff;
            padding: 10px 22px;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
        }

        .btn-success {
            background: linear-gradient(135deg, #059669, var(--success));
            border: none;
            border-radius: 10px;
            font-weight: 600;
            padding: 10px 22px;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc2626, var(--danger));
            border: none;
            border-radius: 10px;
            font-weight: 600;
            padding: 10px 22px;
        }

        .btn-outline-primary {
            border: 2px solid var(--primary);
            color: var(--primary);
            border-radius: 10px;
            font-weight: 600;
            padding: 9px 22px;
        }

        .btn-outline-primary:hover {
            background: var(--primary);
            color: #fff;
        }

        .btn-sm {
            padding: 6px 14px;
            font-size: 0.8rem;
            border-radius: 8px;
        }

        /* ── TABLES ── */
        .table-card {
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        }

        .table {
            margin: 0;
        }

        .table thead th {
            background: #f8f9ff;
            color: #6b7280;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none;
            padding: 14px 18px;
        }

        .table tbody td {
            padding: 14px 18px;
            vertical-align: middle;
            border-color: #f0f2f8;
            font-size: 0.875rem;
        }

        .table tbody tr:hover {
            background: #fafbff;
        }

        /* ── BADGES ── */
        .badge-status {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .badge-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-approved {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-sold {
            background: #ddd6fe;
            color: #4c1d95;
        }

        .badge-unsold {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-open {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-closed {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-live {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-completed {
            background: #ddd6fe;
            color: #4c1d95;
        }

        /* ── FORMS ── */
        .form-control,
        .form-select {
            border: 2px solid #e8eaf0;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 0.875rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(108, 63, 197, 0.1);
        }

        .form-label {
            font-weight: 600;
            font-size: 0.8rem;
            color: #374151;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        /* ── ALERTS ── */
        .alert {
            border: none;
            border-radius: 12px;
            padding: 14px 18px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .alert-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .alert-info {
            background: #dbeafe;
            color: #1e40af;
        }

        /* ── PLAYER AVATAR ── */
        .player-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e8eaf0;
        }

        .player-avatar-placeholder {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: 0.875rem;
        }

        /* ── TEAM LOGO ── */
        .team-logo {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            object-fit: cover;
        }

        .team-logo-placeholder {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--accent-dark), var(--accent));
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: 1rem;
        }

        /* ── MOBILE OVERLAY ── */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1049;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .sidebar-overlay.show {
                display: block;
            }

            .topbar {
                left: 0;
            }

            .main-content {
                margin-left: 0;
            }

            .sidebar-toggle {
                display: flex;
            }

            .content-wrapper {
                padding: 20px 16px;
            }
        }

        /* ── SCROLLBAR ── */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f0f2f8;
        }

        ::-webkit-scrollbar-thumb {
            background: #c4c9d8;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary);
        }

        /* ── BREADCRUMB ── */
        .breadcrumb {
            background: none;
            padding: 0;
            margin: 0;
            font-size: 0.8rem;
        }

        .breadcrumb-item a {
            color: var(--primary);
            text-decoration: none;
        }

        .breadcrumb-item.active {
            color: #6b7280;
        }

        /* ── FOOTER ── */
        .content-footer {
            text-align: center;
            padding: 20px 28px;
            color: #9ca3af;
            font-size: 0.8rem;
            border-top: 1px solid #e8eaf0;
            margin-top: 20px;
        }

        .content-footer a {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
        }

        .content-footer a:hover {
            text-decoration: underline;
        }
    </style>

    @stack('styles')
</head>

<body>

    <!-- Sidebar Overlay (mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <a href="{{ route('dashboard') }}" class="sidebar-brand">
            <div class="brand-icon">🏏</div>
            <div class="brand-text">
                Auction XI
                <span>Cricket Auction Portal</span>
            </div>
        </a>

        <nav class="sidebar-nav">
            <div class="nav-section-title">Main</div>

            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-fill"></i> Dashboard
            </a>

            <div class="nav-section-title">Tournaments</div>

            <a href="{{ route('tournament.index') }}"
                class="nav-link {{ request()->routeIs('tournament.*') ? 'active' : '' }}">
                <i class="bi bi-trophy-fill"></i> My Tournaments
            </a>

            <a href="{{ route('tournament.create') }}" class="nav-link">
                <i class="bi bi-plus-circle-fill"></i> Create Tournament
            </a>

            @if (isset($tournament))
                <div class="nav-section-title">Current Tournament</div>

                <a href="{{ route('team.index', $tournament->id) }}"
                    class="nav-link {{ request()->routeIs('team.*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill"></i> Teams
                </a>

                <a href="{{ route('player.index', $tournament->id) }}"
                    class="nav-link {{ request()->routeIs('player.*') ? 'active' : '' }}">
                    <i class="bi bi-person-badge-fill"></i> Players
                </a>

                <a href="{{ route('player.import', $tournament->id) }}" class="nav-link">
                    <i class="bi bi-upload"></i> Import Players
                </a>

                <a href="{{ route('auction.panel', $tournament->id) }}"
                    class="nav-link {{ request()->routeIs('auction.*') ? 'active' : '' }}">
                    <i class="bi bi-broadcast"></i> Auction Panel
                </a>

                <a href="{{ route('auction.results', $tournament->id) }}" class="nav-link">
                    <i class="bi bi-bar-chart-fill"></i> Auction Results
                </a>
            @endif

            <div class="nav-section-title">Account</div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-link w-100 border-0 bg-transparent text-start">
                    <i class="bi bi-box-arrow-left"></i> Logout
                </button>
            </form>
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="avatar">
                    @if (Auth::user()->profile_photo)
                        <img src="{{ Storage::url(Auth::user()->profile_photo) }}" alt="">
                    @else
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    @endif
                </div>
                <div class="user-info">
                    <div class="name">{{ Auth::user()->name }}</div>
                    <div class="role">Organizer</div>
                </div>
            </div>
        </div>
    </aside>

    <!-- TOPBAR -->
    <header class="topbar">
        <div class="topbar-left">
            <button class="topbar-btn sidebar-toggle" id="sidebarToggle">
                <i class="bi bi-list" style="font-size:1.2rem;"></i>
            </button>
            <span class="topbar-title">@yield('page-title', 'Dashboard')</span>
        </div>
        <div class="topbar-right">
            @if (isset($tournament))
                <a href="{{ route('public.live', $tournament->code) }}" target="_blank"
                    class="btn btn-sm btn-warning text-white">
                    <i class="bi bi-eye-fill me-1"></i> Live View
                </a>
            @endif
            <div class="topbar-btn">
                <i class="bi bi-bell"></i>
            </div>
            <div class="dropdown">
                <button class="topbar-btn" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle" style="font-size:1.1rem;"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0"
                    style="border-radius:12px; min-width:180px;">
                    <li><span
                            class="dropdown-item-text fw-600 text-muted small px-3 pt-2">{{ Auth::user()->name }}</span>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-left me-2"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <div class="content-wrapper">

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="bi bi-exclamation-circle-fill me-2"></i>
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>

        <footer class="content-footer">
            Developed by <a href="https://saunak-info.onrender.com" target="_blank">Saunak Chaudhary</a>
        </footer>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Sidebar toggle
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const sidebarToggle = document.getElementById('sidebarToggle');

        sidebarToggle?.addEventListener('click', () => {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        });

        overlay?.addEventListener('click', () => {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
    </script>

    @stack('scripts')
</body>

</html>
