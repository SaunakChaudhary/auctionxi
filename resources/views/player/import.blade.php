@extends('layouts.app')

@section('title', 'Import Players')
@section('page-title', 'Import Players')

@section('content')

    {{-- ── PAGE HEADER ── --}}
    <div class="page-header">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('tournament.show', $tournament->id) }}">
                        {{ $tournament->name }}
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('player.index', $tournament->id) }}">Players</a>
                </li>
                <li class="breadcrumb-item active">Import CSV</li>
            </ol>
        </nav>
        <div class="imp-header">
            <div>
                <h1 class="imp-title">
                    <span class="imp-title-icon">📥</span>
                    Import Players via CSV
                </h1>
                <p class="imp-subtitle">
                    Bulk-add players to
                    <strong>{{ $tournament->name }}</strong>
                </p>
            </div>
            <a href="{{ route('player.index', $tournament->id) }}" class="imp-back-btn">
                <i class="bi bi-arrow-left"></i> Back to Players
            </a>
        </div>
    </div>

    {{-- ── SUCCESS / ERROR ALERTS ── --}}
    @if (session('success'))
        <div class="imp-alert imp-alert-success">
            <i class="bi bi-check-circle-fill"></i>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="imp-alert imp-alert-error">
            <i class="bi bi-exclamation-circle-fill"></i>
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="imp-alert imp-alert-error">
            <i class="bi bi-exclamation-circle-fill"></i>
            {{ $errors->first() }}
        </div>
    @endif

    {{-- ── MAIN GRID ── --}}
    <div class="imp-grid">

        {{-- ════════════════════════════
         LEFT — INSTRUCTIONS
         ════════════════════════════ --}}
        <div class="imp-instructions-col">

            {{-- Format Card --}}
            <div class="imp-card">
                <div class="imp-card-header">
                    <i class="bi bi-file-earmark-text"></i>
                    CSV Format Guide
                </div>
                <div class="imp-card-body">

                    {{-- Required columns --}}
                    <div class="imp-col-section">
                        <div class="imp-col-section-title imp-required-title">
                            <span class="imp-dot imp-dot-red"></span>
                            Required Columns
                        </div>
                        <div class="imp-col-list">
                            <div class="imp-col-item">
                                <div class="imp-col-top">
                                    <code class="imp-col-name">Player Name</code>
                                    <span class="imp-badge imp-badge-req">Required</span>
                                </div>
                                <div class="imp-col-desc">
                                    Full name of the player
                                </div>
                            </div>
                            <div class="imp-col-item">
                                <div class="imp-col-top">
                                    <code class="imp-col-name">Role</code>
                                    <span class="imp-badge imp-badge-req">Required</span>
                                </div>
                                <div class="imp-col-desc">
                                    Batsman / Bowler / All Rounder / Wicket Keeper
                                </div>
                            </div>
                            <div class="imp-col-item">
                                <div class="imp-col-top">
                                    <code class="imp-col-name">Mobile</code>
                                    <span class="imp-badge imp-badge-req">Required</span>
                                </div>
                                <div class="imp-col-desc">
                                    Player's mobile number
                                </div>
                            </div>
                            <div class="imp-col-item">
                                <div class="imp-col-top">
                                    <code class="imp-col-name">Photo</code>
                                    <span class="imp-badge imp-badge-opt">Optional</span>
                                </div>
                                <div class="imp-col-desc">
                                    Google Drive shareable link for player photo
                                </div>
                                <div class="imp-drive-tip">
                                    <i class="bi bi-google"></i>
                                    Drive → Share → Copy Link → Paste here
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Optional columns --}}
                    <div class="imp-col-section">
                        <div class="imp-col-section-title imp-optional-title">
                            <span class="imp-dot imp-dot-green"></span>
                            More Optional Columns
                        </div>
                        <div class="imp-optional-grid">
                            @foreach (['Email', 'Age', 'City', 'Batting Style', 'Bowling Style', 'Experience', 'Jersey Number', 'Base Price'] as $col)
                                <span class="imp-opt-tag">{{ $col }}</span>
                            @endforeach
                        </div>
                    </div>

                    {{-- Sample CSV --}}
                    <div class="imp-sample-box">
                        <div class="imp-sample-title">
                            <i class="bi bi-terminal"></i>
                            Sample CSV (first 2 rows)
                        </div>
                        <pre class="imp-sample-code">Player Name,Role,Mobile,Photo
Rohit Sharma,Batsman,9876543210,https://drive.google.com/file/d/ABC123/view
Jasprit Bumrah,Bowler,9876543211,</pre>
                    </div>

                    {{-- Notes --}}
                    <div class="imp-note-box">
                        <i class="bi bi-lightbulb-fill imp-note-icon"></i>
                        <div class="imp-note-text">
                            <strong>Auto-approved:</strong>
                            All imported players are immediately approved.<br>
                            <strong>Player IDs</strong> are auto-generated
                            (PX1001, PX1002…).<br>
                            <strong>Column names</strong> are case-insensitive
                            — "player name" or "Player Name" both work.
                        </div>
                    </div>

                </div>
            </div>

            {{-- Drive Help Card --}}
            {{-- Drive Help Card --}}
            <div class="imp-card imp-drive-card">
                <div class="imp-card-header">
                    <i class="bi bi-google"></i>
                    How to get a Google Drive Photo Link
                </div>
                <div class="imp-card-body">

                    {{-- Warning box --}}
                    <div
                        style="background:#fef2f2;border:1px solid #fecaca;
                    border-radius:8px;padding:12px 14px;margin-bottom:16px;">
                        <div
                            style="font-size:0.78rem;font-weight:700;
                        color:#991b1b;margin-bottom:4px;">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            Files must be set to Public
                        </div>
                        <div style="font-size:0.75rem;color:#b91c1c;line-height:1.6;">
                            Private Drive files will <strong>not display</strong>
                            as player photos. Follow the steps below to make
                            each photo publicly accessible.
                        </div>
                    </div>

                    <ol class="imp-drive-steps">
                        <li>
                            Open <strong>Google Drive</strong> and locate
                            the player photo
                        </li>
                        <li>
                            <strong>Right-click</strong> the photo file
                            → click <strong>Share</strong>
                        </li>
                        <li>
                            Under <strong>"General access"</strong>
                            → click the dropdown
                            → select <strong>"Anyone with the link"</strong>
                        </li>
                        <li>
                            Make sure the role is set to
                            <strong>Viewer</strong>
                        </li>
                        <li>
                            Click <strong>Copy link</strong>
                            → then click <strong>Done</strong>
                        </li>
                        <li>
                            Paste the copied link in the
                            <strong>Photo</strong> column of your CSV
                        </li>
                    </ol>

                    {{-- Visual indicator --}}
                    <div
                        style="background:#f0fdf4;border:1px solid #bbf7d0;
                    border-radius:8px;padding:10px 14px;margin-bottom:16px;">
                        <div
                            style="font-size:0.75rem;font-weight:700;
                        color:#166534;margin-bottom:3px;">
                            <i class="bi bi-check-circle-fill me-1"></i>
                            Correct sharing setting
                        </div>
                        <div style="font-size:0.72rem;color:#15803d;line-height:1.5;">
                            General access →
                            <strong>"Anyone with the link"</strong>
                            → Viewer ✓
                        </div>
                    </div>

                    <div class="imp-drive-example">
                        <div class="imp-drive-example-label">
                            Paste this type of link in your CSV:
                        </div>
                        <code class="imp-drive-example-url">
                            https://drive.google.com/file/d/<span style="color:#2563eb;">FILE_ID</span>/view?usp=sharing
                        </code>
                        <div class="imp-drive-example-label" style="margin-top:10px;">
                            System automatically converts it to:
                        </div>
                        <code class="imp-drive-example-url">
                            https://lh3.googleusercontent.com/d/<span style="color:#16a34a;">FILE_ID</span>=w200
                        </code>
                    </div>

                </div>
            </div>

        </div>

        {{-- ════════════════════════════
         RIGHT — UPLOAD FORM
         ════════════════════════════ --}}
        <div class="imp-upload-col">

            {{-- Upload Card --}}
            <div class="imp-card">
                <div class="imp-card-header">
                    <i class="bi bi-upload"></i>
                    Upload CSV File
                </div>
                <div class="imp-card-body">
                    <form method="POST" action="{{ route('player.importCsv', $tournament->id) }}"
                        enctype="multipart/form-data" id="importForm">
                        @csrf

                        {{-- Drop Zone --}}
                        <div class="imp-dropzone" id="dropZone">
                            <input type="file" name="csv_file" id="csvFile" accept=".csv,.txt"
                                class="imp-file-input" required>

                            {{-- Default state --}}
                            <div class="imp-dropzone-content" id="dzDefault">
                                <div class="imp-dz-icon">
                                    <i class="bi bi-file-earmark-spreadsheet"></i>
                                </div>
                                <div class="imp-dz-title">
                                    Drop your CSV file here
                                </div>
                                <div class="imp-dz-sub">
                                    or click to browse
                                </div>
                                <div class="imp-dz-formats">
                                    Accepted: .csv &nbsp;·&nbsp; .txt
                                </div>
                            </div>

                            {{-- File selected state --}}
                            <div class="imp-dropzone-content" id="dzSelected" style="display:none;">
                                <div class="imp-dz-icon imp-dz-icon-success">
                                    <i class="bi bi-file-earmark-check"></i>
                                </div>
                                <div class="imp-dz-filename" id="dzFileName">—</div>
                                <div class="imp-dz-filesize" id="dzFileSize">—</div>
                                <button type="button" class="imp-change-btn" id="btnChangeFile">
                                    <i class="bi bi-arrow-repeat"></i>
                                    Change File
                                </button>
                            </div>
                        </div>

                        {{-- CSV Preview --}}
                        <div id="previewWrap" style="display:none;">
                            <div class="imp-preview-header">
                                <i class="bi bi-table"></i>
                                Preview — first 3 data rows
                            </div>
                            <div class="imp-preview-table-wrap">
                                <table class="imp-preview-table" id="previewTable">
                                </table>
                            </div>

                            {{-- Column mapping status --}}
                            <div class="imp-col-status" id="colStatus"></div>
                        </div>

                        {{-- Submit --}}
                        <button type="submit" class="imp-submit-btn" id="submitBtn" disabled>
                            <i class="bi bi-upload"></i>
                            Import Players
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

@endsection

@push('styles')
    <style>
        /* ══════════════════════════════════════
       IMPORT PAGE — DESIGN SYSTEM
       Tone: Utilitarian / clean industrial
       Font: DM Mono + DM Sans
       Palette: Slate + Blue + Green accents
       ══════════════════════════════════════ */

        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap');

        :root {
            --imp-blue: #1d4ed8;
            --imp-blue-lt: #3b82f6;
            --imp-blue-pale: #eff6ff;
            --imp-green: #15803d;
            --imp-green-lt: #16a34a;
            --imp-red: #dc2626;
            --imp-red-pale: #fef2f2;
            --imp-amber: #d97706;
            --imp-amber-pale: #fffbeb;
            --imp-slate: #334155;
            --imp-muted: #94a3b8;
            --imp-border: #e2e8f0;
            --imp-bg: #f8fafc;
            --imp-white: #ffffff;
            --imp-dark: #0f172a;
            --imp-radius: 10px;
            --imp-shadow: 0 1px 3px rgba(0, 0, 0, 0.07),
                0 1px 2px rgba(0, 0, 0, 0.04);
        }

        /* ── HEADER ── */
        .imp-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 14px;
            margin-top: 10px;
        }

        .imp-title {
            font-family: 'DM Sans', sans-serif;
            font-weight: 700;
            font-size: 1.45rem;
            color: var(--imp-dark);
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0 0 4px;
        }

        .imp-title-icon {
            font-size: 1.3rem;
        }

        .imp-subtitle {
            font-size: 0.875rem;
            color: var(--imp-muted);
            margin: 0;
        }

        .imp-back-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            background: var(--imp-white);
            border: 1.5px solid var(--imp-border);
            border-radius: var(--imp-radius);
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--imp-slate);
            text-decoration: none;
            transition: all 0.15s;
        }

        .imp-back-btn:hover {
            background: var(--imp-dark);
            border-color: var(--imp-dark);
            color: #fff;
        }

        /* ── ALERTS ── */
        .imp-alert {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 13px 18px;
            border-radius: var(--imp-radius);
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 20px;
        }

        .imp-alert-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
        }

        .imp-alert-error {
            background: var(--imp-red-pale);
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        /* ── MAIN GRID ── */
        .imp-grid {
            display: grid;
            grid-template-columns: 400px 1fr;
            gap: 20px;
            align-items: start;
        }

        /* ── CARD ── */
        .imp-card {
            background: var(--imp-white);
            border: 1px solid var(--imp-border);
            border-radius: 14px;
            box-shadow: var(--imp-shadow);
            overflow: hidden;
            margin-bottom: 16px;
        }

        .imp-card:last-child {
            margin-bottom: 0;
        }

        .imp-card-header {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 14px 20px;
            border-bottom: 1px solid var(--imp-border);
            background: #f8fafc;
            font-weight: 700;
            font-size: 0.85rem;
            color: var(--imp-dark);
        }

        .imp-card-header i {
            color: var(--imp-blue-lt);
        }

        .imp-card-body {
            padding: 22px;
        }

        /* ── COLUMN SECTIONS ── */
        .imp-col-section {
            margin-bottom: 22px;
        }

        .imp-col-section:last-child {
            margin-bottom: 0;
        }

        .imp-col-section-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 12px;
        }

        .imp-required-title {
            color: var(--imp-red);
        }

        .imp-optional-title {
            color: var(--imp-green-lt);
        }

        .imp-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .imp-dot-red {
            background: var(--imp-red);
        }

        .imp-dot-green {
            background: var(--imp-green-lt);
        }

        .imp-col-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .imp-col-item {
            background: #f8fafc;
            border: 1px solid var(--imp-border);
            border-radius: 8px;
            padding: 10px 14px;
        }

        .imp-col-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 3px;
        }

        .imp-col-name {
            font-family: 'DM Mono', monospace;
            font-size: 0.82rem;
            font-weight: 500;
            color: var(--imp-blue);
            background: var(--imp-blue-pale);
            padding: 1px 7px;
            border-radius: 4px;
        }

        .imp-col-desc {
            font-size: 0.76rem;
            color: var(--imp-muted);
            line-height: 1.4;
        }

        .imp-drive-tip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 0.7rem;
            color: var(--imp-blue-lt);
            font-weight: 600;
            margin-top: 5px;
        }

        /* ── BADGES ── */
        .imp-badge {
            font-size: 0.62rem;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 4px;
            letter-spacing: 0.3px;
            flex-shrink: 0;
        }

        .imp-badge-req {
            background: #fee2e2;
            color: #991b1b;
        }

        .imp-badge-opt {
            background: #dcfce7;
            color: #166534;
        }

        /* ── OPTIONAL TAGS ── */
        .imp-optional-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .imp-opt-tag {
            font-family: 'DM Mono', monospace;
            font-size: 0.72rem;
            background: var(--imp-bg);
            border: 1px solid var(--imp-border);
            padding: 3px 10px;
            border-radius: 4px;
            color: var(--imp-slate);
        }

        /* ── SAMPLE CSV BOX ── */
        .imp-sample-box {
            background: #0f172a;
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 14px;
        }

        .imp-sample-title {
            font-size: 0.7rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.4);
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .imp-sample-code {
            font-family: 'DM Mono', monospace;
            font-size: 0.72rem;
            color: #7dd3fc;
            margin: 0;
            white-space: pre-wrap;
            word-break: break-all;
            line-height: 1.7;
        }

        /* ── NOTE BOX ── */
        .imp-note-box {
            display: flex;
            gap: 10px;
            background: var(--imp-amber-pale);
            border: 1px solid #fcd34d;
            border-radius: 8px;
            padding: 12px 14px;
        }

        .imp-note-icon {
            color: var(--imp-amber);
            font-size: 1rem;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .imp-note-text {
            font-size: 0.78rem;
            color: #92400e;
            line-height: 1.6;
        }

        /* ── DRIVE CARD ── */
        .imp-drive-card .imp-card-header i {
            color: #4285F4;
        }

        .imp-drive-steps {
            padding-left: 20px;
            margin: 0 0 16px;
        }

        .imp-drive-steps li {
            font-size: 0.82rem;
            color: var(--imp-slate);
            margin-bottom: 6px;
            line-height: 1.5;
        }

        .imp-drive-example {
            background: #f8fafc;
            border: 1px solid var(--imp-border);
            border-radius: 8px;
            padding: 12px 14px;
        }

        .imp-drive-example-label {
            font-size: 0.68rem;
            font-weight: 600;
            color: var(--imp-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .imp-drive-example-url {
            font-family: 'DM Mono', monospace;
            font-size: 0.7rem;
            color: var(--imp-dark);
            word-break: break-all;
            display: block;
        }

        /* ── DROP ZONE ── */
        .imp-dropzone {
            position: relative;
            border: 2px dashed var(--imp-border);
            border-radius: 12px;
            padding: 40px 24px;
            text-align: center;
            cursor: pointer;
            transition: border-color 0.2s, background 0.2s;
            background: #f8fafc;
            margin-bottom: 20px;
        }

        .imp-dropzone:hover,
        .imp-dropzone.imp-dz-dragover {
            border-color: var(--imp-blue-lt);
            background: var(--imp-blue-pale);
        }

        .imp-dropzone.imp-dz-has-file {
            border-color: var(--imp-green-lt);
            background: #f0fdf4;
            border-style: solid;
        }

        .imp-file-input {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
            z-index: 2;
        }

        .imp-dropzone-content {
            pointer-events: none;
            position: relative;
            z-index: 1;
        }

        .imp-dz-icon {
            font-size: 2.2rem;
            color: var(--imp-muted);
            margin-bottom: 10px;
            display: block;
        }

        .imp-dz-icon-success {
            color: var(--imp-green-lt);
        }

        .imp-dz-title {
            font-weight: 700;
            font-size: 0.95rem;
            color: var(--imp-dark);
            margin-bottom: 5px;
        }

        .imp-dz-sub {
            font-size: 0.82rem;
            color: var(--imp-muted);
            margin-bottom: 8px;
        }

        .imp-dz-formats {
            font-size: 0.72rem;
            color: var(--imp-muted);
            background: var(--imp-white);
            border: 1px solid var(--imp-border);
            display: inline-block;
            padding: 3px 12px;
            border-radius: 20px;
        }

        .imp-dz-filename {
            font-family: 'DM Mono', monospace;
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--imp-dark);
            margin-bottom: 4px;
            word-break: break-all;
        }

        .imp-dz-filesize {
            font-size: 0.78rem;
            color: var(--imp-muted);
            margin-bottom: 12px;
        }

        .imp-change-btn {
            pointer-events: all;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 14px;
            background: var(--imp-white);
            border: 1.5px solid var(--imp-border);
            border-radius: 7px;
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--imp-slate);
            cursor: pointer;
            transition: all 0.15s;
            z-index: 3;
            position: relative;
        }

        .imp-change-btn:hover {
            background: var(--imp-dark);
            border-color: var(--imp-dark);
            color: #fff;
        }

        /* ── CSV PREVIEW ── */
        .imp-preview-header {
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--imp-slate);
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .imp-preview-table-wrap {
            border: 1px solid var(--imp-border);
            border-radius: 8px;
            overflow: auto;
            margin-bottom: 12px;
            max-height: 200px;
        }

        .imp-preview-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.75rem;
        }

        .imp-preview-table thead th {
            background: #0f172a;
            color: #7dd3fc;
            font-family: 'DM Mono', monospace;
            font-weight: 500;
            padding: 8px 12px;
            text-align: left;
            white-space: nowrap;
            border: none;
        }

        .imp-preview-table tbody td {
            padding: 7px 12px;
            border-bottom: 1px solid var(--imp-border);
            color: var(--imp-slate);
            white-space: nowrap;
            vertical-align: middle;
        }

        .imp-preview-table tbody tr:last-child td {
            border-bottom: none;
        }

        .imp-preview-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        /* ── COLUMN STATUS ── */
        .imp-col-status {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 16px;
        }

        .imp-col-found {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 0.7rem;
            font-weight: 600;
            padding: 3px 9px;
            border-radius: 4px;
        }

        .imp-col-found-yes {
            background: #dcfce7;
            color: #166534;
        }

        .imp-col-found-no {
            background: #fee2e2;
            color: #991b1b;
        }

        .imp-col-found-opt {
            background: #f1f5f9;
            color: #64748b;
        }

        /* ── SUBMIT BUTTON ── */
        .imp-submit-btn {
            width: 100%;
            padding: 13px;
            background: var(--imp-blue);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background 0.15s, transform 0.1s;
            font-family: 'DM Sans', sans-serif;
        }

        .imp-submit-btn:hover:not(:disabled) {
            background: #1e40af;
        }

        .imp-submit-btn:active:not(:disabled) {
            transform: scale(0.99);
        }

        .imp-submit-btn:disabled {
            background: #cbd5e1;
            cursor: not-allowed;
            color: #94a3b8;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 1024px) {
            .imp-grid {
                grid-template-columns: 1fr;
            }

            .imp-instructions-col {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 16px;
            }

            .imp-instructions-col .imp-card {
                margin-bottom: 0;
            }
        }

        @media (max-width: 640px) {
            .imp-instructions-col {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        'use strict';

        /* ══════════════════════════════════
           IMPORT PAGE SCRIPT
           ══════════════════════════════════ */

        const dropZone = document.getElementById('dropZone');
        const csvFileInput = document.getElementById('csvFile');
        const dzDefault = document.getElementById('dzDefault');
        const dzSelected = document.getElementById('dzSelected');
        const dzFileName = document.getElementById('dzFileName');
        const dzFileSize = document.getElementById('dzFileSize');
        const submitBtn = document.getElementById('submitBtn');
        const previewWrap = document.getElementById('previewWrap');
        const previewTable = document.getElementById('previewTable');
        const colStatus = document.getElementById('colStatus');
        const btnChangeFile = document.getElementById('btnChangeFile');

        // Required and optional column names to detect
        const REQUIRED_COLS = ['player name', 'role', 'mobile'];
        const OPTIONAL_COLS = [
            'photo', 'email', 'age', 'city',
            'batting style', 'bowling style',
            'experience', 'jersey number', 'base price'
        ];

        // ── Drag & Drop ──
        dropZone.addEventListener('dragover', e => {
            e.preventDefault();
            dropZone.classList.add('imp-dz-dragover');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('imp-dz-dragover');
        });

        dropZone.addEventListener('drop', e => {
            e.preventDefault();
            dropZone.classList.remove('imp-dz-dragover');
            const files = e.dataTransfer.files;
            if (files && files[0]) {
                // Create a DataTransfer to set files on input
                const dt = new DataTransfer();
                dt.items.add(files[0]);
                csvFileInput.files = dt.files;
                processFile(files[0]);
            }
        });

        // ── File Input Change ──
        csvFileInput.addEventListener('change', () => {
            if (csvFileInput.files && csvFileInput.files[0]) {
                processFile(csvFileInput.files[0]);
            }
        });

        // ── Change File Button ──
        btnChangeFile.addEventListener('click', e => {
            e.stopPropagation();
            csvFileInput.value = '';
            resetDropzone();
        });

        // ── Process selected file ──
        function processFile(file) {
            // Update UI
            dzDefault.style.display = 'none';
            dzSelected.style.display = 'block';
            dropZone.classList.add('imp-dz-has-file');
            dropZone.classList.remove('imp-dz-dragover');

            dzFileName.textContent = file.name;
            dzFileSize.textContent = formatSize(file.size);

            submitBtn.disabled = false;

            // Read and preview
            const reader = new FileReader();
            reader.onload = e => {
                parseAndPreview(e.target.result);
            };
            reader.readAsText(file);
        }

        // ── Reset dropzone to default state ──
        function resetDropzone() {
            dzDefault.style.display = 'block';
            dzSelected.style.display = 'none';
            dropZone.classList.remove('imp-dz-has-file', 'imp-dz-dragover');
            submitBtn.disabled = true;
            previewWrap.style.display = 'none';
            previewTable.innerHTML = '';
            colStatus.innerHTML = '';
        }

        // ── Parse CSV and render preview ──
        function parseAndPreview(csvText) {
            // Split into lines, remove empty trailing lines
            const allLines = csvText.split(/\r?\n/).filter(l => l.trim() !== '');

            if (allLines.length === 0) return;

            // Parse header
            const headerLine = allLines[0];
            const headers = parseCSVRow(headerLine);
            const normHeaders = headers.map(h => h.toLowerCase().trim());

            // Parse up to 3 data rows for preview
            const dataRows = allLines.slice(1, 4).map(line => parseCSVRow(line));

            // Build preview table
            let tableHTML = '<thead><tr>';
            headers.forEach(h => {
                tableHTML += `<th>${escapeHtml(h)}</th>`;
            });
            tableHTML += '</tr></thead><tbody>';

            dataRows.forEach(row => {
                tableHTML += '<tr>';
                headers.forEach((_, i) => {
                    const cell = row[i] || '';
                    // Check if it looks like a Drive link
                    const isDrive = cell.includes('drive.google.com');
                    tableHTML += `<td title="${escapeHtml(cell)}">`;
                    if (isDrive) {
                        tableHTML += `<span style="color:#2563eb;font-size:0.68rem;">
                    <i class="bi bi-google"></i> Drive Link
                </span>`;
                    } else {
                        tableHTML += escapeHtml(
                            cell.length > 30 ? cell.substring(0, 28) + '…' : cell
                        );
                    }
                    tableHTML += '</td>';
                });
                tableHTML += '</tr>';
            });

            tableHTML += '</tbody>';
            previewTable.innerHTML = tableHTML;

            // Render column detection status
            let statusHTML =
                '<div style="font-size:0.7rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;width:100%;">Column Detection</div>';

            REQUIRED_COLS.forEach(col => {
                const found = normHeaders.some(h =>
                    h === col || h.replace(/[\s_]/g, '') === col.replace(/[\s_]/g, '')
                );
                statusHTML += `
            <span class="imp-col-found ${found ? 'imp-col-found-yes' : 'imp-col-found-no'}">
                ${found ? '<i class="bi bi-check-circle-fill"></i>' : '<i class="bi bi-x-circle-fill"></i>'}
                ${col}
            </span>`;
            });

            // Check for photo column
            const photoFound = normHeaders.some(h => ['photo', 'image url', 'image_url', 'image', 'drive link', 'photo url']
                .includes(h)
            );
            statusHTML += `
        <span class="imp-col-found ${photoFound ? 'imp-col-found-yes' : 'imp-col-found-opt'}">
            ${photoFound
                ? '<i class="bi bi-check-circle-fill"></i>'
                : '<i class="bi bi-dash-circle"></i>'}
            photo / drive link
        </span>`;

            colStatus.innerHTML = statusHTML;
            previewWrap.style.display = 'block';
        }

        // ── Simple CSV row parser (handles quoted fields) ──
        function parseCSVRow(line) {
            const result = [];
            let current = '';
            let inQuotes = false;

            for (let i = 0; i < line.length; i++) {
                const ch = line[i];
                if (ch === '"') {
                    if (inQuotes && line[i + 1] === '"') {
                        current += '"';
                        i++;
                    } else {
                        inQuotes = !inQuotes;
                    }
                } else if (ch === ',' && !inQuotes) {
                    result.push(current.trim());
                    current = '';
                } else {
                    current += ch;
                }
            }
            result.push(current.trim());
            return result;
        }

        // ── Helpers ──
        function formatSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
        }

        function escapeHtml(str) {
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }
    </script>
@endpush
