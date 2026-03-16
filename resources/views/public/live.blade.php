@extends('layouts.public')

@section('title', 'Live — ' . $tournament->name)

@section('nav-items')
<a href="#results" class="nav-pill">Results</a>
<a href="#teams"   class="nav-pill">Teams</a>
<a href="#search"  class="nav-pill">Search</a>
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
                <div style="font-size:0.72rem;font-weight:600;
                            color:var(--muted);text-transform:uppercase;
                            letter-spacing:1px;margin-bottom:6px;">
                    Cricket Player Auction
                </div>
                <h1 style="font-family:'Poppins',sans-serif;
                           font-weight:800;font-size:clamp(1.3rem,3vw,1.8rem);
                           color:var(--dark);margin:0 0 8px;">
                    {{ $tournament->name }}
                </h1>
                <div class="d-flex flex-wrap gap-3 align-items-center">
                    @if($tournament->location)
                    <span style="font-size:0.82rem;color:var(--muted);">
                        <i class="bi bi-geo-alt me-1"></i>
                        {{ $tournament->location }}
                    </span>
                    @endif
                    @if($tournament->auction_date)
                    <span style="font-size:0.82rem;color:var(--muted);">
                        <i class="bi bi-calendar3 me-1"></i>
                        {{ \Carbon\Carbon::parse($tournament->auction_date)
                           ->format('d M Y') }}
                    </span>
                    @endif
                    @php
                        $statusMap = [
                            'pending'   => ['label'=>'Not Started',
                                            'class'=>'status-pending-auction'],
                            'live'      => ['label'=>'Live',
                                            'class'=>'status-live'],
                            'completed' => ['label'=>'Completed',
                                            'class'=>'status-completed'],
                        ];
                        $st = $statusMap[$tournament->auction_status]
                              ?? $statusMap['pending'];
                    @endphp
                    <span class="status-badge {{ $st['class'] }}">
                        {{ $st['label'] }}
                    </span>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="row g-2">
                    @php
                        $soldCount   = $results->where('status','sold')->count();
                        $unsoldCount = $results->where('status','unsold')->count();
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
                        <div class="stat-box"
                             style="border-color:#bbf7d0;">
                            <div class="stat-num"
                                 style="color:var(--success);">
                                {{ $soldCount }}
                            </div>
                            <div class="stat-lbl">Sold</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="stat-box">
                            <div class="stat-num"
                                 style="color:var(--muted);">
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
<div class="card mb-4" id="search">
    <div class="card-header">
        <i class="bi bi-search"></i> Search Player by ID
    </div>
    <div class="card-body">
        <div class="d-flex gap-2 mb-3" style="max-width:420px;">
            <input type="text"
                   id="searchInput"
                   class="form-control"
                   placeholder="e.g. PX1001"
                   style="font-weight:600;letter-spacing:1px;"
                   onkeydown="if(event.key==='Enter') searchPlayer()">
            <button onclick="searchPlayer()"
                    class="btn btn-primary"
                    style="white-space:nowrap;">
                <i class="bi bi-search me-1"></i>Search
            </button>
        </div>
        <div id="searchResult" style="display:none;"></div>
    </div>
</div>

<div class="row g-4">

    {{-- Results Table --}}
    <div class="col-lg-8" id="results">
        <div class="card">
            <div class="card-header"
                 style="justify-content:space-between;">
                <span>
                    <i class="bi bi-list-ul me-1"></i>
                    Auction Results
                </span>
                <span style="font-size:0.78rem;color:var(--muted);
                             font-weight:400;">
                    {{ $results->count() }} entries
                </span>
            </div>

            @if($results->count() > 0)
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
                        @foreach($results as $r)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($r->player->photo)
                                    <img src="{{ Storage::url($r->player->photo) }}"
                                         class="p-avatar" alt="">
                                    @elseif($r->player->image_url)
                                    <img src="{{ $r->player->image_url }}"
                                         class="p-avatar" alt=""
                                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                                    <div class="p-avatar-placeholder" style="display:none;">
                                        {{ strtoupper(substr($r->player->name,0,1)) }}
                                    </div>
                                    @else
                                    <div class="p-avatar-placeholder">
                                        {{ strtoupper(substr($r->player->name,0,1)) }}
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
                                @if($r->team)
                                <div class="d-flex align-items-center gap-2">
                                    @if($r->team->logo)
                                    <img src="{{ Storage::url($r->team->logo) }}"
                                         style="width:24px;height:24px;
                                                border-radius:4px;
                                                object-fit:cover;">
                                    @endif
                                    <span style="font-size:0.82rem;
                                                 font-weight:500;">
                                        {{ $r->team->name }}
                                    </span>
                                </div>
                                @else
                                <span style="color:var(--muted);">—</span>
                                @endif
                            </td>
                            <td>
                                @if($r->status==='sold')
                                <span style="font-weight:700;
                                             color:var(--primary-lt);">
                                    ₹{{ number_format($r->sold_price) }}
                                </span>
                                @else
                                <span style="color:var(--muted);">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="status-badge
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
            @if($teams->count() > 0)
            <div class="list-group list-group-flush">
                @foreach($teams as $team)
                <a href="{{ route('public.team.squad',
                           [$tournament->code, $team->id]) }}"
                   class="list-group-item list-group-item-action p-3"
                   style="text-decoration:none;border-color:#f1f5f9;">
                    <div class="d-flex align-items-center gap-3">
                        @if($team->logo)
                        <img src="{{ Storage::url($team->logo) }}"
                             style="width:40px;height:40px;
                                    border-radius:8px;object-fit:cover;
                                    border:1px solid var(--border);">
                        @else
                        <div style="width:40px;height:40px;
                                    border-radius:8px;background:var(--primary);
                                    display:flex;align-items:center;
                                    justify-content:center;font-weight:700;
                                    color:#fff;font-size:0.9rem;
                                    flex-shrink:0;">
                            {{ strtoupper(substr($team->name,0,1)) }}
                        </div>
                        @endif
                        <div class="flex-fill" style="min-width:0;">
                            <div style="font-weight:600;font-size:0.875rem;
                                        color:var(--dark);
                                        white-space:nowrap;overflow:hidden;
                                        text-overflow:ellipsis;">
                                {{ $team->name }}
                            </div>
                            <div style="font-size:0.72rem;color:var(--muted);">
                                {{ $team->players->count() }} players
                                &nbsp;·&nbsp;
                                ₹{{ number_format(
                                   $team->budget-$team->spent) }} left
                            </div>
                        </div>
                        <div style="text-align:right;flex-shrink:0;">
                            <div style="font-weight:700;font-size:0.82rem;
                                        color:var(--primary-lt);">
                                ₹{{ number_format($team->spent) }}
                            </div>
                            <div style="font-size:0.68rem;
                                        color:var(--muted);">spent</div>
                        </div>
                    </div>
                    {{-- Budget bar --}}
                    @php
                        $p  = $team->budget > 0
                              ? min(100,($team->spent/$team->budget)*100) : 0;
                        $bc = $p>=90?'#dc2626':($p>=70?'#d97706':'#16a34a');
                    @endphp
                    <div style="height:3px;background:#f1f5f9;
                                border-radius:2px;margin-top:8px;
                                overflow:hidden;">
                        <div style="height:100%;width:{{ $p }}%;
                                    background:{{ $bc }};
                                    border-radius:2px;"></div>
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
    // Auto refresh countdown
    let s = 12;
    const cdEl = document.getElementById('cd');
    setInterval(() => {
        s--;
        if (cdEl) cdEl.textContent = s;
        if (s <= 0) location.reload();
    }, 1000);

    // Search player
    async function searchPlayer() {
        const pid = document.getElementById('searchInput')
                            .value.trim().toUpperCase();
        if (!pid) return;

        const resultDiv = document.getElementById('searchResult');
        resultDiv.style.display = 'block';
        resultDiv.innerHTML = `
            <div style="padding:16px;color:var(--muted);
                        font-size:0.875rem;">
                <span class="spinner-border spinner-border-sm
                             me-2 text-primary"></span>
                Searching for ${pid}...
            </div>`;

        try {
            const res = await fetch(
                `{{ route('public.search', $tournament->code) }}` +
                `?player_id=${pid}`
            );
            const player = await res.json();

            if (!player) {
                resultDiv.innerHTML = `
                    <div style="padding:16px;background:#fef2f2;
                                border:1px solid #fecaca;
                                border-radius:8px;font-size:0.875rem;
                                color:#991b1b;">
                        <i class="bi bi-exclamation-circle me-2"></i>
                        No player found with ID <strong>${pid}</strong>
                    </div>`;
                return;
            }

            const statusLabels = {
                pending:'Pending', approved:'Approved',
                sold:'Sold', unsold:'Unsold'
            };
            const statusClasses = {
                pending:'status-pending', approved:'status-approved',
                sold:'status-sold', unsold:'status-unsold'
            };

            const photoHtml = player.photo
                ? `<img src="/storage/${player.photo}"
                        style="width:52px;height:52px;border-radius:8px;
                               object-fit:cover;border:1px solid var(--border);">`
                : player.image_url
                ? `<img src="${player.image_url}"
                        style="width:52px;height:52px;border-radius:8px;
                               object-fit:cover;border:1px solid var(--border);"
                        onerror="this.outerHTML='<div style=\'width:52px;height:52px;border-radius:8px;background:var(--primary);display:flex;align-items:center;justify-content:center;font-weight:700;color:#fff;font-size:1.2rem;\'>${player.name.charAt(0).toUpperCase()}</div>'">`
                : `<div style="width:52px;height:52px;border-radius:8px;
                               background:var(--primary);display:flex;
                               align-items:center;justify-content:center;
                               font-weight:700;color:#fff;font-size:1.2rem;">
                       ${player.name.charAt(0).toUpperCase()}
                   </div>`;

            resultDiv.innerHTML = `
                <div style="background:#f8fafc;border:1px solid var(--border);
                            border-radius:10px;padding:16px;">
                    <div class="d-flex align-items-start gap-3">
                        ${photoHtml}
                        <div>
                            <div class="d-flex align-items-center
                                        gap-2 flex-wrap mb-1">
                                <strong style="font-size:1rem;">
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
                            <div style="font-size:0.82rem;
                                        color:var(--muted);margin-bottom:8px;">
                                <span class="role-badge">${player.role}</span>
                                ${player.city
                                  ? `&nbsp;·&nbsp;${player.city}` : ''}
                                ${player.age
                                  ? `&nbsp;·&nbsp;Age ${player.age}` : ''}
                            </div>
                            <div class="d-flex gap-2 flex-wrap">
                                ${player.batting_style ? `
                                <span style="background:#f1f5f9;
                                             color:var(--mid);
                                             padding:2px 8px;
                                             border-radius:4px;
                                             font-size:0.72rem;">
                                    🏏 ${player.batting_style}
                                </span>` : ''}
                                ${player.base_price > 0 ? `
                                <span style="background:#eff6ff;
                                             color:var(--primary);
                                             padding:2px 8px;
                                             border-radius:4px;
                                             font-size:0.72rem;
                                             font-weight:600;">
                                    Base: ₹${Number(player.base_price)
                                             .toLocaleString()}
                                </span>` : ''}
                            </div>
                        </div>
                    </div>
                </div>`;
        } catch(e) {
            resultDiv.innerHTML = `
                <div class="alert alert-danger" style="border-radius:8px;">
                    Error searching. Please try again.
                </div>`;
        }
    }
</script>
@endpush