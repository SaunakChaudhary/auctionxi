@extends('layouts.app')

@section('title', 'Import Players')
@section('page-title', 'Import Players')

@section('content')

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
                    <a href="{{ route('player.index', $tournament->id) }}">
                        Players
                    </a>
                </li>
                <li class="breadcrumb-item active">Import CSV</li>
            </ol>
        </nav>
        <h1 class="mt-2">Import Players via CSV</h1>
        <p>Bulk add players to {{ $tournament->name }}</p>
    </div>

    <div class="row g-4">

        {{-- Instructions --}}
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-info-circle-fill text-primary"></i>
                    CSV Format Instructions
                </div>
                <div class="card-body">
                    <p style="font-size:0.875rem;color:#6b7280;margin-bottom:20px;">
                        Your CSV file must include the following columns
                        in the first row (header row):
                    </p>

                    {{-- Required Columns --}}
                    <div class="mb-4">
                        <div
                            style="font-size:0.75rem;font-weight:700;
                                color:#374151;text-transform:uppercase;
                                letter-spacing:1px;margin-bottom:10px;">
                            Required Columns
                        </div>
                        @foreach ([['Player Name', 'Full name of the player'], ['Role', 'Batsman / Bowler / All Rounder / Wicket Keeper'], ['Mobile', 'Player mobile number']] as $col)
                            <div class="d-flex align-items-start gap-2 mb-2">
                                <span
                                    style="background:#fee2e2;color:#991b1b;
                                     padding:2px 8px;border-radius:5px;
                                     font-size:0.72rem;font-weight:700;
                                     white-space:nowrap;">
                                    Required
                                </span>
                                <div>
                                    <code style="color:#6c3fc5;font-size:0.82rem;">
                                        {{ $col[0] }}
                                    </code>
                                    <div style="font-size:0.78rem;color:#9ca3af;">
                                        {{ $col[1] }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Optional Columns --}}
                    <div class="mb-4">
                        <div
                            style="font-size:0.75rem;font-weight:700;
                                color:#374151;text-transform:uppercase;
                                letter-spacing:1px;margin-bottom:10px;">
                            Optional Columns
                        </div>
                        @foreach ([['Email', 'Player email address'], ['Age', 'Player age (number)'], ['City', 'Player city'], ['Batting Style', 'Right Hand / Left Hand'], ['Bowling Style', 'e.g. Right Arm Fast'], ['Experience', 'Beginner / Intermediate / etc'], ['Jersey Number', 'Player jersey number'], ['Base Price', 'Starting bid price (number)']] as $col)
                            <div class="d-flex align-items-start gap-2 mb-2">
                                <span
                                    style="background:#d1fae5;color:#065f46;
                                     padding:2px 8px;border-radius:5px;
                                     font-size:0.72rem;font-weight:700;
                                     white-space:nowrap;">
                                    Optional
                                </span>
                                <div>
                                    <code style="color:#059669;font-size:0.82rem;">
                                        {{ $col[0] }}
                                    </code>
                                    <div style="font-size:0.78rem;color:#9ca3af;">
                                        {{ $col[1] }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Sample --}}
                    <div class="p-3 rounded-3"
                        style="background:#f8f9ff;
                            border:1.5px solid #e8eaf0;
                            overflow-x:auto;">
                        <div
                            style="font-size:0.72rem;font-weight:700;
                                color:#374151;text-transform:uppercase;
                                letter-spacing:1px;margin-bottom:8px;">
                            Sample CSV Row
                        </div>
                        <code style="font-size:0.72rem;color:#374151;
                                 white-space:nowrap;">
                            Player Name,Role,Mobile,Email<br>
                            Rohit Sharma,Batsman,9876543210,rohit@email.com<br>
                            Jasprit Bumrah,Bowler,9876543211,
                        </code>
                    </div>

                    {{-- Notes --}}
                    <div class="mt-3 p-3 rounded-3"
                        style="background:linear-gradient(135deg,
                            rgba(108,63,197,0.05),
                            rgba(139,92,246,0.08));
                            border:1.5px solid rgba(108,63,197,0.15);">
                        <div style="font-size:0.78rem;color:#6b7280;">
                            <i class="bi bi-lightbulb-fill me-1" style="color:#f59e0b;"></i>
                            <strong>Note:</strong> Imported players are
                            automatically approved. Player IDs
                            (PX1001, PX1002...) are auto-generated.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Upload Form --}}
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-upload text-success"></i>
                    Upload CSV File
                </div>
                <div class="card-body">
                    <form method="POST"
                        action="{{ route('player.importCsv', $tournament->id) }}"
                        enctype="multipart/form-data" id="importForm">
                        @csrf

                        {{-- Drop Zone --}}
                        <div class="drop-zone mb-4" id="dropZone"
                            onclick="document.getElementById(
                             'csvFile').click()">
                            <div id="dropZoneContent">
                                <div style="font-size:3rem;margin-bottom:12px;">
                                    📄
                                </div>
                                <div
                                    style="font-weight:700;font-size:1rem;
                                        color:#374151;margin-bottom:6px;">
                                    Click to upload CSV file
                                </div>
                                <div style="font-size:0.82rem;color:#9ca3af;">
                                    or drag and drop here
                                </div>
                                <div
                                    style="font-size:0.75rem;color:#9ca3af;
                                        margin-top:8px;">
                                    Supported: .csv, .txt
                                </div>
                            </div>
                            <div id="fileSelected" style="display:none;">
                                <div style="font-size:2rem;
                                        margin-bottom:10px;">✅
                                </div>
                                <div style="font-weight:700;color:#374151;" id="fileName"></div>
                                <div style="font-size:0.78rem;color:#9ca3af;" id="fileSize"></div>
                            </div>
                            <input type="file" name="csv_file" id="csvFile" accept=".csv,.txt" style="display:none;"
                                onchange="handleFile(this)" required>
                        </div>

                        @error('csv_file')
                            <div class="alert alert-danger mb-3">
                                {{ $message }}
                            </div>
                        @enderror

                        {{-- Preview Table --}}
                        <div id="previewSection" style="display:none;">
                            <div
                                style="font-weight:700;font-size:0.875rem;
                                    margin-bottom:10px;color:#374151;">
                                <i class="bi bi-table me-1"></i>
                                CSV Preview (first 3 rows)
                            </div>
                            <div class="table-responsive mb-4"
                                style="border:1.5px solid #e8eaf0;
                                    border-radius:12px;overflow:hidden;">
                                <table class="table table-sm mb-0" id="previewTable">
                                </table>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100" id="submitBtn" disabled>
                            <i class="bi bi-upload me-2"></i>
                            Import Players
                        </button>
                    </form>
                </div>
            </div>

            {{-- Google Form Alternative --}}
            <div class="card mt-4">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-google text-danger"></i>
                    Alternative: Google Form
                </div>
                <div class="card-body">
                    <p style="font-size:0.875rem;color:#6b7280;
                          margin-bottom:16px;">
                        You can also collect player registrations through a
                        Google Form and share the link below with players.
                    </p>
                    <div class="d-flex gap-2">
                        <input type="url" class="form-control" placeholder="Paste your Google Form link here..."
                            id="googleFormLink">
                        <button onclick="copyGoogleLink()" class="btn btn-danger" style="white-space:nowrap;">
                            <i class="bi bi-copy me-1"></i>Copy
                        </button>
                    </div>
                    <div style="font-size:0.75rem;color:#9ca3af;
                            margin-top:8px;">
                        Share this Google Form link as an alternative
                        registration method
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <style>
        .drop-zone {
            border: 2.5px dashed #d1d5db;
            border-radius: 16px;
            padding: 48px 24px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            background: #fafbff;
        }

        .drop-zone:hover,
        .drop-zone.dragover {
            border-color: #6c3fc5;
            background: rgba(108, 63, 197, 0.03);
        }

        .drop-zone.has-file {
            border-color: #10b981;
            background: rgba(16, 185, 129, 0.03);
        }
    </style>
@endpush

@push('scripts')
    <script>
        const dropZone = document.getElementById('dropZone');

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('dragover');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('dragover');
            const file = e.dataTransfer.files[0];
            if (file) {
                document.getElementById('csvFile').files = e.dataTransfer.files;
                handleFile(document.getElementById('csvFile'));
            }
        });

        function handleFile(input) {
            if (!input.files || !input.files[0]) return;
            const file = input.files[0];

            document.getElementById('dropZoneContent').style.display = 'none';
            document.getElementById('fileSelected').style.display = 'block';
            document.getElementById('fileName').textContent = file.name;
            document.getElementById('fileSize').textContent =
                (file.size / 1024).toFixed(1) + ' KB';

            dropZone.classList.add('has-file');
            document.getElementById('submitBtn').disabled = false;

            // CSV Preview
            const reader = new FileReader();
            reader.onload = (e) => {
                const lines = e.target.result.split('\n').slice(0, 4);
                const header = lines[0].split(',');
                const rows = lines.slice(1);

                let html = '<thead><tr>' +
                    header.map(h =>
                        `<th style="font-size:0.72rem;">${h.trim()}</th>`
                    ).join('') +
                    '</tr></thead><tbody>';

                rows.forEach(row => {
                    if (row.trim()) {
                        html += '<tr>' +
                            row.split(',').map(cell =>
                                `<td style="font-size:0.78rem;">
                             ${cell.trim()}
                         </td>`
                            ).join('') +
                            '</tr>';
                    }
                });

                html += '</tbody>';
                document.getElementById('previewTable').innerHTML = html;
                document.getElementById('previewSection').style.display = 'block';
            };
            reader.readAsText(file);
        }

        function copyGoogleLink() {
            const link = document.getElementById('googleFormLink').value;
            if (!link) return;
            navigator.clipboard.writeText(link).then(() => {
                alert('Google Form link copied!');
            });
        }
    </script>
@endpush
