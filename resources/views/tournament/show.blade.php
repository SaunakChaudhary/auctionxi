@extends('layouts.app')

@section('title', $tournament->name)
@section('page-title', 'Manage Tournament')

@section('content')

    <div class="page-header">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('tournament.index') }}">Tournaments</a>
                </li>
                <li class="breadcrumb-item active">
                    {{ $tournament->name }}
                </li>
            </ol>
        </nav>
    </div>

    {{-- Tournament Header --}}
    <div class="card mb-4"
        style="background:linear-gradient(135deg,#6c3fc5,#8b5cf6);
            border:none;overflow:hidden;position:relative;">
        <div
            style="position:absolute;top:-30px;right:-30px;
                width:150px;height:150px;border-radius:50%;
                background:rgba(255,255,255,0.06);">
        </div>
        <div
            style="position:absolute;bottom:-40px;right:80px;
                width:100px;height:100px;border-radius:50%;
                background:rgba(255,255,255,0.04);">
        </div>
        <div class="card-body p-4 position-relative">
            <div class="row align-items-center g-3">
                <div class="col-lg-7">
                    <div style="font-size:2.2rem;margin-bottom:10px;">🏆</div>
                    <h2
                        style="color:#fff;font-family:'Poppins',sans-serif;
                           font-weight:800;margin-bottom:8px;">
                        {{ $tournament->name }}
                    </h2>
                    <div class="d-flex flex-wrap gap-3">
                        @if ($tournament->location)
                            <span style="color:rgba(255,255,255,0.7);font-size:0.85rem;">
                                <i class="bi bi-geo-alt-fill me-1"></i>
                                {{ $tournament->location }}
                            </span>
                        @endif
                        @if ($tournament->auction_date)
                            <span style="color:rgba(255,255,255,0.7);font-size:0.85rem;">
                                <i class="bi bi-calendar3 me-1"></i>
                                Auction:
                                {{ \Carbon\Carbon::parse($tournament->auction_date)->format('d M Y') }}
                            </span>
                        @endif
                        <span
                            style="background:rgba(255,255,255,0.15);
                                 color:#fff;padding:3px 10px;
                                 border-radius:6px;font-size:0.78rem;
                                 font-weight:700;letter-spacing:1px;">
                            {{ $tournament->code }}
                        </span>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="d-flex gap-2 flex-wrap justify-content-lg-end">
                        <a href="{{ route('tournament.edit', $tournament->id) }}" class="btn btn-sm"
                            style="background:rgba(255,255,255,0.15);
                              color:#fff;border:1px solid rgba(255,255,255,0.25);
                              border-radius:10px;font-weight:600;">
                            <i class="bi bi-pencil-fill me-1"></i>Edit
                        </a>
                        <form method="POST"
                            action="{{ route('tournament.toggleRegistration', $tournament->id) }}">
                            @csrf
                            <button type="submit" class="btn btn-sm"
                                style="background:rgba(255,255,255,0.15);
                                       color:#fff;
                                       border:1px solid rgba(255,255,255,0.25);
                                       border-radius:10px;font-weight:600;">
                                @if ($tournament->registration_status === 'open')
                                    <i class="bi bi-lock-fill me-1"></i>Close Reg.
                                @else
                                    <i class="bi bi-unlock-fill me-1"></i>Open Reg.
                                @endif
                            </button>
                        </form>
                        <a href="{{ route('public.live', $tournament->code) }}" target="_blank" class="btn btn-sm"
                            style="background:rgba(16,185,129,0.3);
                              color:#fff;
                              border:1px solid rgba(16,185,129,0.4);
                              border-radius:10px;font-weight:600;">
                            <i class="bi bi-eye-fill me-1"></i>Live View
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card text-center p-3">
                <div
                    style="font-size:1.8rem;font-weight:800;
                        font-family:'Poppins',sans-serif;
                        color:#6c3fc5;">
                    {{ $tournament->teams->count() }}
                    <small style="font-size:0.9rem;color:#9ca3af;">
                        /{{ $tournament->number_of_teams }}
                    </small>
                </div>
                <div style="font-size:0.78rem;color:#9ca3af;font-weight:500;">
                    Teams Created
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center p-3">
                <div
                    style="font-size:1.8rem;font-weight:800;
                        font-family:'Poppins',sans-serif;color:#d97706;">
                    {{ $tournament->players->count() }}
                </div>
                <div style="font-size:0.78rem;color:#9ca3af;font-weight:500;">
                    Total Players
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center p-3">
                <div
                    style="font-size:1.8rem;font-weight:800;
                        font-family:'Poppins',sans-serif;color:#059669;">
                    {{ $tournament->players->where('status', 'approved')->count() }}
                </div>
                <div style="font-size:0.78rem;color:#9ca3af;font-weight:500;">
                    Approved Players
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center p-3">
                <div
                    style="font-size:1.8rem;font-weight:800;
                        font-family:'Poppins',sans-serif;color:#dc2626;">
                    {{ $tournament->players->where('status', 'sold')->count() }}
                </div>
                <div style="font-size:0.78rem;color:#9ca3af;font-weight:500;">
                    Players Sold
                </div>
            </div>
        </div>
    </div>

    {{-- Public Links --}}
    <div class="card mb-4">
        <div class="card-header d-flex align-items-center gap-2">
            <i class="bi bi-link-45deg text-primary"></i>
            Public Links
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="p-3 rounded-3"
                        style="background:#f8f9ff;
                            border:1.5px solid rgba(108,63,197,0.2);">
                        <div
                            style="font-size:0.72rem;font-weight:700;
                                color:#6c3fc5;text-transform:uppercase;
                                letter-spacing:1px;margin-bottom:8px;">
                            <i class="bi bi-person-plus-fill me-1"></i>
                            Player Registration Link
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <input type="text" class="form-control form-control-sm"
                                value="{{ route('public.player.register', $tournament->code) }}"
                                readonly id="regLink"
                                style="font-size:0.78rem;
                                      background:#fff;">
                            <button onclick="copyById('regLink', this)" class="btn btn-sm btn-primary"
                                style="white-space:nowrap;">
                                <i class="bi bi-copy me-1"></i>Copy
                            </button>
                        </div>
                        <div style="font-size:0.72rem;color:#9ca3af;margin-top:6px;">
                            Share this with players to register
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 rounded-3"
                        style="background:#f8f9ff;
                            border:1.5px solid rgba(5,150,105,0.2);">
                        <div
                            style="font-size:0.72rem;font-weight:700;
                                color:#059669;text-transform:uppercase;
                                letter-spacing:1px;margin-bottom:8px;">
                            <i class="bi bi-broadcast me-1"></i>
                            Live Viewer Link
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <input type="text" class="form-control form-control-sm"
                                value="{{ route('public.live', $tournament->code) }}"
                                readonly id="liveLink"
                                style="font-size:0.78rem;
                                      background:#fff;">
                            <button onclick="copyById('liveLink', this)" class="btn btn-sm btn-success"
                                style="white-space:nowrap;">
                                <i class="bi bi-copy me-1"></i>Copy
                            </button>
                        </div>
                        <div style="font-size:0.72rem;color:#9ca3af;margin-top:6px;">
                            Share this with viewers for live updates
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Management Cards --}}
    <div class="row g-3">
        <div class="col-md-6 col-lg-3">
            <a href="{{ route('team.index', $tournament->id) }}" class="card manage-card text-decoration-none">
                <div class="card-body text-center py-4">
                    <div class="manage-icon"
                        style="background:linear-gradient(135deg,
                            rgba(245,158,11,0.1),rgba(251,191,36,0.15));">
                        <i class="bi bi-people-fill" style="color:#d97706;"></i>
                    </div>
                    <div class="manage-label">Manage Teams</div>
                    <div class="manage-count">
                        {{ $tournament->teams->count() }} Teams
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-lg-3">
            <a href="{{ route('player.index', $tournament->id) }}" class="card manage-card text-decoration-none">
                <div class="card-body text-center py-4">
                    <div class="manage-icon"
                        style="background:linear-gradient(135deg,
                            rgba(16,185,129,0.1),rgba(52,211,153,0.15));">
                        <i class="bi bi-person-badge-fill" style="color:#059669;"></i>
                    </div>
                    <div class="manage-label">Manage Players</div>
                    <div class="manage-count">
                        {{ $tournament->players->count() }} Players
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-lg-3">
            <a href="{{ route('auction.panel', $tournament->id) }}" class="card manage-card text-decoration-none">
                <div class="card-body text-center py-4">
                    <div class="manage-icon"
                        style="background:linear-gradient(135deg,
                            rgba(239,68,68,0.1),rgba(252,165,165,0.15));">
                        <i class="bi bi-broadcast" style="color:#dc2626;"></i>
                    </div>
                    <div class="manage-label">Auction Panel</div>
                    <div class="manage-count">
                        {{ $tournament->players->where('status', 'sold')->count() }}
                        Sold
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-lg-3">
            <a href="{{ route('auction.results', $tournament->id) }}" class="card manage-card text-decoration-none">
                <div class="card-body text-center py-4">
                    <div class="manage-icon"
                        style="background:linear-gradient(135deg,
                            rgba(59,130,246,0.1),rgba(147,197,253,0.15));">
                        <i class="bi bi-bar-chart-fill" style="color:#1d4ed8;"></i>
                    </div>
                    <div class="manage-label">Auction Results</div>
                    <div class="manage-count">View All</div>
                </div>
            </a>
        </div>
    </div>

@endsection

@push('styles')
    <style>
        .manage-card {
            border: 1.5px solid #f0f2f8 !important;
            transition: all 0.2s;
        }

        .manage-card:hover {
            transform: translateY(-4px);
            border-color: #6c3fc5 !important;
            box-shadow: 0 10px 30px rgba(108, 63, 197, 0.12) !important;
        }

        .manage-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            margin: 0 auto 14px;
        }

        .manage-label {
            font-weight: 700;
            font-size: 0.9rem;
            color: #1e1e2e;
            margin-bottom: 4px;
        }

        .manage-count {
            font-size: 0.78rem;
            color: #9ca3af;
            font-weight: 500;
        }
    </style>
@endpush

@push('scripts')
    <script>
        function copyById(inputId, btn) {
            const val = document.getElementById(inputId).value;
            navigator.clipboard.writeText(val).then(() => {
                const orig = btn.innerHTML;
                btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Copied!';
                setTimeout(() => btn.innerHTML = orig, 2000);
            });
        }
    </script>
@endpush
