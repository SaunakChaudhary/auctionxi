@extends('layouts.app')

@section('title', $team->name . ' Squad')
@section('page-title', 'Team Squad')

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
                    <a href="{{ route('team.index', $tournament->id) }}">Teams</a>
                </li>
                <li class="breadcrumb-item active">{{ $team->name }}</li>
            </ol>
        </nav>
    </div>

    {{-- Team Header Card --}}
    <div class="card mb-4"
        style="background:linear-gradient(135deg,#d97706,#f59e0b);
            border:none;overflow:hidden;position:relative;">
        <div
            style="position:absolute;top:-20px;right:-20px;
                width:120px;height:120px;border-radius:50%;
                background:rgba(255,255,255,0.08);">
        </div>
        <div class="card-body p-4 position-relative">
            <div class="row align-items-center g-3">
                <div class="col-auto">
                    @if ($team->logo)
                        <img src="{{ Storage::url($team->logo) }}" alt="{{ $team->name }}"
                            style="width:80px;height:80px;border-radius:20px;
                            object-fit:cover;
                            border:3px solid rgba(255,255,255,0.3);">
                    @else
                        <div
                            style="width:80px;height:80px;border-radius:20px;
                            background:rgba(255,255,255,0.2);
                            display:flex;align-items:center;
                            justify-content:center;
                            font-size:2rem;font-weight:800;color:#fff;">
                            {{ strtoupper(substr($team->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <div class="col">
                    <h2
                        style="color:#fff;font-family:'Poppins',sans-serif;
                           font-weight:800;margin-bottom:6px;">
                        {{ $team->name }}
                    </h2>
                    <div style="color:rgba(255,255,255,0.8);font-size:0.875rem;">
                        <i class="bi bi-person-fill me-1"></i>
                        {{ $team->owner_name }}
                        @if ($team->owner_mobile)
                            &nbsp;&middot;&nbsp;
                            <i class="bi bi-phone me-1"></i>
                            {{ $team->owner_mobile }}
                        @endif
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="row g-2">
                        <div class="col-4">
                            <div class="text-center p-2 rounded-3" style="background:rgba(255,255,255,0.15);">
                                <div
                                    style="font-weight:800;font-size:1.2rem;
                                        color:#fff;">
                                    {{ $team->players->count() }}
                                </div>
                                <div
                                    style="font-size:0.68rem;
                                        color:rgba(255,255,255,0.7);">
                                    Players
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center p-2 rounded-3" style="background:rgba(255,255,255,0.15);">
                                <div
                                    style="font-weight:800;font-size:1rem;
                                        color:#fff;">
                                    ₹{{ number_format($team->spent) }}
                                </div>
                                <div
                                    style="font-size:0.68rem;
                                        color:rgba(255,255,255,0.7);">
                                    Spent
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center p-2 rounded-3" style="background:rgba(255,255,255,0.15);">
                                <div
                                    style="font-weight:800;font-size:1rem;
                                        color:#fff;">
                                    ₹{{ number_format($team->budget - $team->spent) }}
                                </div>
                                <div
                                    style="font-size:0.68rem;
                                        color:rgba(255,255,255,0.7);">
                                    Remaining
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Budget Progress --}}
    <div class="card mb-4">
        <div class="card-body">
            @php
                $pct = $team->budget > 0 ? min(100, ($team->spent / $team->budget) * 100) : 0;
                $barColor = $pct >= 90 ? '#ef4444' : ($pct >= 70 ? '#f59e0b' : '#10b981');
            @endphp
            <div class="d-flex justify-content-between mb-2">
                <span style="font-weight:600;font-size:0.85rem;">
                    Budget Usage
                </span>
                <span style="font-weight:700;font-size:0.85rem;
                         color:{{ $barColor }};">
                    {{ number_format($pct, 1) }}% Used
                </span>
            </div>
            <div style="height:10px;background:#f0f2f8;
                    border-radius:5px;overflow:hidden;">
                <div
                    style="height:100%;width:{{ $pct }}%;
                        background:linear-gradient(90deg,
                        {{ $barColor }},
                        {{ $barColor }}cc);
                        border-radius:5px;">
                </div>
            </div>
            <div class="d-flex justify-content-between mt-2">
                <span style="font-size:0.78rem;color:#9ca3af;">
                    Total Budget: ₹{{ number_format($team->budget) }}
                </span>
                <span style="font-size:0.78rem;color:#9ca3af;">
                    Remaining:
                    ₹{{ number_format($team->budget - $team->spent) }}
                </span>
            </div>
        </div>
    </div>

    {{-- Players Table --}}
    <div class="card">
        <div class="card-header d-flex align-items-center gap-2">
            <i class="bi bi-people-fill text-warning"></i>
            Squad Players
            <span class="ms-auto badge" style="background:#f0f2f8;color:#6b7280;
                     font-size:0.75rem;">
                {{ $team->players->count() }} Players
            </span>
        </div>
        @if ($team->players->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Player</th>
                            <th>Player ID</th>
                            <th>Role</th>
                            <th>City</th>
                            <th>Sold Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($team->players as $player)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if ($player->photo)
                                            <img src="{{ Storage::url($player->photo) }}" class="player-avatar"
                                                alt="{{ $player->name }}">
                                        @else
                                            <div class="player-avatar-placeholder">
                                                {{ strtoupper(substr($player->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div
                                                style="font-weight:600;
                                            font-size:0.875rem;">
                                                {{ $player->name }}
                                            </div>
                                            @if ($player->age)
                                                <small class="text-muted">
                                                    Age: {{ $player->age }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <code
                                        style="background:#f0f2f8;
                                     color:#6c3fc5;
                                     padding:3px 8px;
                                     border-radius:6px;
                                     font-size:0.8rem;
                                     font-weight:700;">
                                        {{ $player->player_id }}
                                    </code>
                                </td>
                                <td>
                                    <span class="badge-status badge-approved">
                                        {{ $player->role }}
                                    </span>
                                </td>
                                <td>
                                    <span style="font-size:0.85rem;color:#6b7280;">
                                        {{ $player->city ?? '—' }}
                                    </span>
                                </td>
                                <td>
                                    <span style="font-weight:700;color:#6c3fc5;">
                                        ₹{{ number_format($player->pivot->sold_price) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background:#f8f9ff;">
                            <td colspan="4" style="font-weight:700;padding:14px 18px;">
                                Total Spent
                            </td>
                            <td
                                style="font-weight:800;color:#6c3fc5;
                               padding:14px 18px;font-size:1rem;">
                                ₹{{ number_format($team->spent) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="card-body text-center py-5">
                <div style="font-size:3rem;margin-bottom:16px;">🏏</div>
                <h6 style="font-weight:700;color:#1e1e2e;">
                    No Players Yet
                </h6>
                <p class="text-muted" style="font-size:0.875rem;">
                    Players will appear here after the auction.
                </p>
            </div>
        @endif
    </div>

@endsection
