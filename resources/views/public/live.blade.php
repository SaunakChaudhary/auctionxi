@extends('layouts.public')

@section('title', 'Live — ' . $tournament->name)

@section('nav-items')
    <a href="#teams" class="nav-pill">Teams</a>
    <a href="{{ route('public.search.page', $tournament->code) }}" class="nav-pill">Search Player</a>
@endsection

@section('refresh-bar')
    <div class="refresh-bar">
        <i class="bi bi-arrow-clockwise me-1"></i>
        Auto-refreshes in <strong id="cd">15</strong>s
        <button onclick="location.reload()">Refresh Now</button>
    </div>
@endsection

@section('content')

    {{-- Tournament Info Bar --}}
    <div class="card mb-4">
        <div class="card-body" style="padding:20px 24px;">
            <div class="row align-items-center g-3">
                <div class="col-lg-7">
                    <div style="font-size:0.72rem;font-weight:600;color:var(--muted);
                                text-transform:uppercase;letter-spacing:1px;margin-bottom:6px;">
                        Cricket Player Auction
                    </div>
                    <h1 style="font-family:'Poppins',sans-serif;font-weight:800;
                               font-size:clamp(1.3rem,3vw,1.8rem);color:var(--dark);margin:0 0 8px;">
                        {{ $tournament->name }}
                    </h1>
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        @if($tournament->location)
                            <span style="font-size:0.82rem;color:var(--muted);">
                                <i class="bi bi-geo-alt me-1"></i>{{ $tournament->location }}
                            </span>
                        @endif
                        @if($tournament->auction_date)
                            <span style="font-size:0.82rem;color:var(--muted);">
                                <i class="bi bi-calendar3 me-1"></i>
                                {{ \Carbon\Carbon::parse($tournament->auction_date)->format('d M Y') }}
                            </span>
                        @endif
                        @php
                            $statusMap = [
                                'pending'   => ['label' => 'Not Started', 'class' => 'status-pending-auction'],
                                'live'      => ['label' => 'Live',        'class' => 'status-live'],
                                'completed' => ['label' => 'Completed',   'class' => 'status-completed'],
                            ];
                            $st = $statusMap[$tournament->auction_status] ?? $statusMap['pending'];
                        @endphp
                        <span class="status-badge {{ $st['class'] }}">{{ $st['label'] }}</span>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="row g-2">
                        <div class="col-4">
                            <div class="stat-box">
                                <div class="stat-num">{{ $totalPlayers }}</div>
                                <div class="stat-lbl">Players</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-box" style="border-color:#bbf7d0;">
                                <div class="stat-num" style="color:var(--success);">{{ $soldCount }}</div>
                                <div class="stat-lbl">Sold</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-box">
                                <div class="stat-num" style="color:var(--muted);">{{ $teams->count() }}</div>
                                <div class="stat-lbl">Teams</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Search Player Button (goes to separate page) --}}
    <div class="card mb-4">
        <div class="card-body" style="padding:18px 24px;">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <div style="font-weight:700;font-size:0.95rem;color:var(--dark);margin-bottom:3px;">
                        <i class="bi bi-search me-2" style="color:var(--primary-lt);"></i>
                        Search Players
                    </div>
                    <div style="font-size:0.78rem;color:var(--muted);">
                        Search by Player ID or Name — view profile, status, team
                    </div>
                </div>
                <a href="{{ route('public.search.page', $tournament->code) }}"
                   class="btn btn-primary"
                   style="white-space:nowrap;font-weight:600;padding:10px 24px;">
                    <i class="bi bi-person-search me-2"></i>Search Player
                </a>
            </div>
        </div>
    </div>

    {{-- Teams Grid ──────────────────────────────── --}}
    <div id="teams">
        <div style="font-family:'Poppins',sans-serif;font-weight:700;font-size:1.1rem;
                    color:var(--dark);margin-bottom:16px;">
            <i class="bi bi-people-fill me-2" style="color:var(--primary-lt);"></i>
            Teams & Budget Status
        </div>

        @if($teams->count() > 0)
            <div class="row g-3">
                @foreach($teams as $team)
                    @php
                        $remaining = $team->budget - $team->spent;
                        $pct       = $team->budget > 0 ? min(100, ($team->spent / $team->budget) * 100) : 0;
                        $barColor  = $pct >= 90 ? '#dc2626' : ($pct >= 70 ? '#d97706' : '#16a34a');
                        $teamPlayers = $team->players;
                    @endphp
                    <div class="col-lg-4 col-md-6">
                        <a href="{{ route('public.team.squad', [$tournament->code, $team->id]) }}"
                           style="text-decoration:none;display:block;">
                            <div class="lv-team-card">
                                {{-- Team Header --}}
                                <div class="lv-team-header">
                                    @if($team->logo)
                                        <img src="{{ teamLogoSrc($team) }}"
                                             style="width:52px;height:52px;border-radius:10px;
                                                    object-fit:cover;border:2px solid rgba(255,255,255,0.2);
                                                    flex-shrink:0;">
                                    @else
                                        <div style="width:52px;height:52px;border-radius:10px;
                                                    background:rgba(255,255,255,0.15);display:flex;
                                                    align-items:center;justify-content:center;
                                                    font-weight:800;font-size:1.3rem;color:#fff;
                                                    flex-shrink:0;">
                                            {{ strtoupper(substr($team->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div style="flex:1;min-width:0;">
                                        <div class="lv-team-name">{{ $team->name }}</div>
                                        @if($team->owner_name)
                                            <div style="font-size:0.72rem;color:rgba(255,255,255,0.65);margin-top:1px;">
                                                {{ $team->owner_name }}
                                            </div>
                                        @endif
                                    </div>
                                    <div style="text-align:right;flex-shrink:0;">
                                        <div style="font-size:0.62rem;color:rgba(255,255,255,0.6);
                                                    text-transform:uppercase;letter-spacing:0.5px;">
                                            Players
                                        </div>
                                        <div style="font-weight:800;font-size:1.3rem;color:#fff;">
                                            {{ $teamPlayers->count() }}
                                        </div>
                                    </div>
                                </div>

                                {{-- Budget Numbers --}}
                                <div class="lv-budget-row">
                                    <div class="lv-budget-box">
                                        <div class="lv-budget-lbl">Total Budget</div>
                                        <div class="lv-budget-val">₹{{ number_format($team->budget) }}</div>
                                    </div>
                                    <div class="lv-budget-box">
                                        <div class="lv-budget-lbl">Spent</div>
                                        <div class="lv-budget-val" style="color:#fbbf24;">
                                            ₹{{ number_format($team->spent) }}
                                        </div>
                                    </div>
                                    <div class="lv-budget-box">
                                        <div class="lv-budget-lbl">Remaining</div>
                                        <div class="lv-budget-val"
                                             style="color:{{ $remaining <= 0 ? '#f87171' : '#4ade80' }};">
                                            ₹{{ number_format($remaining) }}
                                        </div>
                                    </div>
                                </div>

                                {{-- Budget Bar --}}
                                <div style="height:6px;background:rgba(255,255,255,0.15);
                                            border-radius:3px;overflow:hidden;margin-top:4px;">
                                    <div style="height:100%;width:{{ $pct }}%;background:{{ $barColor }};
                                                border-radius:3px;transition:width 0.3s;"></div>
                                </div>
                                <div style="font-size:0.65rem;color:rgba(255,255,255,0.5);
                                            margin-top:4px;text-align:right;">
                                    {{ round($pct) }}% spent
                                </div>

                                {{-- Recent Players --}}
                                @if($teamPlayers->count() > 0)
                                    <div class="lv-players-row">
                                        @foreach($teamPlayers->take(6) as $tp)
                                            @php
                                                $__src = playerAvatarSrc($tp);
                                                $__bg  = playerAvatarColor($tp->name ?? '');
                                                $__ini = strtoupper(substr($tp->name ?? '?', 0, 1));
                                            @endphp
                                            @if($__src)
                                                <img src="{{ $__src }}"
                                                     title="{{ $tp->name }}"
                                                     style="width:28px;height:28px;border-radius:50%;
                                                            object-fit:cover;border:2px solid rgba(255,255,255,0.3);"
                                                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                                                <div style="display:none;width:28px;height:28px;border-radius:50%;
                                                            background:{{ $__bg }};color:#fff;font-weight:700;
                                                            font-size:0.6rem;align-items:center;
                                                            justify-content:center;">
                                                    {{ $__ini }}
                                                </div>
                                            @else
                                                <div title="{{ $tp->name }}"
                                                     style="width:28px;height:28px;border-radius:50%;
                                                            background:{{ $__bg }};color:#fff;font-weight:700;
                                                            font-size:0.6rem;display:flex;align-items:center;
                                                            justify-content:center;border:2px solid rgba(255,255,255,0.2);">
                                                    {{ $__ini }}
                                                </div>
                                            @endif
                                        @endforeach
                                        @if($teamPlayers->count() > 6)
                                            <div style="width:28px;height:28px;border-radius:50%;
                                                        background:rgba(255,255,255,0.15);color:rgba(255,255,255,0.8);
                                                        font-weight:700;font-size:0.6rem;display:flex;
                                                        align-items:center;justify-content:center;">
                                                +{{ $teamPlayers->count() - 6 }}
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div style="font-size:0.72rem;color:rgba(255,255,255,0.4);
                                                margin-top:10px;text-align:center;padding:6px 0;">
                                        No players yet
                                    </div>
                                @endif
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <div class="card">
                <div class="card-body text-center py-5">
                    <div style="font-size:2.5rem;margin-bottom:12px;">🏏</div>
                    <h5 style="font-weight:700;margin-bottom:6px;">No Teams Yet</h5>
                    <p style="color:var(--muted);font-size:0.875rem;">Teams will appear here once created.</p>
                </div>
            </div>
        @endif
    </div>

@endsection

@push('styles')
<style>
.lv-team-card {
    background: linear-gradient(135deg, #1e3a5f, #2563eb);
    border-radius: 14px;
    padding: 18px;
    transition: transform 0.15s, box-shadow 0.15s;
    height: 100%;
}
.lv-team-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 30px rgba(37,99,235,0.25);
}
.lv-team-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 14px;
    padding-bottom: 14px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}
.lv-team-name {
    font-family: 'Poppins', sans-serif;
    font-weight: 700;
    font-size: 0.95rem;
    color: #fff;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.lv-budget-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
    margin-bottom: 10px;
}
.lv-budget-box {
    text-align: center;
}
.lv-budget-lbl {
    font-size: 0.6rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: rgba(255,255,255,0.5);
    margin-bottom: 2px;
}
.lv-budget-val {
    font-family: 'Poppins', sans-serif;
    font-weight: 700;
    font-size: 0.82rem;
    color: #fff;
}
.lv-players-row {
    display: flex;
    align-items: center;
    gap: 4px;
    margin-top: 10px;
    flex-wrap: wrap;
}
</style>
@endpush

@push('scripts')
<script>
'use strict';
let s = 15;
const cdEl = document.getElementById('cd');
setInterval(() => {
    s--;
    if (cdEl) cdEl.textContent = s;
    if (s <= 0) location.reload();
}, 1000);
</script>
@endpush