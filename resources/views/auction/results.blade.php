@extends('layouts.app')

@section('title', 'Auction Results')
@section('page-title', 'Auction Results')

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
                <li class="breadcrumb-item active">Auction Results</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center
                justify-content-between flex-wrap gap-3 mt-2">
            <div>
                <h1>Auction Results</h1>
                <p>{{ $tournament->name }} &mdash;
                    Complete auction summary</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('auction.panel', $tournament->id) }}" class="btn btn-outline-primary">
                    <i class="bi bi-broadcast me-2"></i>Back to Panel
                </a>
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="bi bi-printer-fill me-2"></i>Print Results
                </button>
            </div>
        </div>
    </div>

    {{-- Summary Cards --}}
    @php
        $totalSold = $results->where('status', 'sold')->count();
        $totalUnsold = $results->where('status', 'unsold')->count();
        $totalSpent = $results->where('status', 'sold')->sum('sold_price');
        $avgPrice = $totalSold > 0 ? $totalSpent / $totalSold : 0;
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card stat-purple">
                <div class="stat-icon">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="stat-value">{{ $results->count() }}</div>
                <div class="stat-label">Total Players</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card stat-green">
                <div class="stat-icon">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="stat-value">{{ $totalSold }}</div>
                <div class="stat-label">Players Sold</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card stat-amber">
                <div class="stat-icon">
                    <i class="bi bi-wallet2-fill"></i>
                </div>
                <div class="stat-value" style="font-size:1.4rem;">
                    ₹{{ number_format($totalSpent) }}
                </div>
                <div class="stat-label">Total Spent</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card stat-blue">
                <div class="stat-icon">
                    <i class="bi bi-graph-up"></i>
                </div>
                <div class="stat-value" style="font-size:1.4rem;">
                    ₹{{ number_format($avgPrice) }}
                </div>
                <div class="stat-label">Avg. Sold Price</div>
            </div>
        </div>
    </div>

    {{-- Results Table --}}
    <div class="card mb-4">
        <div class="card-header d-flex align-items-center
                justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-table text-primary"></i>
                Complete Results
            </div>
            <div class="d-flex gap-2">
                <input type="text" id="searchResult" class="form-control form-control-sm" placeholder="Search..."
                    style="width:180px;" oninput="searchResult(this.value)">
            </div>
        </div>
        @if ($results->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="resultsTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Player</th>
                            <th>Player ID</th>
                            <th>Role</th>
                            <th>Base Price</th>
                            <th>Team</th>
                            <th>Sold Price</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($results as $i => $result)
                            <tr class="result-row" data-name="{{ strtolower($result->player->name) }}">
                                <td style="color:#9ca3af;font-size:0.8rem;">
                                    {{ $i + 1 }}
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @php
                                            $__p = $result->player;
                                            $__src = playerAvatarSrc($__p);
                                            $__initial = strtoupper(substr($__p->name ?? '?', 0, 1));
                                            $__bg = playerAvatarColor($__p->name ?? '');
                                        @endphp
                                        @if ($__src)
                                            <img src="{{ $__src }}" alt="{{ $__p->name }}"
                                                style="width:38px;height:38px;border-radius:50%;
                object-fit:cover;flex-shrink:0;
                border:2px solid #e8eaf0;"
                                                onerror="this.style.display='none';
                  this.nextElementSibling.style.display='flex';">
                                            <div
                                                style="display:none;width:38px;height:38px;
                border-radius:50%;background:{{ $__bg }};
                color:#fff;font-weight:700;font-size:0.8rem;
                align-items:center;justify-content:center;
                flex-shrink:0;">
                                                {{ $__initial }}
                                            </div>
                                        @else
                                            <div
                                                style="width:38px;height:38px;border-radius:50%;
                background:{{ $__bg }};color:#fff;font-weight:700;
                font-size:0.8rem;display:flex;align-items:center;
                justify-content:center;flex-shrink:0;">
                                                {{ $__initial }}
                                            </div>
                                        @endif
                                        <span
                                            style="font-weight:600;
                                         font-size:0.875rem;">
                                            {{ $result->player->name }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <code
                                        style="background:#f0f2f8;color:#6c3fc5;
                                     padding:3px 8px;border-radius:6px;
                                     font-size:0.8rem;font-weight:700;">
                                        {{ $result->player->player_id }}
                                    </code>
                                </td>
                                <td>
                                    <span class="badge-status badge-approved" style="font-size:0.7rem;">
                                        {{ $result->player->role }}
                                    </span>
                                </td>
                                <td style="font-size:0.875rem;color:#6b7280;">
                                    @if ($result->player->base_price > 0)
                                        ₹{{ number_format($result->player->base_price) }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($result->team)
                                        <div class="d-flex align-items-center gap-2">
                                            @if ($result->team->logo)
                                                <img src="{{ Storage::url($result->team->logo) }}"
                                                    style="width:28px;height:28px;
                                        border-radius:6px;
                                        object-fit:cover;">
                                            @else
                                                <div
                                                    style="width:28px;height:28px;
                                        border-radius:6px;
                                        background:linear-gradient(
                                        135deg,#d97706,#f59e0b);
                                        display:flex;align-items:center;
                                        justify-content:center;
                                        font-size:0.7rem;font-weight:700;
                                        color:#fff;">
                                                    {{ strtoupper(substr($result->team->name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <span
                                                style="font-weight:600;
                                         font-size:0.875rem;">
                                                {{ $result->team->name }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($result->status === 'sold')
                                        <span
                                            style="font-weight:700;color:#6c3fc5;
                                     font-size:0.9rem;">
                                            ₹{{ number_format($result->sold_price) }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge-status
                            badge-{{ $result->status }}">
                                        {{ ucfirst($result->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="card-body text-center py-5">
                <div style="font-size:3rem;margin-bottom:16px;">📊</div>
                <h5 style="font-weight:700;color:#1e1e2e;margin-bottom:8px;">
                    No Results Yet
                </h5>
                <p class="text-muted mb-4">
                    Start the auction to see results here.
                </p>
                <a href="{{ route('auction.panel', $tournament->id) }}" class="btn btn-primary">
                    <i class="bi bi-broadcast me-2"></i>Go to Auction Panel
                </a>
            </div>
        @endif
    </div>

    {{-- Team Summary --}}
    @if ($tournament->teams->count() > 0)
        <div class="card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-people-fill text-warning"></i>
                Team Summary
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Team</th>
                            <th>Owner</th>
                            <th>Players</th>
                            <th>Budget</th>
                            <th>Spent</th>
                            <th>Remaining</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tournament->teams as $team)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if ($team->logo)
                                            <img src="{{ Storage::url($team->logo) }}" class="team-logo"
                                                style="width:36px;height:36px;">
                                        @else
                                            <div class="team-logo-placeholder"
                                                style="width:36px;height:36px;
                                        font-size:0.9rem;">
                                                {{ strtoupper(substr($team->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <span style="font-weight:600;">
                                            {{ $team->name }}
                                        </span>
                                    </div>
                                </td>
                                <td style="font-size:0.875rem;color:#6b7280;">
                                    {{ $team->owner_name }}
                                </td>
                                <td>
                                    <span style="font-weight:700;">
                                        {{ $team->players->count() }}
                                    </span>
                                    <span style="font-size:0.78rem;color:#9ca3af;">
                                        players
                                    </span>
                                </td>
                                <td style="font-size:0.875rem;">
                                    ₹{{ number_format($team->budget) }}
                                </td>
                                <td style="font-weight:700;color:#6c3fc5;">
                                    ₹{{ number_format($team->spent) }}
                                </td>
                                <td>
                                    @php
                                        $rem = $team->budget - $team->spent;
                                        $remColor = $rem < $team->budget * 0.2 ? '#ef4444' : '#10b981';
                                    @endphp
                                    <span
                                        style="font-weight:700;
                                     color:{{ $remColor }};">
                                        ₹{{ number_format($rem) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

@endsection

@push('scripts')
    <script>
        function searchResult(val) {
            const q = val.toLowerCase();
            document.querySelectorAll('.result-row').forEach(row => {
                row.style.display =
                    row.dataset.name.includes(q) ? '' : 'none';
            });
        }
    </script>

    @push('styles')
        <style>
            @media print {

                .sidebar,
                .topbar,
                .page-header nav,
                .btn,
                button {
                    display: none !important;
                }

                .main-content {
                    margin-left: 0 !important;
                }

                .content-wrapper {
                    padding: 0 !important;
                }
            }
        </style>
    @endpush
@endpush
