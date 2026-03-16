<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | Auction XI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@600;700;800&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --primary: #1e40af;
            --primary-lt: #2563eb;
            --primary-pale: #eff6ff;
            --accent: #0f766e;
            --accent-lt: #14b8a6;
            --dark: #0f172a;
            --dark2: #1e293b;
            --mid: #334155;
            --muted: #64748b;
            --border: #e2e8f0;
            --bg: #f8fafc;
            --white: #ffffff;
            --danger: #dc2626;
            --success: #16a34a;
            --warning: #d97706;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--dark);
            min-height: 100vh;
            font-size: 15px;
            line-height: 1.6;
        }

        /* ── NAVBAR ── */
        .pub-nav {
            background: var(--dark);
            height: 60px;
            display: flex;
            align-items: center;
            padding: 0 32px;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .pub-nav-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .pub-nav-logo {
            width: 34px;
            height: 34px;
            background: var(--primary-lt);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .pub-nav-name {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            color: #fff;
        }

        .pub-nav-links {
            display: flex;
            gap: 4px;
        }

        .pub-nav-links a {
            color: rgba(255, 255, 255, 0.55);
            text-decoration: none;
            font-size: 0.82rem;
            font-weight: 500;
            padding: 6px 14px;
            border-radius: 6px;
            transition: all 0.15s;
        }

        .pub-nav-links a:hover,
        .pub-nav-links a.active {
            color: #fff;
            background: rgba(255, 255, 255, 0.08);
        }

        .live-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(220, 38, 38, 0.12);
            border: 1px solid rgba(220, 38, 38, 0.25);
            color: #f87171;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .live-dot {
            width: 6px;
            height: 6px;
            background: #ef4444;
            border-radius: 50%;
            animation: blink 1.4s ease-in-out infinite;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.2;
            }
        }

        /* ── REFRESH BAR ── */
        .refresh-bar {
            background: var(--primary);
            color: rgba(255, 255, 255, 0.9);
            padding: 8px 20px;
            text-align: center;
            font-size: 0.78rem;
            font-weight: 500;
            letter-spacing: 0.2px;
        }

        .refresh-bar button {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #fff;
            padding: 2px 10px;
            border-radius: 4px;
            font-size: 0.72rem;
            font-weight: 600;
            cursor: pointer;
            margin-left: 8px;
            transition: background 0.15s;
        }

        .refresh-bar button:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        /* ── MAIN ── */
        .pub-main {
            max-width: 1100px;
            margin: 0 auto;
            padding: 32px 20px;
        }

        /* ── CARDS ── */
        .card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .card-header {
            background: #f8fafc;
            border-bottom: 1px solid var(--border);
            padding: 14px 20px;
            font-weight: 600;
            font-size: 0.875rem;
            color: var(--dark);
            border-radius: 12px 12px 0 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card-body {
            padding: 20px;
        }

        /* ── TABLES ── */
        .table {
            margin: 0;
            font-size: 0.875rem;
        }

        .table thead th {
            background: #f8fafc;
            color: var(--muted);
            font-size: 0.72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 11px 16px;
            border: none;
            border-bottom: 1px solid var(--border);
        }

        .table tbody td {
            padding: 12px 16px;
            vertical-align: middle;
            border-color: #f1f5f9;
        }

        .table tbody tr:hover {
            background: #f8fafc;
        }

        /* ── BADGES ── */
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .status-sold {
            background: #ede9fe;
            color: #5b21b6;
        }

        .status-unsold {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-approved {
            background: #dcfce7;
            color: #166534;
        }

        .status-pending {
            background: #fef9c3;
            color: #854d0e;
        }

        .status-live {
            background: #fef9c3;
            color: #854d0e;
        }

        .status-pending-auction {
            background: #f1f5f9;
            color: #475569;
        }

        .status-completed {
            background: #ede9fe;
            color: #5b21b6;
        }

        /* ── PLAYER AVATAR ── */
        .p-avatar {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid var(--border);
        }

        .p-avatar-placeholder {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: 0.8rem;
            flex-shrink: 0;
        }

        /* ── PLAYER ID ── */
        .pid-badge {
            background: var(--primary-pale);
            color: var(--primary);
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 700;
            font-family: monospace;
            letter-spacing: 0.5px;
        }

        /* ── ROLE BADGE ── */
        .role-badge {
            background: #f0fdf4;
            color: #166534;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.72rem;
            font-weight: 600;
        }

        /* ── BUTTONS ── */
        .btn-primary {
            background: var(--primary-lt);
            border-color: var(--primary-lt);
            font-weight: 600;
            border-radius: 8px;
            padding: 9px 20px;
            font-size: 0.875rem;
        }

        .btn-primary:hover {
            background: var(--primary);
            border-color: var(--primary);
        }

        .btn-outline-primary {
            border-color: var(--primary-lt);
            color: var(--primary-lt);
            font-weight: 600;
            border-radius: 8px;
            padding: 8px 20px;
            font-size: 0.875rem;
        }

        .btn-outline-primary:hover {
            background: var(--primary-lt);
            color: #fff;
        }

        /* ── FORMS ── */
        .form-control,
        .form-select {
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 9px 14px;
            font-size: 0.875rem;
            transition: border-color 0.15s, box-shadow 0.15s;
            color: var(--dark);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-lt);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-label {
            font-weight: 600;
            font-size: 0.78rem;
            color: var(--mid);
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        /* ── ALERTS ── */
        .alert {
            border-radius: 10px;
            border: none;
            font-size: 0.875rem;
        }

        .alert-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
        }

        .alert-danger {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        /* ── FOOTER ── */
        .pub-footer {
            background: var(--dark2);
            color: rgba(255, 255, 255, 0.4);
            text-align: center;
            padding: 20px;
            font-size: 0.78rem;
            margin-top: 48px;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
        }

        .pub-footer a {
            color: #60a5fa;
            font-weight: 600;
            text-decoration: none;
        }

        .pub-footer a:hover {
            text-decoration: underline;
        }

        /* ── STAT BOX ── */
        .stat-box {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 16px 20px;
            text-align: center;
        }

        .stat-box .stat-num {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1.6rem;
            color: var(--dark);
            line-height: 1;
            margin-bottom: 4px;
        }

        .stat-box .stat-lbl {
            font-size: 0.72rem;
            color: var(--muted);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ── SECTION HEADER ── */
        .section-hdr {
            border-left: 3px solid var(--primary-lt);
            padding-left: 12px;
            margin-bottom: 20px;
        }

        .section-hdr h2 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--dark);
            margin: 0;
        }

        .section-hdr p {
            font-size: 0.82rem;
            color: var(--muted);
            margin: 2px 0 0;
        }

        @media (max-width: 768px) {
            .pub-nav {
                padding: 0 16px;
            }

            .pub-nav-links {
                display: none;
            }

            .pub-main {
                padding: 20px 14px;
            }
        }
    </style>

    @stack('styles')
</head>

<body>

    <nav class="pub-nav">
        <a href="{{ route('home') }}" class="pub-nav-brand">
            <div class="pub-nav-logo">🏏</div>
            <span class="pub-nav-name">Auction XI</span>
        </a>
        <div class="pub-nav-links d-none d-md-flex">
            @yield('nav-items')
        </div>
        <div class="live-chip">
            <div class="live-dot"></div>LIVE
        </div>
    </nav>

    @yield('refresh-bar')

    <main class="pub-main">
        @yield('content')
    </main>

    <footer class="pub-footer">
        Developed by
        <a href="https://saunak-info.onrender.com" target="_blank">
            Saunak Chaudhary
        </a>
        &nbsp;·&nbsp; Auction XI &copy; {{ date('Y') }}
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>
