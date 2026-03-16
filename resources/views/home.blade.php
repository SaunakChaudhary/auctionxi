<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auction XI — Cricket Tournament Auction Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --primary: #6c3fc5;
            --primary-dark: #4e2d8f;
            --primary-light: #8b5cf6;
            --accent: #f59e0b;
            --accent-dark: #d97706;
            --dark: #0f0f1a;
            --dark2: #1a1a2e;
            --dark3: #16213e;
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
            background: #fff;
            color: #1e1e2e;
            overflow-x: hidden;
        }

        /* ── NAVBAR ── */
        .navbar {
            background: rgba(15, 15, 26, 0.95);
            backdrop-filter: blur(20px);
            padding: 0 40px;
            height: 70px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            transition: all 0.3s;
        }

        .navbar.scrolled {
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.4);
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .brand-icon {
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

        .brand-name {
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            font-size: 1.3rem;
            color: #fff;
        }

        .brand-name span {
            color: var(--accent);
        }

        .navbar-links {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .navbar-links a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .navbar-links a:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.08);
        }

        .btn-nav-login {
            background: rgba(108, 63, 197, 0.2) !important;
            border: 1px solid rgba(108, 63, 197, 0.4) !important;
            color: var(--primary-light) !important;
            font-weight: 600 !important;
        }

        .btn-nav-login:hover {
            background: var(--primary) !important;
            color: #fff !important;
            border-color: var(--primary) !important;
        }

        .btn-nav-cta {
            background: linear-gradient(135deg, var(--primary), var(--primary-light)) !important;
            color: #fff !important;
            font-weight: 600 !important;
            border: none !important;
            box-shadow: 0 4px 15px rgba(108, 63, 197, 0.3);
        }

        .btn-nav-cta:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(108, 63, 197, 0.4) !important;
        }

        /* ── HERO ── */
        .hero {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--dark) 0%, var(--dark2) 50%, var(--dark3) 100%);
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            padding-top: 70px;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background:
                radial-gradient(ellipse at 20% 50%, rgba(108, 63, 197, 0.25) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 20%, rgba(245, 158, 11, 0.1) 0%, transparent 50%);
        }

        /* Cricket pattern overlay */
        .hero::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image:
                radial-gradient(circle at 1px 1px, rgba(255, 255, 255, 0.03) 1px, transparent 0);
            background-size: 40px 40px;
        }

        .hero-content {
            position: relative;
            z-index: 10;
            padding: 80px 0;
        }

        .hero-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(108, 63, 197, 0.15);
            border: 1px solid rgba(108, 63, 197, 0.3);
            color: var(--primary-light);
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 24px;
        }

        .hero-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 900;
            font-size: clamp(2.5rem, 5vw, 4.5rem);
            color: #fff;
            line-height: 1.05;
            margin-bottom: 20px;
            letter-spacing: -1px;
        }

        .hero-title .highlight {
            background: linear-gradient(135deg, var(--accent), #fbbf24);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-title .highlight-purple {
            background: linear-gradient(135deg, var(--primary-light), #c4b5fd);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-desc {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.6);
            line-height: 1.7;
            margin-bottom: 36px;
            max-width: 520px;
        }

        .hero-buttons {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
            margin-bottom: 52px;
        }

        .btn-hero-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: #fff;
            text-decoration: none;
            padding: 15px 32px;
            border-radius: 14px;
            font-weight: 700;
            font-size: 0.95rem;
            transition: all 0.2s;
            box-shadow: 0 6px 25px rgba(108, 63, 197, 0.4);
        }

        .btn-hero-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 35px rgba(108, 63, 197, 0.5);
            color: #fff;
        }

        .btn-hero-secondary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.08);
            border: 1.5px solid rgba(255, 255, 255, 0.15);
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            padding: 15px 32px;
            border-radius: 14px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.2s;
            backdrop-filter: blur(10px);
        }

        .btn-hero-secondary:hover {
            background: rgba(255, 255, 255, 0.14);
            border-color: rgba(255, 255, 255, 0.25);
            color: #fff;
            transform: translateY(-2px);
        }

        .hero-stats {
            display: flex;
            gap: 40px;
            flex-wrap: wrap;
        }

        .hero-stat .number {
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            font-size: 1.8rem;
            color: #fff;
            line-height: 1;
        }

        .hero-stat .label {
            font-size: 0.78rem;
            color: rgba(255, 255, 255, 0.4);
            font-weight: 500;
            margin-top: 2px;
        }

        /* Hero right visual */
        .hero-visual {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hero-card-stack {
            position: relative;
            width: 100%;
            max-width: 440px;
            margin: 0 auto;
        }

        .mock-card {
            background: rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 24px;
            color: #fff;
        }

        .mock-card.main-card {
            position: relative;
            z-index: 3;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
        }

        .mock-card.card-behind-1 {
            position: absolute;
            top: -15px;
            left: 20px;
            right: 20px;
            z-index: 2;
            opacity: 0.5;
            transform: scale(0.97);
        }

        .mock-card.card-behind-2 {
            position: absolute;
            top: -28px;
            left: 36px;
            right: 36px;
            z-index: 1;
            opacity: 0.25;
            transform: scale(0.94);
        }

        .mock-player-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 20px;
        }

        .mock-avatar {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
        }

        .mock-player-name {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .mock-player-role {
            font-size: 0.78rem;
            color: rgba(255, 255, 255, 0.5);
        }

        .mock-pid {
            background: rgba(108, 63, 197, 0.3);
            border: 1px solid rgba(108, 63, 197, 0.4);
            color: var(--primary-light);
            padding: 3px 10px;
            border-radius: 6px;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 1px;
            margin-left: auto;
        }

        .mock-stats-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }

        .mock-stat-box {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 10px;
            text-align: center;
        }

        .mock-stat-box .val {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            color: var(--accent);
        }

        .mock-stat-box .lbl {
            font-size: 0.65rem;
            color: rgba(255, 255, 255, 0.4);
            margin-top: 2px;
        }

        .mock-sold-banner {
            background: linear-gradient(135deg, rgba(108, 63, 197, 0.4), rgba(139, 92, 246, 0.2));
            border: 1px solid rgba(108, 63, 197, 0.4);
            border-radius: 12px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .mock-sold-banner .sold-label {
            font-size: 0.72rem;
            color: rgba(255, 255, 255, 0.5);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .mock-sold-banner .sold-team {
            font-weight: 700;
            font-size: 0.9rem;
        }

        .mock-sold-banner .sold-price {
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            font-size: 1.2rem;
            color: var(--accent);
        }

        /* Floating badges */
        .float-badge {
            position: absolute;
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            padding: 10px 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            font-size: 0.8rem;
            font-weight: 600;
            color: #1e1e2e;
            animation: floatAnim 3s ease-in-out infinite;
        }

        .float-badge.badge-1 {
            top: 10px;
            right: -20px;
            animation-delay: 0s;
        }

        .float-badge.badge-2 {
            bottom: 20px;
            left: -20px;
            animation-delay: 1.5s;
        }

        @keyframes floatAnim {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-8px);
            }
        }

        /* ── SECTION COMMONS ── */
        section {
            padding: 90px 0;
        }

        .section-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--primary);
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .section-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            font-size: clamp(1.8rem, 3vw, 2.5rem);
            color: #1e1e2e;
            line-height: 1.2;
            margin-bottom: 16px;
        }

        .section-title .colored {
            color: var(--primary);
        }

        .section-desc {
            font-size: 1rem;
            color: #6b7280;
            line-height: 1.7;
            max-width: 560px;
        }

        /* ── FEATURES ── */
        .features-section {
            background: #f8f9ff;
        }

        .feature-card {
            background: #fff;
            border-radius: 20px;
            padding: 32px 28px;
            height: 100%;
            border: 1px solid #f0f2f8;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary), var(--primary-light));
            transform: scaleX(0);
            transition: transform 0.3s;
            transform-origin: left;
        }

        .feature-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 50px rgba(108, 63, 197, 0.12);
            border-color: rgba(108, 63, 197, 0.15);
        }

        .feature-card:hover::before {
            transform: scaleX(1);
        }

        .feature-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 20px;
        }

        .feature-card h4 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1.05rem;
            color: #1e1e2e;
            margin-bottom: 10px;
        }

        .feature-card p {
            font-size: 0.875rem;
            color: #6b7280;
            line-height: 1.65;
        }

        /* ── HOW IT WORKS ── */
        .how-section {
            background: #fff;
        }

        .step-card {
            text-align: center;
            padding: 32px 20px;
            position: relative;
        }

        .step-card::after {
            content: '→';
            position: absolute;
            top: 48px;
            right: -20px;
            font-size: 1.5rem;
            color: #d1d5db;
            font-weight: 300;
        }

        .step-card:last-child::after {
            display: none;
        }

        .step-number {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: #fff;
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 8px 25px rgba(108, 63, 197, 0.3);
            position: relative;
            z-index: 1;
        }

        .step-number::before {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            opacity: 0.2;
            z-index: -1;
        }

        .step-card h5 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            color: #1e1e2e;
            margin-bottom: 10px;
        }

        .step-card p {
            font-size: 0.85rem;
            color: #6b7280;
            line-height: 1.6;
        }

        /* ── AUCTION STEPS ── */
        .auction-steps-section {
            background: linear-gradient(135deg, var(--dark) 0%, var(--dark2) 100%);
        }

        .auction-step-item {
            display: flex;
            gap: 20px;
            margin-bottom: 32px;
            align-items: flex-start;
        }

        .auction-step-item:last-child {
            margin-bottom: 0;
        }

        .auction-step-dot {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: rgba(108, 63, 197, 0.2);
            border: 1px solid rgba(108, 63, 197, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .auction-step-content h5 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            color: #fff;
            margin-bottom: 6px;
        }

        .auction-step-content p {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.5);
            line-height: 1.6;
        }

        /* Auction panel mock */
        .auction-panel-mock {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            padding: 24px;
            color: #fff;
        }

        .panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .panel-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1rem;
        }

        .panel-live {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #ef4444;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 700;
        }

        .panel-live-dot {
            width: 6px;
            height: 6px;
            background: #ef4444;
            border-radius: 50%;
            animation: livePulse 1.5s infinite;
        }

        @keyframes livePulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.3;
            }
        }

        .panel-search {
            display: flex;
            gap: 8px;
            margin-bottom: 16px;
        }

        .panel-input {
            flex: 1;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 10px;
            padding: 10px 14px;
            color: #fff;
            font-size: 0.875rem;
        }

        .panel-btn {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border: none;
            border-radius: 10px;
            color: #fff;
            padding: 10px 16px;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
        }

        .panel-player-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 14px;
            padding: 16px;
            margin-bottom: 14px;
        }

        .panel-player-info {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
        }

        .panel-avatar {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
        }

        .panel-assign-row {
            display: flex;
            gap: 8px;
        }

        .panel-select {
            flex: 1;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 8px;
            padding: 8px 10px;
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.8rem;
        }

        .panel-assign-btn {
            background: linear-gradient(135deg, #059669, #10b981);
            border: none;
            border-radius: 8px;
            color: #fff;
            padding: 8px 14px;
            font-size: 0.8rem;
            font-weight: 700;
            cursor: pointer;
            white-space: nowrap;
        }

        /* ── ROLES SECTION ── */
        .roles-section {
            background: #f8f9ff;
        }

        .role-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #fff;
            border: 1.5px solid #e8eaf0;
            border-radius: 50px;
            padding: 10px 20px;
            font-weight: 600;
            font-size: 0.875rem;
            color: #374151;
            transition: all 0.2s;
            text-decoration: none;
        }

        .role-pill:hover {
            border-color: var(--primary);
            color: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(108, 63, 197, 0.1);
        }

        /* ── CTA ── */
        .cta-section {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 50%, var(--primary-light) 100%);
            position: relative;
            overflow: hidden;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle at 1px 1px, rgba(255, 255, 255, 0.06) 1px, transparent 0);
            background-size: 30px 30px;
        }

        .cta-section .section-title {
            color: #fff;
        }

        .cta-section .section-desc {
            color: rgba(255, 255, 255, 0.7);
            max-width: 600px;
        }

        .btn-cta-white {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #fff;
            color: var(--primary);
            padding: 15px 32px;
            border-radius: 14px;
            font-weight: 700;
            font-size: 0.95rem;
            text-decoration: none;
            transition: all 0.2s;
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.15);
        }

        .btn-cta-white:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.2);
            color: var(--primary-dark);
        }

        .btn-cta-outline {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.12);
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: #fff;
            padding: 13px 32px;
            border-radius: 14px;
            font-weight: 600;
            font-size: 0.95rem;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-cta-outline:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            transform: translateY(-2px);
        }

        /* ── FOOTER ── */
        footer {
            background: var(--dark);
            padding: 50px 0 24px;
            color: rgba(255, 255, 255, 0.4);
        }

        footer .footer-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 16px;
        }

        footer .footer-brand-name {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1.1rem;
            color: #fff;
        }

        footer p {
            font-size: 0.85rem;
            line-height: 1.7;
            max-width: 300px;
        }

        footer .footer-links h6 {
            color: #fff;
            font-weight: 600;
            font-size: 0.85rem;
            margin-bottom: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        footer .footer-links a {
            display: block;
            color: rgba(255, 255, 255, 0.4);
            text-decoration: none;
            font-size: 0.85rem;
            margin-bottom: 10px;
            transition: color 0.2s;
        }

        footer .footer-links a:hover {
            color: var(--primary-light);
        }

        footer .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            margin-top: 40px;
            padding-top: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: gap;
            gap: 12px;
        }

        footer .footer-bottom a {
            color: var(--primary-light);
            font-weight: 600;
            text-decoration: none;
        }

        /* ── HAMBURGER ── */
        .hamburger {
            display: none;
            flex-direction: column;
            gap: 5px;
            background: none;
            border: none;
            cursor: pointer;
            padding: 6px;
        }

        .hamburger span {
            display: block;
            width: 24px;
            height: 2px;
            background: #fff;
            border-radius: 2px;
            transition: all 0.3s;
        }

        @media (max-width: 991.98px) {
            .navbar {
                padding: 0 20px;
            }

            .navbar-links {
                display: none;
            }

            .hamburger {
                display: flex;
            }

            .mobile-menu {
                display: none;
                position: fixed;
                top: 70px;
                left: 0;
                right: 0;
                background: var(--dark2);
                padding: 20px;
                z-index: 999;
                border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            }

            .mobile-menu.open {
                display: block;
            }

            .mobile-menu a {
                display: block;
                color: rgba(255, 255, 255, 0.7);
                text-decoration: none;
                padding: 12px 0;
                font-size: 0.95rem;
                font-weight: 500;
                border-bottom: 1px solid rgba(255, 255, 255, 0.06);
                transition: color 0.2s;
            }

            .mobile-menu a:hover {
                color: #fff;
            }

            .hero-content {
                padding: 50px 0;
            }

            .hero-visual {
                margin-top: 40px;
            }

            .step-card::after {
                display: none;
            }

            section {
                padding: 60px 0;
            }

            .float-badge {
                display: none;
            }
        }

        /* ── ANIMATIONS ── */
        .fade-up {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }

        .fade-up.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .delay-1 {
            transition-delay: 0.1s;
        }

        .delay-2 {
            transition-delay: 0.2s;
        }

        .delay-3 {
            transition-delay: 0.3s;
        }

        .delay-4 {
            transition-delay: 0.4s;
        }

        .delay-5 {
            transition-delay: 0.5s;
        }
    </style>
</head>

<body>

    <!-- ── NAVBAR ── -->
    <nav class="navbar" id="mainNavbar">
        <a href="{{ route('home') }}" class="navbar-brand">
            <div class="brand-icon">🏏</div>
            <span class="brand-name">Auction <span>XI</span></span>
        </a>

        <div class="navbar-links">
            <a href="#features">Features</a>
            <a href="#how-it-works">How It Works</a>
            <a href="#auction-steps">Auction Steps</a>
            <a href="{{ route('login') }}" class="btn-nav-login">Login</a>
            <a href="{{ route('register') }}" class="btn-nav-cta">Create Tournament</a>
        </div>

        <button class="hamburger" id="hamburger">
            <span></span><span></span><span></span>
        </button>
    </nav>

    <!-- Mobile Menu -->
    <div class="mobile-menu" id="mobileMenu">
        <a href="#features">Features</a>
        <a href="#how-it-works">How It Works</a>
        <a href="#auction-steps">Auction Steps</a>
        <a href="{{ route('login') }}">Login</a>
        <a href="{{ route('register') }}">Create Tournament</a>
    </div>

    <!-- ── HERO ── -->
    <section class="hero" id="home">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content fade-up visible">
                        <div class="hero-eyebrow">
                            🏏 &nbsp; Cricket Auction Portal
                        </div>
                        <h1 class="hero-title">
                            Run Your Cricket<br>
                            <span class="highlight">Player Auction</span><br>
                            <span class="highlight-purple">Like a Pro</span>
                        </h1>
                        <p class="hero-desc">
                            Auction XI is the ultimate platform to organize, manage, and conduct cricket tournament
                            player auctions. From player registrations to live auction results — all in one place.
                        </p>
                        <div class="hero-buttons">
                            <a href="{{ route('register') }}" class="btn-hero-primary">
                                <i class="bi bi-plus-circle-fill"></i>
                                Create Tournament
                            </a>
                            <a href="{{ route('login') }}" class="btn-hero-secondary">
                                <i class="bi bi-box-arrow-in-right"></i>
                                Login
                            </a>
                        </div>
                        <div class="hero-stats">
                            <div class="hero-stat">
                                <div class="number">100%</div>
                                <div class="label">Free to Use</div>
                            </div>
                            <div class="hero-stat">
                                <div class="number">Beta</div>
                                <div class="label">Version</div>
                            </div>
                            <div class="hero-stat">
                                <div class="number">∞</div>
                                <div class="label">Players</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-visual fade-up visible delay-2">
                        <div class="hero-card-stack">
                            <div class="mock-card card-behind-2"></div>
                            <div class="mock-card card-behind-1"></div>
                            <div class="mock-card main-card">
                                <div class="mock-player-header">
                                    <div class="mock-avatar">🏏</div>
                                    <div>
                                        <div class="mock-player-name">Rohit Sharma</div>
                                        <div class="mock-player-role">Right-Hand Batsman</div>
                                    </div>
                                    <div class="mock-pid">PX1001</div>
                                </div>
                                <div class="mock-stats-row">
                                    <div class="mock-stat-box">
                                        <div class="val">28</div>
                                        <div class="lbl">Age</div>
                                    </div>
                                    <div class="mock-stat-box">
                                        <div class="val">Mumbai</div>
                                        <div class="lbl">City</div>
                                    </div>
                                    <div class="mock-stat-box">
                                        <div class="val">₹50K</div>
                                        <div class="lbl">Base Price</div>
                                    </div>
                                </div>
                                <div class="mock-sold-banner">
                                    <div>
                                        <div class="sold-label">Sold To</div>
                                        <div class="sold-team">⚡ Thunder Strikers</div>
                                    </div>
                                    <div class="sold-price">₹1,20,000</div>
                                </div>
                            </div>

                            <div class="float-badge badge-1">
                                <span style="font-size:1.1rem;">🔥</span>
                                <span>Live Auction</span>
                            </div>
                            <div class="float-badge badge-2">
                                <span style="color:#10b981; font-size:1.1rem;">✓</span>
                                <span>Player Sold!</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── FEATURES ── -->
    <section class="features-section" id="features">
        <div class="container">
            <div class="text-center mb-60 fade-up" style="margin-bottom:52px;">
                <div class="section-eyebrow"><i class="bi bi-stars"></i> Platform Features</div>
                <h2 class="section-title">Everything You Need to<br><span class="colored">Run a Great Auction</span>
                </h2>
                <p class="section-desc mx-auto">Powerful tools designed specifically for cricket tournament organizers.
                    Simple, fast, and effective.</p>
            </div>

            <div class="row g-4">
                <div class="col-md-6 col-lg-4 fade-up delay-1">
                    <div class="feature-card">
                        <div class="feature-icon"
                            style="background: linear-gradient(135deg,rgba(108,63,197,0.1),rgba(139,92,246,0.15));">🏆
                        </div>
                        <h4>Tournament Management</h4>
                        <p>Create and manage multiple cricket tournaments. Set team budgets, auction dates, and share
                            public links with players and viewers.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 fade-up delay-2">
                    <div class="feature-card">
                        <div class="feature-icon"
                            style="background: linear-gradient(135deg,rgba(245,158,11,0.1),rgba(251,191,36,0.15));">👥
                        </div>
                        <h4>Team Management</h4>
                        <p>Create teams with logos, owner details, and budgets. Track spending in real-time and view
                            complete squad details instantly.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 fade-up delay-3">
                    <div class="feature-card">
                        <div class="feature-icon"
                            style="background: linear-gradient(135deg,rgba(16,185,129,0.1),rgba(52,211,153,0.15));">📋
                        </div>
                        <h4>Player Registration</h4>
                        <p>Players register through a public link — no account needed. Organizers approve or reject
                            registrations with full profile management.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 fade-up delay-1">
                    <div class="feature-card">
                        <div class="feature-icon"
                            style="background: linear-gradient(135deg,rgba(239,68,68,0.1),rgba(252,165,165,0.15));">🎙️
                        </div>
                        <h4>Auction Control Panel</h4>
                        <p>Search players by ID, assign them to teams, set final bid prices, and mark them as sold or
                            unsold — all from one control panel.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 fade-up delay-2">
                    <div class="feature-card">
                        <div class="feature-icon"
                            style="background: linear-gradient(135deg,rgba(59,130,246,0.1),rgba(147,197,253,0.15));">📺
                        </div>
                        <h4>Live Viewer Page</h4>
                        <p>Viewers watch the auction in near real-time through a public link. The page auto-refreshes
                            every 10–15 seconds — no login required.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 fade-up delay-3">
                    <div class="feature-card">
                        <div class="feature-icon"
                            style="background: linear-gradient(135deg,rgba(236,72,153,0.1),rgba(249,168,212,0.15));">📊
                        </div>
                        <h4>Auction Results</h4>
                        <p>View complete auction results with player IDs, names, roles, teams, and final prices.
                            Download-ready results table for all stakeholders.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── HOW IT WORKS ── -->
    <section class="how-section" id="how-it-works">
        <div class="container">
            <div class="text-center mb-60 fade-up" style="margin-bottom:52px;">
                <div class="section-eyebrow"><i class="bi bi-diagram-3"></i> How It Works</div>
                <h2 class="section-title">Simple 5-Step Process to<br><span class="colored">Launch Your Auction</span>
                </h2>
            </div>

            <div class="row">
                <div class="col fade-up delay-1">
                    <div class="step-card">
                        <div class="step-number">1</div>
                        <h5>Create Tournament</h5>
                        <p>Register and create your tournament with team count and budget settings.</p>
                    </div>
                </div>
                <div class="col fade-up delay-2">
                    <div class="step-card">
                        <div class="step-number">2</div>
                        <h5>Add Teams</h5>
                        <p>Create teams with logos and owner info. Budget is auto-assigned.</p>
                    </div>
                </div>
                <div class="col fade-up delay-3">
                    <div class="step-card">
                        <div class="step-number">3</div>
                        <h5>Register Players</h5>
                        <p>Share the public link. Players self-register with their details.</p>
                    </div>
                </div>
                <div class="col fade-up delay-4">
                    <div class="step-card">
                        <div class="step-number">4</div>
                        <h5>Start Auction</h5>
                        <p>Use the control panel to bid players and assign them to teams.</p>
                    </div>
                </div>
                <div class="col fade-up delay-5">
                    <div class="step-card">
                        <div class="step-number">5</div>
                        <h5>View Results</h5>
                        <p>Share the viewer link. Everyone watches live auction results.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── AUCTION STEPS ── -->
    <section class="auction-steps-section" id="auction-steps">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6 fade-up">
                    <div class="section-eyebrow" style="color: var(--primary-light);">
                        <i class="bi bi-broadcast"></i> Auction Workflow
                    </div>
                    <h2 class="section-title" style="color:#fff;">
                        How the <span style="color:var(--accent);">Auction Panel</span><br>Works
                    </h2>
                    <p class="section-desc" style="color:rgba(255,255,255,0.5); margin-bottom:36px;">
                        The auctioneer conducts the bidding offline. After each player's bidding round ends, they update
                        the result manually in seconds.
                    </p>

                    <div class="auction-step-item fade-up delay-1">
                        <div class="auction-step-dot">🔍</div>
                        <div class="auction-step-content">
                            <h5>Search Player by ID</h5>
                            <p>Enter the Player ID (e.g. PX1001) to instantly load their full profile and details.</p>
                        </div>
                    </div>
                    <div class="auction-step-item fade-up delay-2">
                        <div class="auction-step-dot">🎙️</div>
                        <div class="auction-step-content">
                            <h5>Conduct Bidding Offline</h5>
                            <p>Run the live bidding round in your venue. Team owners bid against each other verbally.
                            </p>
                        </div>
                    </div>
                    <div class="auction-step-item fade-up delay-3">
                        <div class="auction-step-dot">✅</div>
                        <div class="auction-step-content">
                            <h5>Assign Player to Team</h5>
                            <p>Select the winning team, enter the final price, and click Assign Player. Done!</p>
                        </div>
                    </div>
                    <div class="auction-step-item fade-up delay-4">
                        <div class="auction-step-dot">📺</div>
                        <div class="auction-step-content">
                            <h5>Viewers See Live Updates</h5>
                            <p>The public viewer page auto-refreshes and shows the latest results to everyone watching.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 fade-up delay-2">
                    <div class="auction-panel-mock">
                        <div class="panel-header">
                            <span class="panel-title">🎙️ Auction Control Panel</span>
                            <span class="panel-live">
                                <span class="panel-live-dot"></span> LIVE
                            </span>
                        </div>

                        <div class="panel-search">
                            <input class="panel-input" placeholder="Search by Player ID e.g. PX1001" readonly>
                            <button class="panel-btn">Search</button>
                        </div>

                        <div class="panel-player-card">
                            <div class="panel-player-info">
                                <div class="panel-avatar">🏏</div>
                                <div>
                                    <div style="font-weight:700; font-size:0.95rem;">Virat Kohli</div>
                                    <div style="font-size:0.78rem; color:rgba(255,255,255,0.4);">Batsman &nbsp;|&nbsp;
                                        PX1003</div>
                                </div>
                                <div style="margin-left:auto; font-size:0.75rem; color:var(--accent);">Base: ₹75,000
                                </div>
                            </div>
                            <div class="panel-assign-row">
                                <select class="panel-select">
                                    <option>Select Team</option>
                                    <option>⚡ Thunder Strikers</option>
                                    <option>🔥 Fire Dragons</option>
                                </select>
                                <input class="panel-input" placeholder="Final Price" style="width:110px; flex:none;"
                                    readonly>
                                <button class="panel-assign-btn">✓ Assign</button>
                            </div>
                        </div>

                        <div style="display:flex; gap:8px; margin-top:4px;">
                            <div
                                style="flex:1; background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.2); border-radius:10px; padding:10px; text-align:center;">
                                <div style="font-size:1.1rem; font-weight:700; color:#10b981;">12</div>
                                <div style="font-size:0.7rem; color:rgba(255,255,255,0.4);">Sold</div>
                            </div>
                            <div
                                style="flex:1; background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.2); border-radius:10px; padding:10px; text-align:center;">
                                <div style="font-size:1.1rem; font-weight:700; color:#ef4444;">3</div>
                                <div style="font-size:0.7rem; color:rgba(255,255,255,0.4);">Unsold</div>
                            </div>
                            <div
                                style="flex:1; background:rgba(245,158,11,0.1); border:1px solid rgba(245,158,11,0.2); border-radius:10px; padding:10px; text-align:center;">
                                <div style="font-size:1.1rem; font-weight:700; color:var(--accent);">8</div>
                                <div style="font-size:0.7rem; color:rgba(255,255,255,0.4);">Remaining</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── ROLES ── -->
    <section class="roles-section">
        <div class="container">
            <div class="text-center fade-up" style="margin-bottom:48px;">
                <div class="section-eyebrow"><i class="bi bi-person-badge"></i> Player Roles</div>
                <h2 class="section-title">Register Players for<br><span class="colored">Every Role</span></h2>
            </div>
            <div class="d-flex flex-wrap justify-content-center gap-3 fade-up delay-1">
                <span class="role-pill"><span style="font-size:1.2rem;">🏏</span> Batsman</span>
                <span class="role-pill"><span style="font-size:1.2rem;">⚾</span> Bowler</span>
                <span class="role-pill"><span style="font-size:1.2rem;">🌟</span> All Rounder</span>
                <span class="role-pill"><span style="font-size:1.2rem;">🧤</span> Wicket Keeper</span>
            </div>
            <div class="text-center mt-5 fade-up delay-2">
                <p class="section-desc mx-auto" style="max-width:500px;">Each player gets a unique auto-generated
                    Player ID (PX1001, PX1002...) for easy tracking during the auction.</p>
            </div>
        </div>
    </section>

    <!-- ── CTA ── -->
    <section class="cta-section">
        <div class="container text-center position-relative">
            <div class="fade-up">
                <div class="section-eyebrow" style="color:rgba(255,255,255,0.7); justify-content:center;">
                    <i class="bi bi-rocket-takeoff"></i> Get Started Today
                </div>
                <h2 class="section-title" style="color:#fff;">
                    Ready to Conduct Your<br>Cricket Auction?
                </h2>
                <p class="section-desc mx-auto" style="color:rgba(255,255,255,0.65); margin-bottom:36px;">
                    It's completely free to use. Create your tournament in minutes and start managing your cricket
                    auction like a professional.
                </p>
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <a href="{{ route('register') }}" class="btn-cta-white">
                        <i class="bi bi-trophy-fill"></i>
                        Create Tournament — Free
                    </a>
                    <a href="{{ route('login') }}" class="btn-cta-outline">
                        <i class="bi bi-box-arrow-in-right"></i>
                        Login to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ── FOOTER ── -->
    <footer>
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="footer-brand">
                        <div class="brand-icon"
                            style="width:38px;height:38px;background:linear-gradient(135deg,#6c3fc5,#8b5cf6);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;">
                            🏏</div>
                        <span class="footer-brand-name">Auction XI</span>
                    </div>
                    <p>The ultimate cricket tournament player auction management portal. Simple, powerful, and
                        completely free to use.</p>
                </div>
                <div class="col-lg-2 col-6">
                    <div class="footer-links">
                        <h6>Platform</h6>
                        <a href="{{ route('register') }}">Create Tournament</a>
                        <a href="{{ route('login') }}">Login</a>
                        <a href="#features">Features</a>
                    </div>
                </div>
                <div class="col-lg-2 col-6">
                    <div class="footer-links">
                        <h6>Learn</h6>
                        <a href="#how-it-works">How It Works</a>
                        <a href="#auction-steps">Auction Steps</a>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="footer-links">
                        <h6>About</h6>
                        <p style="font-size:0.85rem; line-height:1.7;">Auction XI is a Beta platform for cricket
                            organizers. Built to simplify the chaos of player auctions.</p>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <span>© {{ date('Y') }} Auction XI. All rights reserved.</span>
                <span>Developed by <a href="https://saunak-info.onrender.com" target="_blank">Saunak
                        Chaudhary</a></span>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', () => {
            const nav = document.getElementById('mainNavbar');
            nav.classList.toggle('scrolled', window.scrollY > 20);
        });

        // Mobile menu
        const hamburger = document.getElementById('hamburger');
        const mobileMenu = document.getElementById('mobileMenu');
        hamburger.addEventListener('click', () => {
            mobileMenu.classList.toggle('open');
        });

        // Close mobile menu on link click
        mobileMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => mobileMenu.classList.remove('open'));
        });

        // Scroll animations
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, {
            threshold: 0.1
        });

        document.querySelectorAll('.fade-up').forEach(el => observer.observe(el));
    </script>
</body>

</html>
