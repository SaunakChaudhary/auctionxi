@extends('layouts.public')

@section('title', 'Live — ' . $tournament->name)

@section('nav-items')
    <a href="#results" class="nav-pill">Results</a>
    <a href="#teams" class="nav-pill">Teams</a>
    <a href="#search" class="nav-pill">Search</a>
@endsection

@section('refresh-bar')
    <div class="refresh-bar">
        <i class="bi bi-arrow-clockwise me-1"></i>
        Auto-refreshes in <strong id="cd">12</strong>s
        <button onclick="location.reload()">Refresh Now</button>
    </div>
@endsection

@section('content')

    {{-- Tournament Info Bar --}}
    <div class="card mb-4">
        <div class="card-body" style="padding:20px 24px;">
            <div class="row align-items-center g-3">
                <div class="col-lg-7">
                    <div
                        style="font-size:0.72rem;font-weight:600;
                            color:var(--muted);text-transform:uppercase;
                            letter-spacing:1px;margin-bottom:6px;">
                        Cricket Player Auction
                    </div>
                    <h1
                        style="font-family:'Poppins',sans-serif;
                           font-weight:800;font-size:clamp(1.3rem,3vw,1.8rem);
                           color:var(--dark);margin:0 0 8px;">
                        {{ $tournament->name }}
                    </h1>
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        @if ($tournament->location)
                            <span style="font-size:0.82rem;color:var(--muted);">
                                <i class="bi bi-geo-alt me-1"></i>
                                {{ $tournament->location }}
                            </span>
                        @endif
                        @if ($tournament->auction_date)
                            <span style="font-size:0.82rem;color:var(--muted);">
                                <i class="bi bi-calendar3 me-1"></i>
                                {{ \Carbon\Carbon::parse($tournament->auction_date)->format('d M Y') }}
                            </span>
                        @endif
                        @php
                            $statusMap = [
                                'pending' => ['label' => 'Not Started', 'class' => 'status-pending-auction'],
                                'live' => ['label' => 'Live', 'class' => 'status-live'],
                                'completed' => ['label' => 'Completed', 'class' => 'status-completed'],
                            ];
                            $st = $statusMap[$tournament->auction_status] ?? $statusMap['pending'];
                        @endphp
                        <span class="status-badge {{ $st['class'] }}">
                            {{ $st['label'] }}
                        </span>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="row g-2">
                        @php
                            $soldCount = $results->where('status', 'sold')->count();
                            $unsoldCount = $results->where('status', 'unsold')->count();
                        @endphp
                        <div class="col-4">
                            <div class="stat-box">
                                <div class="stat-num">
                                    {{ $tournament->players->count() }}
                                </div>
                                <div class="stat-lbl">Players</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-box" style="border-color:#bbf7d0;">
                                <div class="stat-num" style="color:var(--success);">
                                    {{ $soldCount }}
                                </div>
                                <div class="stat-lbl">Sold</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-box">
                                <div class="stat-num" style="color:var(--muted);">
                                    {{ $teams->count() }}
                                </div>
                                <div class="stat-lbl">Teams</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Search --}}
    {{-- Search Player — Separate Section --}}
    <div class="card mb-4" id="search">
        <div class="card-header" style="justify-content:space-between;">
            <span>
                <i class="bi bi-search"></i>
                Search Player by ID
            </span>
            <span style="font-size:0.72rem;color:var(--muted);
                     font-weight:400;">
                Search pauses auto-refresh
            </span>
        </div>
        <div class="card-body">
            <div class="d-flex gap-2 mb-3" style="max-width:480px;">
                <input type="text" id="searchInput" class="form-control" placeholder="Enter Player ID — e.g. PX1001"
                    style="font-weight:700;letter-spacing:1px;
                          text-transform:uppercase;"
                    autocomplete="off" onkeydown="if(event.key==='Enter') searchPlayer()">
                <button onclick="searchPlayer()" class="btn btn-primary" style="white-space:nowrap;min-width:90px;">
                    <i class="bi bi-search me-1"></i>Search
                </button>
                <button onclick="clearSearch()" class="btn btn-outline-secondary" title="Clear search">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            {{-- Search Result --}}
            <div id="searchResult" style="display:none;"></div>

            {{-- Search Notice --}}
            <div id="searchNotice"
                style="font-size:0.75rem;color:var(--muted);
                    display:none;margin-top:8px;">
                <i class="bi bi-pause-circle me-1"></i>
                Auto-refresh paused while searching.
                <a href="#" onclick="clearSearch();return false;" style="color:var(--primary-lt);font-weight:600;">
                    Resume
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">

        {{-- Results Table --}}
        <div class="col-lg-8" id="results">
            <div class="card">
                <div class="card-header" style="justify-content:space-between;">
                    <span>
                        <i class="bi bi-list-ul me-1"></i>
                        Auction Results
                    </span>
                    <span style="font-size:0.78rem;color:var(--muted);
                             font-weight:400;">
                        {{ $results->count() }} entries
                    </span>
                </div>

                @if ($results->count() > 0)
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Player</th>
                                    <th>ID</th>
                                    <th>Role</th>
                                    <th>Team</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($results as $r)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                @php
                                                    $__p = $r->player;
                                                    $__src = playerAvatarSrc($__p);
                                                    $__initial = strtoupper(substr($__p->name ?? '?', 0, 1));
                                                    $__bg = playerAvatarColor($__p->name ?? '');
                                                @endphp
                                                @if ($__src)
                                                    <img src="{{ $__src }}" alt="{{ $__p->name }}"
                                                        style="width:36px;height:36px;border-radius:8px;
                object-fit:cover;flex-shrink:0;
                border:1px solid #e2e8f0;"
                                                        onerror="this.style.display='none';
                  this.nextElementSibling.style.display='flex';">
                                                    <div
                                                        style="display:none;width:36px;height:36px;
                border-radius:8px;background:{{ $__bg }};
                color:#fff;font-weight:700;font-size:0.78rem;
                align-items:center;justify-content:center;
                flex-shrink:0;">
                                                        {{ $__initial }}
                                                    </div>
                                                @else
                                                    <div
                                                        style="width:36px;height:36px;border-radius:8px;
                background:{{ $__bg }};color:#fff;font-weight:700;
                font-size:0.78rem;display:flex;align-items:center;
                justify-content:center;flex-shrink:0;">
                                                        {{ $__initial }}
                                                    </div>
                                                @endif
                                                <span style="font-weight:600;">
                                                    {{ $r->player->name }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="pid-badge">
                                                {{ $r->player->player_id }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="role-badge">
                                                {{ $r->player->role }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($r->team)
                                                <div class="d-flex align-items-center gap-2">
                                                    @if ($r->team->logo)
                                                        <img src="{{ teamLogoSrc($r->team) }}"
                                                            style="width:24px;height:24px;
                                                border-radius:4px;
                                                object-fit:cover;">
                                                    @endif
                                                    <span
                                                        style="font-size:0.82rem;
                                                 font-weight:500;">
                                                        {{ $r->team->name }}
                                                    </span>
                                                </div>
                                            @else
                                                <span style="color:var(--muted);">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($r->status === 'sold')
                                                <span
                                                    style="font-weight:700;
                                             color:var(--primary-lt);">
                                                    ₹{{ number_format($r->sold_price) }}
                                                </span>
                                            @else
                                                <span style="color:var(--muted);">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span
                                                class="status-badge
                                    status-{{ $r->status }}">
                                                {{ ucfirst($r->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="card-body text-center py-5">
                        <div style="font-size:2.5rem;margin-bottom:12px;">⏳</div>
                        <h5 style="font-weight:700;margin-bottom:6px;">
                            Auction Not Started Yet
                        </h5>
                        <p style="color:var(--muted);font-size:0.875rem;">
                            Results will appear here once the auctioneer
                            begins assigning players.
                        </p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Teams Sidebar --}}
        <div class="col-lg-4" id="teams">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-people me-1"></i>Teams
                </div>
                @if ($teams->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach ($teams as $team)
                            <a href="{{ route('public.team.squad', [$tournament->code, $team->id]) }}"
                                class="list-group-item list-group-item-action p-3"
                                style="text-decoration:none;border-color:#f1f5f9;">
                                <div class="d-flex align-items-center gap-3">
                                    @if ($team->logo)
                                        <img src="{{ teamLogoSrc($team) }}"
                                            style="width:40px;height:40px;
                                    border-radius:8px;object-fit:cover;
                                    border:1px solid var(--border);">
                                    @else
                                        <div
                                            style="width:40px;height:40px;
                                    border-radius:8px;background:var(--primary);
                                    display:flex;align-items:center;
                                    justify-content:center;font-weight:700;
                                    color:#fff;font-size:0.9rem;
                                    flex-shrink:0;">
                                            {{ strtoupper(substr($team->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div class="flex-fill" style="min-width:0;">
                                        <div
                                            style="font-weight:600;font-size:0.875rem;
                                        color:var(--dark);
                                        white-space:nowrap;overflow:hidden;
                                        text-overflow:ellipsis;">
                                            {{ $team->name }}
                                        </div>
                                        <div style="font-size:0.72rem;color:var(--muted);">
                                            {{ $team->players->count() }} players
                                            &nbsp;·&nbsp;
                                            ₹{{ number_format($team->budget - $team->spent) }}
                                            left
                                        </div>
                                    </div>
                                    <div style="text-align:right;flex-shrink:0;">
                                        <div
                                            style="font-weight:700;font-size:0.82rem;
                                        color:var(--primary-lt);">
                                            ₹{{ number_format($team->spent) }}
                                        </div>
                                        <div
                                            style="font-size:0.68rem;
                                        color:var(--muted);">
                                            spent</div>
                                    </div>
                                </div>
                                {{-- Budget bar --}}
                                @php
                                    $p = $team->budget > 0 ? min(100, ($team->spent / $team->budget) * 100) : 0;
                                    $bc = $p >= 90 ? '#dc2626' : ($p >= 70 ? '#d97706' : '#16a34a');
                                @endphp
                                <div
                                    style="height:3px;background:#f1f5f9;
                                border-radius:2px;margin-top:8px;
                                overflow:hidden;">
                                    <div
                                        style="height:100%;width:{{ $p }}%;
                                    background:{{ $bc }};
                                    border-radius:2px;">
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="card-body text-center py-4">
                        <p style="color:var(--muted);font-size:0.875rem;margin:0;">
                            No teams yet
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        'use strict';

        // ── AUTO REFRESH ──
        let refreshSeconds = 12;
        let refreshPaused = false;
        let refreshTimer = null;
        const cdEl = document.getElementById('cd');

        function startRefreshTimer() {
            refreshTimer = setInterval(() => {
                if (refreshPaused) return;
                refreshSeconds--;
                if (cdEl) cdEl.textContent = refreshSeconds;
                if (refreshSeconds <= 0) location.reload();
            }, 1000);
        }

        function pauseRefresh() {
            refreshPaused = true;
            if (cdEl) cdEl.textContent = '⏸';
            document.getElementById('searchNotice').style.display = 'block';
        }

        function resumeRefresh() {
            refreshPaused = false;
            refreshSeconds = 12;
            if (cdEl) cdEl.textContent = refreshSeconds;
            document.getElementById('searchNotice').style.display = 'none';
        }

        startRefreshTimer();

        // ── CLEAR SEARCH ──
        function clearSearch() {
            document.getElementById('searchInput').value = '';
            document.getElementById('searchResult').style.display = 'none';
            resumeRefresh();
        }

        // ── SEARCH PLAYER ──
        async function searchPlayer() {
            const pid = document.getElementById('searchInput')
                .value.trim().toUpperCase();
            if (!pid) return;

            // Pause auto-refresh while user is searching
            pauseRefresh();

            const resultDiv = document.getElementById('searchResult');
            resultDiv.style.display = 'block';
            resultDiv.innerHTML = `
        <div style="padding:12px 0;color:var(--muted);font-size:0.875rem;
                    display:flex;align-items:center;gap:8px;">
            <span class="spinner-border spinner-border-sm text-primary">
            </span>
            Searching for <strong>${pid}</strong>...
        </div>`;

            try {
                const res = await fetch(
                    `{{ route('public.search', $tournament->code) }}` +
                    `?player_id=${encodeURIComponent(pid)}`
                );
                const player = await res.json();

                if (!player) {
                    resultDiv.innerHTML = `
                <div style="padding:14px 16px;background:#fef2f2;
                            border:1px solid #fecaca;border-radius:8px;
                            font-size:0.875rem;color:#991b1b;">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    No player found with ID
                    <strong>${pid}</strong>
                </div>`;
                    return;
                }

                const statusLabels = {
                    pending: 'Pending',
                    approved: 'Approved',
                    sold: 'Sold',
                    unsold: 'Unsold'
                };
                const statusClasses = {
                    pending: 'status-pending',
                    approved: 'status-approved',
                    sold: 'status-sold',
                    unsold: 'status-unsold'
                };

                // Build avatar
                const src = player.photo ?
                    player.photo :
                    (player.image_url || null);

                const initial = (player.name || '?').charAt(0).toUpperCase();

                const photoHtml = src ?
                    `<img src="${src}"
                    style="width:80px;height:90px;border-radius:10px;
                           object-fit:cover;object-position:top;
                           border:2px solid #e2e8f0;flex-shrink:0;"
                    onerror="this.outerHTML='<div style=\'width:80px;height:90px;border-radius:10px;background:var(--primary);display:flex;align-items:center;justify-content:center;font-weight:800;color:#fff;font-size:2rem;flex-shrink:0;\'>${initial}</div>'">` :
                    `<div style="width:80px;height:90px;border-radius:10px;
                           background:var(--primary);display:flex;
                           align-items:center;justify-content:center;
                           font-weight:800;color:#fff;font-size:2rem;
                           flex-shrink:0;">
                   ${initial}
               </div>`;

                resultDiv.innerHTML = `
            <div style="background:#f8fafc;border:1px solid var(--border);
                        border-radius:10px;padding:16px;">
                <div style="display:flex;gap:16px;align-items:flex-start;">
                    ${photoHtml}
                    <div style="flex:1;min-width:0;">
                        <div style="display:flex;align-items:center;
                                    gap:8px;flex-wrap:wrap;margin-bottom:4px;">
                            <strong style="font-size:1.05rem;">
                                ${player.name}
                            </strong>
                            <span class="pid-badge">
                                ${player.player_id}
                            </span>
                            <span class="status-badge
                                ${statusClasses[player.status]||''}">
                                ${statusLabels[player.status]||player.status}
                            </span>
                        </div>
                        <div style="margin-bottom:10px;">
                            <span class="role-badge">${player.role}</span>
                            ${player.city
                              ? `<span style="font-size:0.78rem;
                                                 color:var(--muted);
                                                 margin-left:6px;">
                                         ${player.city}
                                     </span>` : ''}
                            ${player.age
                              ? `<span style="font-size:0.78rem;
                                                 color:var(--muted);
                                                 margin-left:6px;">
                                         Age ${player.age}
                                     </span>` : ''}
                        </div>
                        <div style="display:grid;
                                    grid-template-columns:1fr 1fr;
                                    gap:6px;">
                            ${player.mobile ? `
                                <div style="background:#fff;border:1px solid
                                            var(--border);border-radius:6px;
                                            padding:6px 10px;">
                                    <div style="font-size:0.62rem;font-weight:600;
                                                color:var(--muted);
                                                text-transform:uppercase;
                                                margin-bottom:1px;">
                                        Mobile
                                    </div>
                                    <div style="font-size:0.8rem;font-weight:600;">
                                        ${player.mobile}
                                    </div>
                                </div>` : ''}
                            ${player.base_price > 0 ? `
                                <div style="background:#eff6ff;border:1px solid
                                            #bfdbfe;border-radius:6px;
                                            padding:6px 10px;">
                                    <div style="font-size:0.62rem;font-weight:600;
                                                color:var(--primary);
                                                text-transform:uppercase;
                                                margin-bottom:1px;">
                                        Base Price
                                    </div>
                                    <div style="font-size:0.8rem;font-weight:700;
                                                color:var(--primary-lt);">
                                        ₹${Number(player.base_price)
                                             .toLocaleString('en-IN')}
                                    </div>
                                </div>` : ''}
                            ${player.batting_style ? `
                                <div style="background:#fff;border:1px solid
                                            var(--border);border-radius:6px;
                                            padding:6px 10px;">
                                    <div style="font-size:0.62rem;font-weight:600;
                                                color:var(--muted);
                                                text-transform:uppercase;
                                                margin-bottom:1px;">
                                        Batting
                                    </div>
                                    <div style="font-size:0.8rem;font-weight:600;">
                                        ${player.batting_style}
                                    </div>
                                </div>` : ''}
                            ${player.experience ? `
                                <div style="background:#fff;border:1px solid
                                            var(--border);border-radius:6px;
                                            padding:6px 10px;">
                                    <div style="font-size:0.62rem;font-weight:600;
                                                color:var(--muted);
                                                text-transform:uppercase;
                                                margin-bottom:1px;">
                                        Experience
                                    </div>
                                    <div style="font-size:0.8rem;font-weight:600;">
                                        ${player.experience}
                                    </div>
                                </div>` : ''}
                        </div>
                    </div>
                </div>
            </div>`;

            } catch (e) {
                resultDiv.innerHTML = `
            <div class="alert alert-danger" style="border-radius:8px;">
                Error searching. Please try again.
            </div>`;
            }
        }
    </script>
@endpush
