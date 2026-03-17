@extends('layouts.app')

@section('title', 'Teams')
@section('page-title', 'Team Management')

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
                <li class="breadcrumb-item active">Teams</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center
                justify-content-between flex-wrap gap-3 mt-2">
            <div>
                <h1>Team Management</h1>
                <p>{{ $tournament->name }} &mdash;
                    {{ $teams->count() }}/{{ $tournament->number_of_teams }}
                    teams created</p>
            </div>
            <a href="{{ route('team.create', $tournament->id) }}" class="btn btn-primary">
                <i class="bi bi-plus-circle-fill me-2"></i>Add Team
            </a>
        </div>
    </div>

    @if ($teams->count() > 0)
        <div class="row g-4">
            @foreach ($teams as $team)
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 team-card">

                        {{-- Team Header --}}
                        <div class="card-body pb-0">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                {{-- Logo --}}
                                @if (teamLogoSrc($team))
                                    <img src="{{ teamLogoSrc($team) }}" alt="{{ $team->name }}" class="team-logo">
                                @else
                                    <div class="team-logo-placeholder">
                                        {{ strtoupper(substr($team->name, 0, 1)) }}
                                    </div>
                                @endif

                                <div class="flex-fill">
                                    <h5
                                        style="font-weight:700;font-size:1rem;
                                   margin-bottom:3px;color:#1e1e2e;">
                                        {{ $team->name }}
                                    </h5>
                                    <div style="font-size:0.8rem;color:#6b7280;">
                                        <i class="bi bi-person-fill me-1"></i>
                                        {{ $team->owner_name }}
                                    </div>
                                    @if ($team->owner_mobile)
                                        <div style="font-size:0.78rem;color:#9ca3af;">
                                            <i class="bi bi-phone me-1"></i>
                                            {{ $team->owner_mobile }}
                                        </div>
                                    @endif
                                </div>

                                <span class="badge-status badge-approved" style="font-size:0.68rem;">
                                    {{ $team->players_count ?? 0 }} Players
                                </span>
                            </div>

                            {{-- Budget Bar --}}
                            <div class="mb-3">
                                <div
                                    class="d-flex justify-content-between
                                align-items-center mb-1">
                                    <span
                                        style="font-size:0.75rem;font-weight:600;
                                     color:#6b7280;">Budget
                                        Used</span>
                                    <span
                                        style="font-size:0.75rem;font-weight:700;
                                     color:#1e1e2e;">
                                        ₹{{ number_format($team->spent) }}
                                        / ₹{{ number_format($team->budget) }}
                                    </span>
                                </div>
                                @php
                                    $pct = $team->budget > 0 ? min(100, ($team->spent / $team->budget) * 100) : 0;
                                    $barColor = $pct >= 90 ? '#ef4444' : ($pct >= 70 ? '#f59e0b' : '#10b981');
                                @endphp
                                <div
                                    style="height:6px;background:#f0f2f8;
                                border-radius:3px;overflow:hidden;">
                                    <div
                                        style="height:100%;width:{{ $pct }}%;
                                    background:{{ $barColor }};
                                    border-radius:3px;
                                    transition:width 0.5s ease;">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <span style="font-size:0.7rem;color:#9ca3af;">
                                        Spent: ₹{{ number_format($team->spent) }}
                                    </span>
                                    <span
                                        style="font-size:0.7rem;
                                     color:{{ $barColor }};
                                     font-weight:600;">
                                        Remaining:
                                        ₹{{ number_format($team->budget - $team->spent) }}
                                    </span>
                                </div>
                            </div>

                            @if ($team->description)
                                <p
                                    style="font-size:0.82rem;color:#9ca3af;
                          margin-bottom:16px;line-height:1.5;">
                                    {{ Str::limit($team->description, 80) }}
                                </p>
                            @endif
                        </div>

                        {{-- Card Footer --}}
                        <div class="card-body pt-0">
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('team.squad', [$tournament->id, $team->id]) }}"
                                    class="btn btn-sm btn-primary flex-fill">
                                    <i class="bi bi-people-fill me-1"></i>Squad
                                </a>
                                <a href="{{ route('team.edit', [$tournament->id, $team->id]) }}"
                                    class="btn btn-sm btn-outline-primary flex-fill">
                                    <i class="bi bi-pencil-fill me-1"></i>Edit
                                </a>
                                <form method="POST" action="{{ route('team.destroy', [$tournament->id, $team->id]) }}"
                                    onsubmit="return confirm(
                              'Delete {{ $team->name }}? This cannot be undone.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <div style="font-size:3.5rem;margin-bottom:16px;">👥</div>
                <h5 style="font-weight:700;color:#1e1e2e;margin-bottom:8px;">
                    No Teams Yet
                </h5>
                <p class="text-muted mb-4">
                    Add teams to get started with
                    {{ $tournament->name }}.
                </p>
                <a href="{{ route('team.create', $tournament->id) }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle-fill me-2"></i>Add First Team
                </a>
            </div>
        </div>
    @endif

@endsection

@push('styles')
    <style>
        .team-card {
            border: 1.5px solid #f0f2f8 !important;
            transition: all 0.25s;
        }

        .team-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.1) !important;
            border-color: rgba(108, 63, 197, 0.2) !important;
        }
    </style>
@endpush
