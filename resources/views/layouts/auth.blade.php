<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Auth') | Auction XI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --primary: #6c3fc5;
            --primary-dark: #4e2d8f;
            --primary-light: #8b5cf6;
            --accent: #f59e0b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0f0f1a 0%, #1a1a2e 50%, #16213e 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(ellipse at center, rgba(108, 63, 197, 0.15) 0%, transparent 60%);
            animation: pulse 8s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }
        }

        .auth-container {
            width: 100%;
            max-width: 480px;
            position: relative;
            z-index: 10;
        }

        .auth-brand {
            text-align: center;
            margin-bottom: 32px;
        }

        .auth-brand .logo {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 16px;
            box-shadow: 0 8px 32px rgba(108, 63, 197, 0.4);
        }

        .auth-brand h1 {
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            font-size: 2rem;
            color: #fff;
            letter-spacing: -0.5px;
        }

        .auth-brand p {
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.85rem;
        }

        .auth-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 36px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
        }

        .auth-card h2 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1.4rem;
            color: #fff;
            margin-bottom: 6px;
        }

        .auth-card .subtitle {
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.85rem;
            margin-bottom: 28px;
        }

        .form-label {
            font-size: 0.78rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.7);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        .form-control,
        .form-select {
            background: rgba(255, 255, 255, 0.08);
            border: 1.5px solid rgba(255, 255, 255, 0.12);
            border-radius: 12px;
            padding: 12px 16px;
            color: #fff;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        .form-control:focus,
        .form-select:focus {
            background: rgba(255, 255, 255, 0.12);
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(108, 63, 197, 0.2);
            color: #fff;
            outline: none;
        }

        .form-select option {
            background: #1a1a2e;
            color: #fff;
        }

        .input-group-text {
            background: rgba(255, 255, 255, 0.08);
            border: 1.5px solid rgba(255, 255, 255, 0.12);
            border-right: none;
            color: rgba(255, 255, 255, 0.5);
            border-radius: 12px 0 0 12px;
        }

        .input-group .form-control {
            border-left: none;
            border-radius: 0 12px 12px 0;
        }

        .btn-auth {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border: none;
            border-radius: 12px;
            color: #fff;
            font-weight: 700;
            font-size: 0.95rem;
            transition: all 0.2s;
            box-shadow: 0 4px 20px rgba(108, 63, 197, 0.4);
            cursor: pointer;
        }

        .btn-auth:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(108, 63, 197, 0.5);
        }

        .auth-footer {
            text-align: center;
            margin-top: 24px;
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.85rem;
        }

        .auth-footer a {
            color: var(--primary-light);
            font-weight: 600;
            text-decoration: none;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }

        .alert {
            border-radius: 12px;
            border: none;
            font-size: 0.875rem;
            padding: 12px 16px;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.15);
            color: #6ee7b7;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.15);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 20px 0;
            color: rgba(255, 255, 255, 0.2);
            font-size: 0.8rem;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
        }

        .back-home {
            text-align: center;
            margin-top: 20px;
        }

        .back-home a {
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.82rem;
            text-decoration: none;
            transition: color 0.2s;
        }

        .back-home a:hover {
            color: rgba(255, 255, 255, 0.8);
        }

        .footer-credit {
            text-align: center;
            margin-top: 24px;
            color: rgba(255, 255, 255, 0.2);
            font-size: 0.78rem;
        }

        .footer-credit a {
            color: rgba(139, 92, 246, 0.7);
            text-decoration: none;
            font-weight: 600;
        }

        @media (max-width: 480px) {
            .auth-card {
                padding: 24px 20px;
            }
        }
    </style>
</head>

<body>
    <div class="auth-container">
        <div class="auth-brand">
            <div class="logo">🏏</div>
            <h1>Auction XI</h1>
            <p>Cricket Tournament Auction Portal</p>
        </div>

        @yield('content')

        <div class="back-home">
            <a href="{{ route('home') }}"><i class="bi bi-arrow-left me-1"></i>Back to Home</a>
        </div>

        <div class="footer-credit">
            Developed by <a href="https://saunak-info.onrender.com" target="_blank">Saunak Chaudhary</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>
