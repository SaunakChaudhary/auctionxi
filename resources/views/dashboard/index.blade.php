@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

    {{-- Page Header --}}
    <div class="page-header">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h1>Welcome back, {{ Auth::user()->name }}! 👋</h1>
                <p>Here's what's happening with your tournaments today.</p>
            </div>
            <a href="{{ route('tournament.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle-fill me-2"></i>New Tournament
            </a>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card stat-purple">
                <div class="stat-icon"><i class="bi bi-trophy-fill"></i></div>
                <div class="stat-value">{{ $totalTournaments }}</div>
                <div class="stat-label">Total Tournaments</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card stat-amber">
                <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
                <div class="stat-value">{{ $totalTeams }}</div>
                <div class="stat-label">Total Teams</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card stat-green">
                <div class="stat-icon"><i class="bi bi-person-badge-fill"></i></div>
                <div class="stat-value">{{ $totalPlayers }}</div>
                <div class="stat-label">Registered Players</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card stat-blue">
                <div class="stat-icon"><i class="bi bi-broadcast"></i></div>
                <div class="stat-value">
                    {{ $tournaments->where('auction_status', 'live')->count() }}
                </div>
                <div class="stat-label">Live Auctions</div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-lightning-fill text-warning"></i>
                    Quick Actions
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6 col-md-3">
                            <a href="{{ route('tournament.create') }}"
                                class="d-flex flex-column align-items-center justify-content-center
                                  p-3 rounded-3 text-decoration-none gap-2
                                  quick-action-btn">
                                <div class="quick-icon"
                                    style="background:linear-gradient(135deg,
                                        rgba(108,63,197,0.1),
                                        rgba(139,92,246,0.15));">
                                    <i class="bi bi-trophy-fill" style="color:#6c3fc5;"></i>
                                </div>
                                <span>New Tournament</span>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="{{ route('tournament.index') }}"
                                class="d-flex flex-column align-items-center justify-content-center
                                  p-3 rounded-3 text-decoration-none gap-2
                                  quick-action-btn">
                                <div class="quick-icon"
                                    style="background:linear-gradient(135deg,
                                        rgba(245,158,11,0.1),
                                        rgba(251,191,36,0.15));">
                                    <i class="bi bi-grid-fill" style="color:#d97706;"></i>
                                </div>
                                <span>My Tournaments</span>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            @if ($tournaments->count() > 0)
                                <a href="{{ route('player.index', $tournaments->first()->id) }}"
                                    class="d-flex flex-column align-items-center justify-content-center
                                  p-3 rounded-3 text-decoration-none gap-2
                                  quick-action-btn">
                                @else
                                    <a href="{{ route('tournament.create') }}"
                                        class="d-flex flex-column align-items-center justify-content-center
                                  p-3 rounded-3 text-decoration-none gap-2
                                  quick-action-btn">
                            @endif
                            <div class="quick-icon"
                                style="background:linear-gradient(135deg,
                                        rgba(16,185,129,0.1),
                                        rgba(52,211,153,0.15));">
                                <i class="bi bi-person-badge-fill" style="color:#059669;"></i>
                            </div>
                            <span>Players</span>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            @if ($tournaments->count() > 0)
                                <a href="{{ route('auction.panel', $tournaments->first()->id) }}"
                                    class="d-flex flex-column align-items-center justify-content-center
                                  p-3 rounded-3 text-decoration-none gap-2
                                  quick-action-btn">
                                @else
                                    <a href="{{ route('tournament.create') }}"
                                        class="d-flex flex-column align-items-center justify-content-center
                                  p-3 rounded-3 text-decoration-none gap-2
                                  quick-action-btn">
                            @endif
                            <div class="quick-icon"
                                style="background:linear-gradient(135deg,
                                        rgba(239,68,68,0.1),
                                        rgba(252,165,165,0.15));">
                                <i class="bi bi-broadcast" style="color:#dc2626;"></i>
                            </div>
                            <span>Auction Panel</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tournaments Table --}}
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-trophy-fill text-warning"></i>
                My Tournaments
            </div>
            <a href="{{ route('tournament.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus me-1"></i>New
            </a>
        </div>
        <div class="card-body p-0">
            @if ($tournaments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Tournament</th>
                                <th>Code</th>
                                <th>Teams</th>
                                <th>Players</th>
                                <th>Budget</th>
                                <th>Registration</th>
                                <th>Auction</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tournaments as $tournament)
                                <tr>
                                    {{-- Name + location --}}
                                    <td>
                                        <div class="fw-600" style="font-weight:600;">
                                            {{ $tournament->name }}
                                        </div>
                                        @if ($tournament->location)
                                            <small class="text-muted">
                                                <i class="bi bi-geo-alt-fill me-1"></i>
                                                {{ $tournament->location }}
                                            </small>
                                        @endif
                                    </td>

                                    {{-- Code --}}
                                    <td>
                                        <code class="px-2 py-1 rounded"
                                            style="background:#f0f2f8;
                                         font-size:0.8rem;
                                         color:#6c3fc5;
                                         font-weight:700;">
                                            {{ $tournament->code }}
                                        </code>
                                    </td>

                                    {{-- Teams --}}
                                    <td>
                                        <span class="fw-600">
                                            {{ $tournament->teams_count }}
                                        </span>
                                        <small class="text-muted">
                                            / {{ $tournament->number_of_teams }}
                                        </small>
                                    </td>

                                    {{-- Players --}}
                                    <td>
                                        <span class="fw-600">
                                            {{ $tournament->players_count }}
                                        </span>
                                    </td>

                                    {{-- Budget --}}
                                    <td>
                                        <span class="fw-600">
                                            ₹{{ number_format($tournament->team_budget) }}
                                        </span>
                                    </td>

                                    {{-- Registration --}}
                                    <td>
                                        <span
                                            class="badge-status
                                badge-{{ $tournament->registration_status }}">
                                            {{ ucfirst($tournament->registration_status) }}
                                        </span>
                                    </td>

                                    {{-- Auction --}}
                                    <td>
                                        <span
                                            class="badge-status
                                badge-{{ $tournament->auction_status }}">
                                            {{ ucfirst($tournament->auction_status) }}
                                        </span>
                                    </td>

                                    {{-- Actions --}}
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('tournament.show', $tournament->id) }}"
                                                class="btn btn-sm btn-outline-primary" title="Manage">
                                                <i class="bi bi-gear-fill"></i>
                                            </a>
                                            <a href="{{ route('auction.panel', $tournament->id) }}"
                                                class="btn btn-sm btn-warning" title="Auction Panel">
                                                <i class="bi bi-broadcast"></i>
                                            </a>
                                            <a href="{{ route('public.live', $tournament->code) }}"
                                                target="_blank" class="btn btn-sm btn-success" title="Live View">
                                                <i class="bi bi-eye-fill"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                {{-- Empty state --}}
                <div class="text-center py-5">
                    <div style="font-size:3.5rem; margin-bottom:16px;">🏏</div>
                    <h5 style="font-weight:700; color:#1e1e2e; margin-bottom:8px;">
                        No Tournaments Yet
                    </h5>
                    <p class="text-muted mb-4" style="font-size:0.9rem;">
                        Create your first cricket tournament and start<br>
                        managing your auction like a pro.
                    </p>
                    <a href="{{ route('tournament.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle-fill me-2"></i>
                        Create First Tournament
                    </a>
                </div>
            @endif
        </div>
    </div>

@endsection

@push('styles')
    <style>
        .quick-action-btn {
            background: #f8f9ff;
            border: 1.5px solid #f0f2f8;
            transition: all 0.2s;
            cursor: pointer;
            color: #374151;
            font-size: 0.82rem;
            font-weight: 600;
        }

        .quick-action-btn:hover {
            background: #fff;
            border-color: #6c3fc5;
            color: #6c3fc5;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(108, 63, 197, 0.1);
        }

        .quick-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
    </style>
@endpush
