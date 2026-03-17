@extends('layouts.public')

@section('title', $team->name . ' — Squad')

@section('nav-items')
    <a href="{{ route('public.live', $tournament->code) }}" class="nav-pill active">
        <i class="bi bi-broadcast me-1"></i>Live Auction
    </a>
@endsection

@section('content')

    {{-- Team Header --}}
    <div class="card mb-4"
        style="background:linear-gradient(135deg,#d97706,#f59e0b);
            border:none;overflow:hidden;position:relative;">
        <div
            style="position:absolute;top:-20px;right:-20px;
                width:120px;height:120px;border-radius:50%;
                background:rgba(255,255,255,0.08);">
        </div>
        <div class="card-body p-4 position-relative">
            <div class="d-flex align-items-center gap-4 flex-wrap">
                @if ($team->logo)
                    <img src="{{ teamLogoSrc($team) }}"
                        style="width:80px;height:80px;border-radius:20px;
                        object-fit:cover;
                        border:3px solid rgba(255,255,255,0.3);">
                @else
                    <div
                        style="width:80px;height:80px;border-radius:20px;
                        background:rgba(255,255,255,0.2);
                        display:flex;align-items:center;
                        justify-content:center;font-size:2.2rem;
                        font-weight:800;color:#fff;">
                        {{ strtoupper(substr($team->name, 0, 1)) }}
                    </div>
                @endif
                <div class="flex-fill">
                    <h2
                        style="color:#fff;font-family:'Poppins',sans-serif;
                           font-weight:800;margin-bottom:6px;">
                        {{ $team->name }}
                    </h2>
                    <div style="color:rgba(255,255,255,0.8);
                            font-size:0.875rem;">
                        <i class="bi bi-person-fill me-1"></i>
                        {{ $team->owner_name }}
                    </div>
                </div>
                <div class="d-flex gap-3 flex-wrap">
                    <div class="text-center px-3 py-2 rounded-3" style="background:rgba(255,255,255,0.15);">
                        <div style="font-weight:800;font-size:1.3rem;
                                color:#fff;">
                            {{ $team->players->count() }}
                        </div>
                        <div style="font-size:0.7rem;
                                color:rgba(255,255,255,0.7);">
                            Players
                        </div>
                    </div>
                    <div class="text-center px-3 py-2 rounded-3" style="background:rgba(255,255,255,0.15);">
                        <div style="font-weight:800;font-size:1.1rem;
                                color:#fff;">
                            ₹{{ number_format($team->spent) }}
                        </div>
                        <div style="font-size:0.7rem;
                                color:rgba(255,255,255,0.7);">
                            Spent
                        </div>
                    </div>
                    <div class="text-center px-3 py-2 rounded-3" style="background:rgba(255,255,255,0.15);">
                        <div style="font-weight:800;font-size:1.1rem;
                                color:#fff;">
                            ₹{{ number_format($team->budget - $team->spent) }}
                        </div>
                        <div style="font-size:0.7rem;
                                color:rgba(255,255,255,0.7);">
                            Remaining
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Back Button --}}
    <div class="mb-4">
        <a href="{{ route('public.live', $tournament->code) }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>
            Back to Live Auction
        </a>
    </div>

    {{-- Squad Table --}}
    <div class="card">
        <div class="card-header d-flex align-items-center gap-2">
            <i class="bi bi-people-fill text-warning"></i>
            Team Squad
            <span class="ms-auto" style="font-size:0.8rem;color:#9ca3af;">
                {{ $team->players->count() }} Players
            </span>
        </div>
        @if ($team->players->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Player</th>
                            <th>Player ID</th>
                            <th>Role</th>
                            <th>Sold Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($team->players as $i => $player)
                            <tr>
                                <td style="color:#9ca3af;">{{ $i + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @php
                                            $__src = playerAvatarSrc($player);
                                            $__initial = strtoupper(substr($player->name ?? '?', 0, 1));
                                            $__bg = playerAvatarColor($player->name ?? '');
                                        @endphp
                                        @if ($__src)
                                            <img src="{{ $__src }}" alt="{{ $player->name }}"
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
                                        <div>
                                            <div
                                                style="font-weight:600;
                                            font-size:0.875rem;">
                                                {{ $player->name }}
                                            </div>
                                            @if ($player->city)
                                                <small class="text-muted">
                                                    {{ $player->city }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <code
                                        style="background:#f0f2f8;color:#6c3fc5;
                                     padding:3px 8px;border-radius:6px;
                                     font-size:0.8rem;font-weight:700;">
                                        {{ $player->player_id }}
                                    </code>
                                </td>
                                <td>
                                    <span class="badge-approved badge-status" style="font-size:0.7rem;">
                                        {{ $player->role }}
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
                <div style="font-size:2.5rem;margin-bottom:12px;">🏏</div>
                <h6 style="font-weight:700;color:#374151;">No Players Yet</h6>
                <p class="text-muted" style="font-size:0.875rem;">
                    Players will appear here after the auction.
                </p>
            </div>
        @endif
    </div>

@endsection
