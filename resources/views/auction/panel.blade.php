@extends('layouts.app')

@section('title', 'Auction Panel — ' . $tournament->name)
@section('page-title', 'Auction Panel')

@section('content')

{{-- ── PAGE HEADER ── --}}
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
            <li class="breadcrumb-item active">Auction Panel</li>
        </ol>
    </nav>
    <div class="ap-header-row">
        <div>
            <h1 class="ap-title">
                <span class="ap-title-icon">🎙</span>
                Auction Control Panel
            </h1>
            <p class="ap-subtitle">{{ $tournament->name }}</p>
        </div>
        <div class="ap-header-actions">
            <form method="POST"
                  action="{{ route('auction.updateStatus', $tournament->id) }}"
                  style="display:inline;">
                @csrf
                @if($tournament->auction_status === 'pending')
                    <input type="hidden" name="status" value="live">
                    <button type="submit" class="ap-btn ap-btn-live">
                        <i class="bi bi-broadcast"></i> Start Auction
                    </button>
                @elseif($tournament->auction_status === 'live')
                    <input type="hidden" name="status" value="completed">
                    <button type="submit" class="ap-btn ap-btn-end">
                        <i class="bi bi-stop-circle-fill"></i> End Auction
                    </button>
                @else
                    <span class="ap-completed-badge">
                        <i class="bi bi-check-circle-fill"></i> Completed
                    </span>
                @endif
            </form>
            <a href="{{ route('public.live', $tournament->code) }}"
               target="_blank" class="ap-btn ap-btn-ghost">
                <i class="bi bi-eye"></i> Live View
            </a>
            <a href="{{ route('auction.results', $tournament->id) }}"
               class="ap-btn ap-btn-results">
                <i class="bi bi-bar-chart-line-fill"></i> Results
            </a>
        </div>
    </div>
</div>

{{-- ── LIVE BANNER ── --}}
@if($tournament->auction_status === 'live')
<div class="ap-live-banner">
    <div class="ap-live-dot"></div>
    <div>
        <strong>Auction is LIVE</strong>
        <span class="ap-live-url">
            Viewer link:
            <a href="{{ route('public.live', $tournament->code) }}"
               target="_blank">
                {{ route('public.live', $tournament->code) }}
            </a>
        </span>
    </div>
</div>
@endif

{{-- ── STATS ROW ── --}}
@php
    $totalPlayers = $players->count();
    $soldCount    = $players->where('status','sold')->count();
    $unsoldCount  = $players->where('status','unsold')->count();
    $remaining    = $players->where('status','approved')->count();
@endphp

<div class="ap-stats-row">
    <div class="ap-stat-card ap-stat-total">
        <div class="ap-stat-num">{{ $totalPlayers }}</div>
        <div class="ap-stat-lbl">Total Players</div>
    </div>
    <div class="ap-stat-card ap-stat-sold">
        <div class="ap-stat-num">{{ $soldCount }}</div>
        <div class="ap-stat-lbl">Sold</div>
    </div>
    <div class="ap-stat-card ap-stat-unsold">
        <div class="ap-stat-num">{{ $unsoldCount }}</div>
        <div class="ap-stat-lbl">Unsold</div>
    </div>
    <div class="ap-stat-card ap-stat-remaining">
        <div class="ap-stat-num">{{ $remaining }}</div>
        <div class="ap-stat-lbl">Remaining</div>
    </div>
</div>

{{-- ── MAIN LAYOUT ── --}}
<div class="ap-layout">

    {{-- ════════════════════════════════
         LEFT COLUMN — AUCTION CONTROL
         ════════════════════════════════ --}}
    <div class="ap-left">
        <div class="ap-panel-card">

            {{-- Panel Title --}}
            <div class="ap-panel-header">
                <div class="ap-panel-title">
                    <i class="bi bi-search"></i>
                    Player Search & Assignment
                </div>
            </div>

            <div class="ap-panel-body">

                {{-- ── SEARCH BAR ── --}}
                <div class="ap-search-wrap">
                    <input type="text"
                           id="pidInput"
                           class="ap-search-input"
                           placeholder="Enter Player ID — e.g. PX1001"
                           autocomplete="off"
                           spellcheck="false">
                    <button type="button"
                            id="btnSearch"
                            class="ap-search-btn">
                        <i class="bi bi-search"></i>
                        Search
                    </button>
                    <button type="button"
                            id="btnClear"
                            class="ap-clear-btn"
                            title="Clear">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                {{-- ── PREV / NEXT NAV ── --}}
                <div class="ap-nav-bar">
                    <button type="button"
                            id="btnPrev"
                            class="ap-nav-btn"
                            disabled>
                        <i class="bi bi-chevron-left"></i>
                        Prev
                    </button>
                    <div class="ap-nav-counter" id="navCounter">
                        @if($totalPlayers > 0)
                            Player 1 of {{ $totalPlayers }}
                        @else
                            No players registered
                        @endif
                    </div>
                    <button type="button"
                            id="btnNext"
                            class="ap-nav-btn"
                            {{ $totalPlayers > 1 ? '' : 'disabled' }}>
                        Next
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>

                {{-- ── BUDGET WARNING ── --}}
                <div id="budgetAlert" class="ap-budget-alert" style="display:none;">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <span id="budgetAlertMsg"></span>
                </div>

                {{-- ── NOT FOUND ── --}}
                <div id="stateNotFound" class="ap-state" style="display:none;">
                    <div class="ap-state-icon">🔍</div>
                    <div class="ap-state-title">Player Not Found</div>
                    <div class="ap-state-desc">
                        No player with that ID exists in this tournament
                    </div>
                </div>

                {{-- ── EMPTY STATE ── --}}
                <div id="stateEmpty"
                     class="ap-state"
                     style="{{ $totalPlayers > 0 ? 'display:none;' : '' }}">
                    <div class="ap-state-icon">🏏</div>
                    <div class="ap-state-title">No Players Yet</div>
                    <div class="ap-state-desc">
                        Players will appear here once they register
                    </div>
                </div>

                {{-- ── PLAYER CARD ── --}}
                <div id="playerCard"
                     style="{{ $firstPlayer ? '' : 'display:none;' }}">

                    {{-- Profile --}}
                    <div class="ap-profile">
                        <div class="ap-avatar-wrap">
                            <div id="avatarImg" style="display:none;">
                                <img id="avatarPhoto"
                                     src=""
                                     class="ap-avatar-img"
                                     alt="Player">
                            </div>
                            <div id="avatarPlaceholder"
                                 class="ap-avatar-placeholder">
                                <span id="avatarInitial">
                                    {{ $firstPlayer ? strtoupper(substr($firstPlayer->name,0,1)) : '' }}
                                </span>
                            </div>
                        </div>
                        <div class="ap-profile-info">
                            <div class="ap-profile-name-row">
                                <h3 id="pName" class="ap-profile-name">
                                    {{ $firstPlayer->name ?? '' }}
                                </h3>
                                <span id="pIdBadge" class="ap-pid-badge">
                                    {{ $firstPlayer->player_id ?? '' }}
                                </span>
                            </div>
                            <div id="pRole" class="ap-profile-role">
                                {{ $firstPlayer->role ?? '' }}
                            </div>
                            <div class="ap-profile-status-row">
                                <span id="pStatusBadge"
                                      class="ap-status-badge ap-status-{{ $firstPlayer->status ?? 'approved' }}">
                                    {{ strtoupper($firstPlayer->status ?? 'APPROVED') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Details Grid --}}
                    <div class="ap-details-grid" id="detailsGrid">
                        @if($firstPlayer)
                        @php
                            $dg = [
                                ['Mobile',     $firstPlayer->mobile         ?? '—'],
                                ['City',       $firstPlayer->city           ?? '—'],
                                ['Age',        $firstPlayer->age            ?? '—'],
                                ['Jersey',     $firstPlayer->jersey_number  ?? '—'],
                                ['Batting',    $firstPlayer->batting_style  ?? '—'],
                                ['Bowling',    $firstPlayer->bowling_style  ?? '—'],
                                ['Experience', $firstPlayer->experience     ?? '—'],
                                ['Base Price', $firstPlayer->base_price > 0
                                               ? '₹'.number_format($firstPlayer->base_price)
                                               : '—'],
                            ];
                        @endphp
                        @foreach($dg as $item)
                        <div class="ap-detail-box">
                            <div class="ap-detail-lbl">{{ $item[0] }}</div>
                            <div class="ap-detail-val">{{ $item[1] }}</div>
                        </div>
                        @endforeach
                        @endif
                    </div>

                    {{-- Already Assigned Warning --}}
                    <div id="assignedWarning"
                         class="ap-assigned-warn"
                         style="display:{{ ($firstPlayer && $firstPlayer->status === 'sold') ? 'flex' : 'none' }};">
                        <div class="ap-assigned-warn-text">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <div>
                                <strong>Already Assigned</strong>
                                <span>— use Edit Assignment to fix mistakes</span>
                            </div>
                        </div>
                        <button type="button"
                                id="btnOpenEdit"
                                class="ap-btn-edit-assign">
                            <i class="bi bi-pencil-fill"></i> Edit
                        </button>
                    </div>

                    {{-- ── ASSIGN FORM ── --}}
                    <div class="ap-assign-form" id="assignForm">

                        <div class="ap-assign-title">
                            <i class="bi bi-hammer"></i>
                            Assign Player to Team
                        </div>

                        {{-- Status + Price Row --}}
                        <div class="ap-form-row">
                            <div class="ap-form-group">
                                <label class="ap-label">
                                    Result <span class="ap-required">*</span>
                                </label>
                                <select id="selStatus" class="ap-select">
                                    <option value="sold">✅ Sold</option>
                                    <option value="unsold">❌ Unsold</option>
                                </select>
                            </div>
                            <div class="ap-form-group" id="priceGroup">
                                <label class="ap-label">
                                    Final Price (₹)
                                    <span class="ap-required">*</span>
                                </label>
                                <input type="number"
                                       id="soldPrice"
                                       class="ap-input"
                                       placeholder="Enter bid amount"
                                       min="0"
                                       value="{{ $firstPlayer && $firstPlayer->base_price > 0 ? $firstPlayer->base_price : '' }}">
                                <div id="priceHint" class="ap-hint">
                                    @if($firstPlayer && $firstPlayer->base_price > 0)
                                        Base: ₹{{ number_format($firstPlayer->base_price) }}
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Team Grid --}}
                        <div id="teamSection">
                            <label class="ap-label" style="margin-bottom:10px;display:block;">
                                Select Team <span class="ap-required">*</span>
                            </label>
                            <div class="ap-team-grid" id="teamGrid">
                                @forelse($tournament->teams as $team)
                                <div class="ap-team-card"
                                     data-team-id="{{ $team->id }}"
                                     data-team-name="{{ addslashes($team->name) }}"
                                     data-remaining="{{ $team->budget - $team->spent }}">
                                    @if($team->logo)
                                        <img src="{{ Storage::url($team->logo) }}"
                                             class="ap-team-logo"
                                             alt="{{ $team->name }}">
                                    @else
                                        <div class="ap-team-logo-placeholder">
                                            {{ strtoupper(substr($team->name,0,1)) }}
                                        </div>
                                    @endif
                                    <div class="ap-team-name">{{ $team->name }}</div>
                                    <div class="ap-team-budget
                                         {{ ($team->budget - $team->spent) <= 0 ? 'ap-budget-zero' : '' }}"
                                         id="teamBudgetDisplay_{{ $team->id }}">
                                        ₹{{ number_format($team->budget - $team->spent) }}
                                    </div>
                                </div>
                                @empty
                                <div style="grid-column:1/-1;text-align:center;
                                            padding:20px;color:#9ca3af;font-size:0.875rem;">
                                    No teams created yet.
                                    <a href="{{ route('team.create', $tournament->id) }}">
                                        Add teams first
                                    </a>
                                </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- Hidden fields --}}
                        <input type="hidden" id="selectedPlayerId"
                               value="{{ $firstPlayer->id ?? '' }}">
                        <input type="hidden" id="selectedTeamId" value="">
                        <input type="hidden" id="selectedTeamRemaining" value="0">

                        {{-- Assign Button --}}
                        <button type="button"
                                id="btnAssign"
                                class="ap-assign-btn">
                            <i class="bi bi-check-circle-fill"></i>
                            Assign Player
                        </button>
                    </div>
                    {{-- END ASSIGN FORM --}}

                </div>
                {{-- END PLAYER CARD --}}

            </div>
        </div>
    </div>
    {{-- END LEFT COLUMN --}}

    {{-- ════════════════════════════════
         RIGHT COLUMN — SIDEBAR
         ════════════════════════════════ --}}
    <div class="ap-right">

        {{-- Recent Results --}}
        <div class="ap-side-card">
            <div class="ap-side-header">
                <span><i class="bi bi-clock-history"></i> Recent Results</span>
                <a href="{{ route('auction.results', $tournament->id) }}"
                   class="ap-side-link">View All</a>
            </div>
            @php
                $recentResults = \App\Models\AuctionResult
                    ::where('tournament_id', $tournament->id)
                    ->with(['player','team'])
                    ->latest()->take(8)->get();
            @endphp
            @if($recentResults->count() > 0)
            <div class="ap-results-list">
                @foreach($recentResults as $res)
                <div class="ap-result-item">
                    <div class="ap-result-left">
                        <div class="ap-result-name">
                            {{ $res->player->name }}
                        </div>
                        <div class="ap-result-meta">
                            {{ $res->player->player_id }}
                            &middot; {{ $res->player->role }}
                        </div>
                    </div>
                    <div class="ap-result-right">
                        @if($res->status === 'sold')
                        <div class="ap-result-price">
                            ₹{{ number_format($res->sold_price) }}
                        </div>
                        <div class="ap-result-team">
                            {{ $res->team->name ?? '' }}
                        </div>
                        @else
                        <span class="ap-result-unsold">Unsold</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="ap-side-empty">
                <span style="font-size:1.5rem;">📋</span>
                No results yet
            </div>
            @endif
        </div>

        {{-- All Players --}}
        <div class="ap-side-card">
            <div class="ap-side-header">
                <span>
                    <i class="bi bi-list-check"></i> All Players
                </span>
                <span class="ap-player-count-badge">
                    {{ $totalPlayers }}
                </span>
            </div>
            <div class="ap-players-list">
                @if($players->count() > 0)
                @foreach($players as $p)
                <div class="ap-player-item"
                     data-pid="{{ $p->player_id }}">
                    <div class="ap-player-item-avatar">
                        {{ strtoupper(substr($p->name,0,1)) }}
                    </div>
                    <div class="ap-player-item-info">
                        <div class="ap-player-item-name">
                            {{ $p->name }}
                        </div>
                        <div class="ap-player-item-meta">
                            {{ $p->player_id }} &middot; {{ $p->role }}
                        </div>
                    </div>
                    <span class="ap-player-item-status ap-s-{{ $p->status }}">
                        {{ ucfirst($p->status) }}
                    </span>
                </div>
                @endforeach
                @else
                <div class="ap-side-empty">No players yet</div>
                @endif
            </div>
        </div>

    </div>
    {{-- END RIGHT COLUMN --}}

</div>
{{-- END MAIN LAYOUT --}}

{{-- ── EDIT ASSIGNMENT MODAL ── --}}
<div id="editModal" class="ap-modal-overlay" style="display:none;">
    <div class="ap-modal">
        <div class="ap-modal-header">
            <div>
                <div class="ap-modal-title">
                    <i class="bi bi-pencil-fill"></i>
                    Edit Assignment
                </div>
                <div class="ap-modal-subtitle" id="editModalSubtitle"></div>
            </div>
            <button type="button" id="btnCloseModal"
                    class="ap-modal-close">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="ap-modal-body">
            <div class="ap-modal-notice">
                <i class="bi bi-info-circle-fill"></i>
                Budget will be automatically recalculated on reassignment.
                Old team spending will be reversed.
            </div>
            <input type="hidden" id="editPlayerId">
            <div class="ap-modal-form-row">
                <div class="ap-form-group">
                    <label class="ap-label">Status</label>
                    <select id="editStatus" class="ap-select">
                        <option value="sold">✅ Sold</option>
                        <option value="unsold">❌ Unsold</option>
                    </select>
                </div>
                <div class="ap-form-group" id="editPriceGroup">
                    <label class="ap-label">Sold Price (₹)</label>
                    <input type="number"
                           id="editSoldPrice"
                           class="ap-input"
                           placeholder="Corrected price"
                           min="0">
                </div>
            </div>
            <div id="editTeamSection">
                <label class="ap-label"
                       style="margin-bottom:10px;display:block;">
                    Assign to Team
                </label>
                <div class="ap-team-grid" id="editTeamGrid">
                    @foreach($tournament->teams as $team)
                    <div class="ap-team-card"
                         data-edit-team-id="{{ $team->id }}"
                         data-team-name="{{ addslashes($team->name) }}">
                        @if($team->logo)
                            <img src="{{ Storage::url($team->logo) }}"
                                 class="ap-team-logo"
                                 alt="{{ $team->name }}">
                        @else
                            <div class="ap-team-logo-placeholder">
                                {{ strtoupper(substr($team->name,0,1)) }}
                            </div>
                        @endif
                        <div class="ap-team-name">{{ $team->name }}</div>
                        <div class="ap-team-budget">
                            ₹{{ number_format($team->budget - $team->spent) }}
                        </div>
                    </div>
                    @endforeach
                </div>
                <input type="hidden" id="editSelectedTeamId" value="">
            </div>
        </div>
        <div class="ap-modal-footer">
            <button type="button" id="btnCancelModal"
                    class="ap-btn ap-btn-ghost">Cancel</button>
            <button type="button" id="btnSaveEdit"
                    class="ap-btn ap-btn-live">
                <i class="bi bi-check-circle-fill"></i>
                Save Changes
            </button>
        </div>
    </div>
</div>

{{-- ── SUCCESS TOAST ── --}}
<div id="toastWrap" class="ap-toast" style="display:none;">
    <i class="bi bi-check-circle-fill"></i>
    <span id="toastMsg">Done!</span>
</div>

{{-- ── ERROR TOAST ── --}}
<div id="errorToastWrap" class="ap-toast ap-toast-error" style="display:none;">
    <i class="bi bi-exclamation-circle-fill"></i>
    <span id="errorToastMsg">Error!</span>
</div>

@endsection

@push('styles')
<style>
/* ═══════════════════════════════════════════
   AUCTION PANEL — PROFESSIONAL DESIGN SYSTEM
   ═══════════════════════════════════════════ */

/* ── VARIABLES ── */
:root {
    --ap-primary:    #1e3a5f;
    --ap-blue:       #2563eb;
    --ap-blue-lt:    #3b82f6;
    --ap-blue-pale:  #eff6ff;
    --ap-green:      #15803d;
    --ap-green-lt:   #16a34a;
    --ap-green-pale: #f0fdf4;
    --ap-red:        #dc2626;
    --ap-red-pale:   #fef2f2;
    --ap-amber:      #d97706;
    --ap-amber-pale: #fffbeb;
    --ap-slate:      #475569;
    --ap-muted:      #94a3b8;
    --ap-border:     #e2e8f0;
    --ap-bg:         #f8fafc;
    --ap-white:      #ffffff;
    --ap-dark:       #0f172a;
    --ap-radius:     10px;
    --ap-shadow:     0 1px 3px rgba(0,0,0,0.08),
                     0 1px 2px rgba(0,0,0,0.04);
    --ap-shadow-md:  0 4px 16px rgba(0,0,0,0.08),
                     0 2px 6px rgba(0,0,0,0.04);
}

/* ── PAGE HEADER ── */
.ap-header-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
    margin-top: 12px;
}

.ap-title {
    font-family: 'Poppins', sans-serif;
    font-weight: 700;
    font-size: 1.5rem;
    color: var(--ap-dark);
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0 0 4px;
}

.ap-title-icon {
    font-size: 1.4rem;
}

.ap-subtitle {
    font-size: 0.85rem;
    color: var(--ap-muted);
    margin: 0;
}

.ap-header-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

/* ── BUTTONS ── */
.ap-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 9px 18px;
    border-radius: var(--ap-radius);
    font-weight: 600;
    font-size: 0.82rem;
    border: none;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.15s;
    white-space: nowrap;
}

.ap-btn-live {
    background: var(--ap-green-lt);
    color: #fff;
}
.ap-btn-live:hover { background: var(--ap-green); color: #fff; }

.ap-btn-end {
    background: var(--ap-red);
    color: #fff;
}
.ap-btn-end:hover { background: #b91c1c; color: #fff; }

.ap-btn-ghost {
    background: var(--ap-white);
    color: var(--ap-slate);
    border: 1px solid var(--ap-border);
}
.ap-btn-ghost:hover {
    background: var(--ap-bg);
    color: var(--ap-dark);
}

.ap-btn-results {
    background: var(--ap-primary);
    color: #fff;
}
.ap-btn-results:hover { background: #162d4a; color: #fff; }

.ap-completed-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #ede9fe;
    color: #5b21b6;
    padding: 8px 16px;
    border-radius: var(--ap-radius);
    font-size: 0.82rem;
    font-weight: 600;
}

/* ── LIVE BANNER ── */
.ap-live-banner {
    display: flex;
    align-items: center;
    gap: 12px;
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: var(--ap-radius);
    padding: 12px 18px;
    margin-bottom: 20px;
    font-size: 0.875rem;
}

.ap-live-dot {
    width: 10px;
    height: 10px;
    background: var(--ap-red);
    border-radius: 50%;
    flex-shrink: 0;
    animation: apPulse 1.4s ease-in-out infinite;
}

@keyframes apPulse {
    0%,100% { opacity:1; transform:scale(1); }
    50%      { opacity:0.3; transform:scale(0.7); }
}

.ap-live-banner strong { color: #991b1b; }
.ap-live-url {
    color: #b91c1c;
    margin-left: 8px;
    font-size: 0.82rem;
}
.ap-live-url a {
    color: var(--ap-blue);
    font-weight: 600;
}

/* ── STATS ROW ── */
.ap-stats-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 24px;
}

.ap-stat-card {
    background: var(--ap-white);
    border: 1px solid var(--ap-border);
    border-radius: var(--ap-radius);
    padding: 16px 12px;
    text-align: center;
    box-shadow: var(--ap-shadow);
}

.ap-stat-num {
    font-family: 'Poppins', sans-serif;
    font-weight: 800;
    font-size: 1.8rem;
    line-height: 1;
    margin-bottom: 4px;
}

.ap-stat-lbl {
    font-size: 0.7rem;
    color: var(--ap-muted);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.ap-stat-total   .ap-stat-num { color: var(--ap-primary); }
.ap-stat-sold    .ap-stat-num { color: var(--ap-green-lt); }
.ap-stat-unsold  .ap-stat-num { color: var(--ap-red); }
.ap-stat-remaining .ap-stat-num { color: var(--ap-amber); }

/* ── MAIN LAYOUT ── */
.ap-layout {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 20px;
    align-items: start;
}

/* ── LEFT PANEL CARD ── */
.ap-panel-card {
    background: var(--ap-white);
    border: 1px solid var(--ap-border);
    border-radius: 14px;
    box-shadow: var(--ap-shadow);
    overflow: hidden;
}

.ap-panel-header {
    padding: 16px 24px;
    border-bottom: 1px solid var(--ap-border);
    background: #f8fafc;
}

.ap-panel-title {
    font-weight: 700;
    font-size: 0.9rem;
    color: var(--ap-dark);
    display: flex;
    align-items: center;
    gap: 8px;
}

.ap-panel-title i { color: var(--ap-blue); }

.ap-panel-body {
    padding: 24px;
}

/* ── SEARCH BAR ── */
.ap-search-wrap {
    display: flex;
    gap: 8px;
    margin-bottom: 14px;
}

.ap-search-input {
    flex: 1;
    padding: 11px 16px;
    border: 1.5px solid var(--ap-border);
    border-radius: var(--ap-radius);
    font-size: 0.9rem;
    font-weight: 700;
    letter-spacing: 1px;
    color: var(--ap-dark);
    transition: border-color 0.15s, box-shadow 0.15s;
    outline: none;
    text-transform: uppercase;
}

.ap-search-input:focus {
    border-color: var(--ap-blue-lt);
    box-shadow: 0 0 0 3px rgba(59,130,246,0.12);
}

.ap-search-input::placeholder {
    font-weight: 400;
    letter-spacing: 0;
    text-transform: none;
    color: #aab;
}

.ap-search-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 11px 20px;
    background: var(--ap-blue);
    color: #fff;
    border: none;
    border-radius: var(--ap-radius);
    font-weight: 600;
    font-size: 0.875rem;
    cursor: pointer;
    transition: background 0.15s;
    white-space: nowrap;
}

.ap-search-btn:hover { background: var(--ap-primary); }

.ap-clear-btn {
    padding: 11px 14px;
    background: var(--ap-white);
    color: var(--ap-slate);
    border: 1.5px solid var(--ap-border);
    border-radius: var(--ap-radius);
    cursor: pointer;
    transition: all 0.15s;
    font-size: 0.875rem;
}

.ap-clear-btn:hover {
    background: var(--ap-red-pale);
    border-color: #fca5a5;
    color: var(--ap-red);
}

/* ── NAV BAR ── */
.ap-nav-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #f8fafc;
    border: 1px solid var(--ap-border);
    border-radius: var(--ap-radius);
    padding: 10px 14px;
    margin-bottom: 16px;
}

.ap-nav-btn {
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 6px 14px;
    background: var(--ap-white);
    border: 1.5px solid var(--ap-border);
    border-radius: 7px;
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--ap-slate);
    cursor: pointer;
    transition: all 0.15s;
}

.ap-nav-btn:hover:not(:disabled) {
    background: var(--ap-primary);
    border-color: var(--ap-primary);
    color: #fff;
}

.ap-nav-btn:disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

.ap-nav-counter {
    font-size: 0.82rem;
    font-weight: 600;
    color: var(--ap-slate);
}

/* ── BUDGET ALERT ── */
.ap-budget-alert {
    background: var(--ap-red-pale);
    border: 1.5px solid #fca5a5;
    border-radius: var(--ap-radius);
    padding: 11px 16px;
    font-size: 0.82rem;
    font-weight: 500;
    color: #991b1b;
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 14px;
}

/* ── STATE MESSAGES ── */
.ap-state {
    text-align: center;
    padding: 40px 20px;
}

.ap-state-icon {
    font-size: 2.5rem;
    margin-bottom: 12px;
}

.ap-state-title {
    font-weight: 700;
    font-size: 1rem;
    color: var(--ap-dark);
    margin-bottom: 6px;
}

.ap-state-desc {
    font-size: 0.82rem;
    color: var(--ap-muted);
}

/* ── PLAYER PROFILE ── */
.ap-profile {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 18px 20px;
    background: linear-gradient(135deg, #f0f7ff, #f8faff);
    border: 1px solid #dbeafe;
    border-radius: 12px;
    margin-bottom: 16px;
}

.ap-avatar-wrap { flex-shrink: 0; }

.ap-avatar-img {
    width: 68px;
    height: 68px;
    border-radius: 12px;
    object-fit: cover;
    border: 2px solid #bfdbfe;
}

.ap-avatar-placeholder {
    width: 68px;
    height: 68px;
    border-radius: 12px;
    background: var(--ap-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    font-weight: 800;
    color: #fff;
}

.ap-profile-name-row {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 4px;
}

.ap-profile-name {
    font-family: 'Poppins', sans-serif;
    font-weight: 700;
    font-size: 1.15rem;
    color: var(--ap-dark);
    margin: 0;
}

.ap-pid-badge {
    background: #dbeafe;
    color: var(--ap-blue);
    padding: 2px 10px;
    border-radius: 5px;
    font-size: 0.75rem;
    font-weight: 700;
    font-family: monospace;
    letter-spacing: 0.5px;
}

.ap-profile-role {
    font-size: 0.85rem;
    color: var(--ap-slate);
    margin-bottom: 8px;
}

.ap-profile-status-row { display: flex; }

.ap-status-badge {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 4px;
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.5px;
}

.ap-status-approved { background: #dcfce7; color: #166534; }
.ap-status-sold     { background: #ede9fe; color: #5b21b6; }
.ap-status-unsold   { background: #fee2e2; color: #991b1b; }
.ap-status-pending  { background: #fef9c3; color: #854d0e; }

/* ── DETAILS GRID ── */
.ap-details-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8px;
    margin-bottom: 16px;
}

.ap-detail-box {
    background: #f8fafc;
    border: 1px solid var(--ap-border);
    border-radius: 8px;
    padding: 9px 12px;
}

.ap-detail-lbl {
    font-size: 0.62rem;
    font-weight: 600;
    color: var(--ap-muted);
    text-transform: uppercase;
    letter-spacing: 0.4px;
    margin-bottom: 3px;
}

.ap-detail-val {
    font-size: 0.82rem;
    font-weight: 600;
    color: var(--ap-dark);
}

/* ── ASSIGNED WARNING ── */
.ap-assigned-warn {
    background: #fffbeb;
    border: 1.5px solid #fcd34d;
    border-radius: 10px;
    padding: 12px 16px;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}

.ap-assigned-warn-text {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 0.82rem;
    color: #92400e;
}

.ap-assigned-warn-text i { font-size: 1rem; flex-shrink: 0; }
.ap-assigned-warn-text strong { font-weight: 700; }
.ap-assigned-warn-text span { color: #b45309; }

.ap-btn-edit-assign {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    background: var(--ap-amber);
    color: #fff;
    border: none;
    border-radius: 7px;
    font-size: 0.78rem;
    font-weight: 700;
    cursor: pointer;
    white-space: nowrap;
    transition: background 0.15s;
}

.ap-btn-edit-assign:hover { background: #b45309; }

/* ── ASSIGN FORM ── */
.ap-assign-form {
    background: #f8fafc;
    border: 1px solid var(--ap-border);
    border-radius: 12px;
    padding: 20px;
}

.ap-assign-title {
    font-weight: 700;
    font-size: 0.875rem;
    color: var(--ap-dark);
    display: flex;
    align-items: center;
    gap: 7px;
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--ap-border);
}

.ap-assign-title i { color: var(--ap-primary); }

.ap-form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-bottom: 16px;
}

.ap-form-group { display: flex; flex-direction: column; }

.ap-label {
    font-size: 0.72rem;
    font-weight: 700;
    color: var(--ap-slate);
    text-transform: uppercase;
    letter-spacing: 0.3px;
    margin-bottom: 6px;
}

.ap-required { color: var(--ap-red); }

.ap-select,
.ap-input {
    padding: 9px 12px;
    border: 1.5px solid var(--ap-border);
    border-radius: 8px;
    font-size: 0.875rem;
    color: var(--ap-dark);
    background: var(--ap-white);
    outline: none;
    transition: border-color 0.15s, box-shadow 0.15s;
}

.ap-select:focus,
.ap-input:focus {
    border-color: var(--ap-blue-lt);
    box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
}

.ap-hint {
    font-size: 0.72rem;
    color: var(--ap-muted);
    margin-top: 4px;
}

/* ── TEAM GRID ── */
.ap-team-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
    margin-bottom: 16px;
}

.ap-team-card {
    background: var(--ap-white);
    border: 2px solid var(--ap-border);
    border-radius: 10px;
    padding: 12px 8px;
    text-align: center;
    cursor: pointer;
    transition: all 0.15s;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 5px;
}

.ap-team-card:hover {
    border-color: var(--ap-blue-lt);
    background: var(--ap-blue-pale);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(37,99,235,0.1);
}

.ap-team-card.ap-selected {
    border-color: var(--ap-blue);
    background: var(--ap-blue-pale);
    box-shadow: 0 0 0 3px rgba(37,99,235,0.15);
}

.ap-team-card.ap-over-budget {
    border-color: #fca5a5 !important;
    background: var(--ap-red-pale) !important;
    opacity: 0.75;
}

.ap-team-card.ap-over-budget:hover {
    transform: none;
    box-shadow: none;
}

.ap-team-logo {
    width: 38px;
    height: 38px;
    border-radius: 8px;
    object-fit: cover;
    border: 1px solid var(--ap-border);
}

.ap-team-logo-placeholder {
    width: 38px;
    height: 38px;
    border-radius: 8px;
    background: var(--ap-primary);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 0.9rem;
}

.ap-team-name {
    font-size: 0.72rem;
    font-weight: 700;
    color: #1e293b;
    line-height: 1.3;
    word-break: break-word;
}

.ap-team-budget {
    font-size: 0.65rem;
    color: var(--ap-green-lt);
    font-weight: 600;
}

.ap-budget-zero { color: var(--ap-red) !important; }

/* ── ASSIGN BUTTON ── */
.ap-assign-btn {
    width: 100%;
    padding: 13px;
    background: var(--ap-green-lt);
    color: #fff;
    border: none;
    border-radius: 10px;
    font-size: 0.95rem;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: background 0.15s, transform 0.1s;
}

.ap-assign-btn:hover { background: var(--ap-green); }
.ap-assign-btn:active { transform: scale(0.99); }
.ap-assign-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* ── RIGHT SIDEBAR ── */
.ap-right {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.ap-side-card {
    background: var(--ap-white);
    border: 1px solid var(--ap-border);
    border-radius: 14px;
    box-shadow: var(--ap-shadow);
    overflow: hidden;
}

.ap-side-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 18px;
    border-bottom: 1px solid var(--ap-border);
    background: #f8fafc;
    font-weight: 700;
    font-size: 0.82rem;
    color: var(--ap-dark);
}

.ap-side-header i { color: var(--ap-blue); margin-right: 4px; }

.ap-side-link {
    font-size: 0.75rem;
    color: var(--ap-blue);
    font-weight: 600;
    text-decoration: none;
}

.ap-side-link:hover { text-decoration: underline; }

.ap-player-count-badge {
    background: #e2e8f0;
    color: var(--ap-slate);
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 0.72rem;
    font-weight: 700;
}

.ap-side-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 28px 16px;
    font-size: 0.82rem;
    color: var(--ap-muted);
}

/* ── RESULTS LIST ── */
.ap-results-list {
    max-height: 280px;
    overflow-y: auto;
}

.ap-result-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 11px 18px;
    border-bottom: 1px solid #f1f5f9;
    transition: background 0.1s;
}

.ap-result-item:last-child { border-bottom: none; }
.ap-result-item:hover { background: #f8fafc; }

.ap-result-name {
    font-size: 0.82rem;
    font-weight: 600;
    color: var(--ap-dark);
    margin-bottom: 2px;
}

.ap-result-meta {
    font-size: 0.7rem;
    color: var(--ap-muted);
}

.ap-result-price {
    font-weight: 700;
    font-size: 0.85rem;
    color: var(--ap-blue);
    text-align: right;
}

.ap-result-team {
    font-size: 0.7rem;
    color: var(--ap-muted);
    text-align: right;
}

.ap-result-unsold {
    background: #fee2e2;
    color: #991b1b;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 0.7rem;
    font-weight: 600;
}

/* ── PLAYERS LIST ── */
.ap-players-list {
    max-height: 340px;
    overflow-y: auto;
}

.ap-player-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 18px;
    border-bottom: 1px solid #f1f5f9;
    cursor: pointer;
    transition: background 0.1s;
}

.ap-player-item:last-child { border-bottom: none; }

.ap-player-item:hover {
    background: var(--ap-blue-pale);
}

.ap-player-item-avatar {
    width: 32px;
    height: 32px;
    border-radius: 7px;
    background: var(--ap-primary);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 800;
    flex-shrink: 0;
}

.ap-player-item-info { flex: 1; min-width: 0; }

.ap-player-item-name {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--ap-dark);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.ap-player-item-meta {
    font-size: 0.68rem;
    color: var(--ap-muted);
}

.ap-player-item-status {
    font-size: 0.65rem;
    font-weight: 600;
    padding: 2px 7px;
    border-radius: 4px;
    flex-shrink: 0;
}

.ap-s-approved { background: #dcfce7; color: #166534; }
.ap-s-sold     { background: #ede9fe; color: #5b21b6; }
.ap-s-unsold   { background: #fee2e2; color: #991b1b; }
.ap-s-pending  { background: #fef9c3; color: #854d0e; }

/* ── MODAL ── */
.ap-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15,23,42,0.55);
    z-index: 9000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    backdrop-filter: blur(3px);
}

.ap-modal {
    background: var(--ap-white);
    border-radius: 16px;
    width: 100%;
    max-width: 520px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0,0,0,0.2);
    animation: apSlideUp 0.2s ease;
}

@keyframes apSlideUp {
    from { transform: translateY(20px); opacity: 0; }
    to   { transform: translateY(0);    opacity: 1; }
}

.ap-modal-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    padding: 20px 24px 18px;
    background: var(--ap-primary);
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.ap-modal-title {
    font-family: 'Poppins', sans-serif;
    font-weight: 700;
    font-size: 1rem;
    color: #fff;
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 3px;
}

.ap-modal-subtitle {
    font-size: 0.78rem;
    color: rgba(255,255,255,0.65);
}

.ap-modal-close {
    background: rgba(255,255,255,0.1);
    border: none;
    color: #fff;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    transition: background 0.15s;
    flex-shrink: 0;
}

.ap-modal-close:hover { background: rgba(255,255,255,0.2); }

.ap-modal-body { padding: 24px; }

.ap-modal-notice {
    background: var(--ap-amber-pale);
    border: 1px solid #fcd34d;
    border-radius: 8px;
    padding: 10px 14px;
    font-size: 0.8rem;
    color: #92400e;
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 18px;
}

.ap-modal-form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-bottom: 16px;
}

.ap-modal-footer {
    padding: 16px 24px;
    border-top: 1px solid var(--ap-border);
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    background: #f8fafc;
}

/* ── TOASTS ── */
.ap-toast {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 9999;
    background: var(--ap-green-lt);
    color: #fff;
    padding: 13px 20px;
    border-radius: 10px;
    font-size: 0.875rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.15);
    animation: apFadeIn 0.2s ease;
}

.ap-toast-error {
    background: var(--ap-red);
}

@keyframes apFadeIn {
    from { opacity:0; transform:translateY(10px); }
    to   { opacity:1; transform:translateY(0); }
}

/* ── RESPONSIVE ── */
@media (max-width: 1100px) {
    .ap-layout {
        grid-template-columns: 1fr;
    }
    .ap-right {
        display: grid;
        grid-template-columns: 1fr 1fr;
    }
}

@media (max-width: 768px) {
    .ap-stats-row { grid-template-columns: repeat(2,1fr); }
    .ap-form-row  { grid-template-columns: 1fr; }
    .ap-team-grid { grid-template-columns: repeat(2,1fr); }
    .ap-details-grid { grid-template-columns: repeat(2,1fr); }
    .ap-right { grid-template-columns: 1fr; }
    .ap-modal-form-row { grid-template-columns: 1fr; }
}

@media (max-width: 480px) {
    .ap-team-grid { grid-template-columns: 1fr 1fr; }
    .ap-header-row { flex-direction: column; align-items: flex-start; }
}
</style>
@endpush

@push('scripts')
<script>
'use strict';

/* ══════════════════════════════════════════════
   AUCTION PANEL — CLEAN REWRITE
   All IDs referenced once, no inline JS events
   ══════════════════════════════════════════════ */

// ── CONSTANTS (set from Blade, never change) ──
const TOURNAMENT_ID  = {{ (int) $tournament->id }};
const SEARCH_URL     = "{{ route('auction.searchPlayer', $tournament->id) }}";
const ASSIGN_URL     = "{{ route('auction.assign', $tournament->id) }}";
const CSRF_TOKEN     = document.querySelector('meta[name="csrf-token"]')
                               .getAttribute('content');

// ── ALL PLAYERS ARRAY (for prev/next) ──
const ALL_PLAYER_IDS = @json($allPlayerIds);

// ── STATE ──
let currentIndex      = 0;   // index in ALL_PLAYER_IDS
let selectedTeamId    = null;
let selectedRemaining = 0;
let editTeamId        = null;

/* ════════════════════════════════
   DOM REFERENCES
   ════════════════════════════════ */
const $ = id => document.getElementById(id);

const DOM = {
    pidInput:        $('pidInput'),
    btnSearch:       $('btnSearch'),
    btnClear:        $('btnClear'),
    btnPrev:         $('btnPrev'),
    btnNext:         $('btnNext'),
    navCounter:      $('navCounter'),

    budgetAlert:     $('budgetAlert'),
    budgetAlertMsg:  $('budgetAlertMsg'),

    stateNotFound:   $('stateNotFound'),
    stateEmpty:      $('stateEmpty'),
    playerCard:      $('playerCard'),

    avatarImg:       $('avatarImg'),
    avatarPhoto:     $('avatarPhoto'),
    avatarPh:        $('avatarPlaceholder'),
    avatarInitial:   $('avatarInitial'),

    pName:           $('pName'),
    pIdBadge:        $('pIdBadge'),
    pRole:           $('pRole'),
    pStatusBadge:    $('pStatusBadge'),
    detailsGrid:     $('detailsGrid'),

    assignedWarning: $('assignedWarning'),
    btnOpenEdit:     $('btnOpenEdit'),

    selStatus:       $('selStatus'),
    priceGroup:      $('priceGroup'),
    soldPrice:       $('soldPrice'),
    priceHint:       $('priceHint'),
    teamSection:     $('teamSection'),
    teamGrid:        $('teamGrid'),
    selectedPlayerId:$('selectedPlayerId'),
    selectedTeamId:  $('selectedTeamId'),
    selectedTeamRem: $('selectedTeamRemaining'),
    btnAssign:       $('btnAssign'),

    editModal:       $('editModal'),
    editModalSub:    $('editModalSubtitle'),
    editPlayerId:    $('editPlayerId'),
    editStatus:      $('editStatus'),
    editPriceGroup:  $('editPriceGroup'),
    editSoldPrice:   $('editSoldPrice'),
    editTeamSection: $('editTeamSection'),
    editTeamGrid:    $('editTeamGrid'),
    editSelTeamId:   $('editSelectedTeamId'),
    btnSaveEdit:     $('btnSaveEdit'),
    btnCloseModal:   $('btnCloseModal'),
    btnCancelModal:  $('btnCancelModal'),

    toastWrap:       $('toastWrap'),
    toastMsg:        $('toastMsg'),
    errorToastWrap:  $('errorToastWrap'),
    errorToastMsg:   $('errorToastMsg'),
};

/* ════════════════════════════════
   INITIALISE
   ════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {

    // ── Wire up search ──
    DOM.btnSearch.addEventListener('click', doSearch);
    DOM.pidInput.addEventListener('keydown', e => {
        if (e.key === 'Enter') doSearch();
    });

    // ── Wire up clear ──
    DOM.btnClear.addEventListener('click', doClear);

    // ── Wire up prev/next ──
    DOM.btnPrev.addEventListener('click', () => doNavigate(-1));
    DOM.btnNext.addEventListener('click', () => doNavigate(+1));

    // ── Wire up status toggle ──
    DOM.selStatus.addEventListener('change', toggleTeamSection);

    // ── Wire up price live check ──
    DOM.soldPrice.addEventListener('input', checkBudgetLive);

    // ── Wire up assign button ──
    DOM.btnAssign.addEventListener('click', doAssign);

    // ── Wire up team cards (main grid) ──
    DOM.teamGrid.querySelectorAll('.ap-team-card').forEach(card => {
        card.addEventListener('click', () => {
            selectMainTeam(
                parseInt(card.dataset.teamId),
                card.dataset.teamName,
                parseFloat(card.dataset.remaining),
                card
            );
        });
    });

    // ── Wire up edit modal ──
    DOM.btnOpenEdit.addEventListener('click', openEditModal);
    DOM.btnCloseModal.addEventListener('click', closeEditModal);
    DOM.btnCancelModal.addEventListener('click', closeEditModal);
    DOM.editModal.addEventListener('click', e => {
        if (e.target === DOM.editModal) closeEditModal();
    });
    DOM.editStatus.addEventListener('change', toggleEditTeamSection);
    DOM.btnSaveEdit.addEventListener('click', doSaveEdit);

    // ── Wire up edit team cards ──
    DOM.editTeamGrid.querySelectorAll('.ap-team-card').forEach(card => {
        card.addEventListener('click', () => {
            selectEditTeam(
                parseInt(card.dataset.editTeamId),
                card
            );
        });
    });

    // ── Wire up player list items ──
    document.querySelectorAll('.ap-player-item').forEach(item => {
        item.addEventListener('click', () => {
            loadByPid(item.dataset.pid);
        });
    });

    // ── Auto-load first player ──
    @if($firstPlayer)
        currentIndex = 0;
        renderFirstPlayer();
        updateNav();
    @endif
});

/* ════════════════════════════════
   RENDER FIRST PLAYER FROM BLADE
   (avoids an AJAX call on load)
   ════════════════════════════════ */
function renderFirstPlayer() {
    @if($firstPlayer)
    const p = {
        id:             {{ $firstPlayer->id }},
        player_id:      "{{ $firstPlayer->player_id }}",
        name:           "{{ addslashes($firstPlayer->name) }}",
        role:           "{{ addslashes($firstPlayer->role) }}",
        status:         "{{ $firstPlayer->status }}",
        mobile:         "{{ $firstPlayer->mobile ?? '' }}",
        city:           "{{ $firstPlayer->city ?? '' }}",
        age:            "{{ $firstPlayer->age ?? '' }}",
        jersey_number:  "{{ $firstPlayer->jersey_number ?? '' }}",
        batting_style:  "{{ $firstPlayer->batting_style ?? '' }}",
        bowling_style:  "{{ $firstPlayer->bowling_style ?? '' }}",
        experience:     "{{ $firstPlayer->experience ?? '' }}",
        base_price:     {{ (float)($firstPlayer->base_price ?? 0) }},
        photo:          "{{ $firstPlayer->photo ?? '' }}",
        image_url:      "{{ $firstPlayer->image_url ?? '' }}",
    };
    renderPlayer(p);
    @endif
}

/* ════════════════════════════════
   SEARCH
   ════════════════════════════════ */
async function doSearch() {
    const raw = DOM.pidInput.value.trim().toUpperCase();
    if (!raw) return;

    hideBudgetAlert();
    setLoadingState(true);

    try {
        const url  = SEARCH_URL + '?player_id=' + encodeURIComponent(raw);
        const resp = await fetch(url, {
            headers: { 'Accept': 'application/json' }
        });

        if (!resp.ok) throw new Error('HTTP ' + resp.status);

        const data = await resp.json();

        if (!data.player) {
            showState('notfound');
        } else {
            // Update index for nav
            const idx = ALL_PLAYER_IDS.indexOf(data.player.player_id);
            if (idx !== -1) currentIndex = idx;

            renderPlayer(data.player);
            updateNav();

            if (data.currentIndex && data.totalPlayers) {
                DOM.navCounter.textContent =
                    'Player ' + data.currentIndex +
                    ' of ' + data.totalPlayers;
            }
        }
    } catch (err) {
        showErrorToast('Search failed. Check your connection.');
        showState('notfound');
    } finally {
        setLoadingState(false);
    }
}

/* ════════════════════════════════
   LOAD BY PID (from player list)
   ════════════════════════════════ */
function loadByPid(pid) {
    DOM.pidInput.value = pid;
    doSearch();
    // Scroll to top of left panel on mobile
    DOM.pidInput.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

/* ════════════════════════════════
   NAVIGATE PREV / NEXT
   ════════════════════════════════ */
function doNavigate(direction) {
    const newIndex = currentIndex + direction;
    if (newIndex < 0 || newIndex >= ALL_PLAYER_IDS.length) return;
    currentIndex = newIndex;
    const pid = ALL_PLAYER_IDS[currentIndex];
    DOM.pidInput.value = pid;
    doSearch();
}

function updateNav() {
    DOM.btnPrev.disabled = (currentIndex <= 0);
    DOM.btnNext.disabled = (currentIndex >= ALL_PLAYER_IDS.length - 1);
    DOM.navCounter.textContent =
        'Player ' + (currentIndex + 1) +
        ' of ' + ALL_PLAYER_IDS.length;
}

/* ════════════════════════════════
   CLEAR PANEL
   ════════════════════════════════ */
function doClear() {
    DOM.pidInput.value = '';
    hideBudgetAlert();
    resetTeamSelection();
    showState('empty');
}

/* ════════════════════════════════
   RENDER PLAYER
   ════════════════════════════════ */
function renderPlayer(p) {
    // Show card, hide other states
    showState('player');

    // Avatar
    const photo    = p.photo    ? '/storage/' + p.photo : null;
    const imageUrl = p.image_url || null;
    const src      = photo || imageUrl;

    if (src) {
        DOM.avatarImg.style.display   = 'block';
        DOM.avatarPh.style.display    = 'none';
        DOM.avatarPhoto.src           = src;
        DOM.avatarPhoto.onerror       = () => {
            DOM.avatarImg.style.display = 'none';
            DOM.avatarPh.style.display  = 'flex';
            DOM.avatarInitial.textContent = (p.name || '?').charAt(0).toUpperCase();
        };
    } else {
        DOM.avatarImg.style.display   = 'none';
        DOM.avatarPh.style.display    = 'flex';
        DOM.avatarInitial.textContent = (p.name || '?').charAt(0).toUpperCase();
    }

    // Name / ID / Role / Status
    DOM.pName.textContent      = p.name || '';
    DOM.pIdBadge.textContent   = p.player_id || '';
    DOM.pRole.textContent      = p.role || '';

    DOM.pStatusBadge.textContent = (p.status || 'approved').toUpperCase();
    DOM.pStatusBadge.className   = 'ap-status-badge ap-status-' + (p.status || 'approved');

    // Details grid
    const fields = [
        ['Mobile',     p.mobile         || '—'],
        ['City',       p.city           || '—'],
        ['Age',        p.age            || '—'],
        ['Jersey',     p.jersey_number  || '—'],
        ['Batting',    p.batting_style  || '—'],
        ['Bowling',    p.bowling_style  || '—'],
        ['Experience', p.experience     || '—'],
        ['Base Price', p.base_price > 0
            ? '₹' + Number(p.base_price).toLocaleString('en-IN')
            : '—'],
    ];

    DOM.detailsGrid.innerHTML = fields.map(([lbl, val]) => `
        <div class="ap-detail-box">
            <div class="ap-detail-lbl">${lbl}</div>
            <div class="ap-detail-val">${val}</div>
        </div>
    `).join('');

    // Base price hint + pre-fill
    if (p.base_price > 0) {
        DOM.priceHint.textContent = 'Base: ₹' +
            Number(p.base_price).toLocaleString('en-IN');
        if (!DOM.soldPrice.value) {
            DOM.soldPrice.value = p.base_price;
        }
    } else {
        DOM.priceHint.textContent = '';
    }

    // Already sold warning
    DOM.assignedWarning.style.display = (p.status === 'sold') ? 'flex' : 'none';

    // Store player ID
    DOM.selectedPlayerId.value = p.id;

    // Reset team selection
    resetTeamSelection();

    // Reset status dropdown
    DOM.selStatus.value = 'sold';
    toggleTeamSection();

    hideBudgetAlert();
}

/* ════════════════════════════════
   STATE HELPERS
   ════════════════════════════════ */
function showState(state) {
    DOM.stateNotFound.style.display = (state === 'notfound') ? 'block' : 'none';
    DOM.stateEmpty.style.display    = (state === 'empty')    ? 'block' : 'none';
    DOM.playerCard.style.display    = (state === 'player')   ? 'block' : 'none';
}

function setLoadingState(loading) {
    DOM.btnSearch.disabled    = loading;
    DOM.btnSearch.innerHTML   = loading
        ? '<span class="spinner-border spinner-border-sm"></span>'
        : '<i class="bi bi-search"></i> Search';
}

/* ════════════════════════════════
   TEAM SELECTION
   ════════════════════════════════ */
function selectMainTeam(id, name, remaining, cardEl) {
    selectedTeamId    = id;
    selectedRemaining = remaining;
    DOM.selectedTeamId.value  = id;
    DOM.selectedTeamRem.value = remaining;

    // Visual update
    DOM.teamGrid.querySelectorAll('.ap-team-card').forEach(c => {
        c.classList.remove('ap-selected', 'ap-over-budget');
    });
    cardEl.classList.add('ap-selected');

    checkBudgetLive();
}

function resetTeamSelection() {
    selectedTeamId    = null;
    selectedRemaining = 0;
    DOM.selectedTeamId.value  = '';
    DOM.selectedTeamRem.value = '0';

    DOM.teamGrid.querySelectorAll('.ap-team-card').forEach(c => {
        c.classList.remove('ap-selected', 'ap-over-budget');
    });
}

function toggleTeamSection() {
    const isUnsold = DOM.selStatus.value === 'unsold';
    DOM.teamSection.style.display  = isUnsold ? 'none' : 'block';
    DOM.priceGroup.style.display   = isUnsold ? 'none' : 'flex';
    hideBudgetAlert();
}

/* ════════════════════════════════
   BUDGET CHECK
   ════════════════════════════════ */
function checkBudgetLive() {
    if (!selectedTeamId) return;

    const price = parseFloat(DOM.soldPrice.value) || 0;
    const rem   = selectedRemaining;

    const card = DOM.teamGrid.querySelector(
        '.ap-team-card[data-team-id="' + selectedTeamId + '"]'
    );

    if (price > rem && price > 0) {
        if (card) {
            card.classList.add('ap-over-budget');
            card.classList.remove('ap-selected');
        }
        showBudgetAlert(
            'Budget exceeded! This team only has ₹' +
            Number(rem).toLocaleString('en-IN') + ' remaining.'
        );
    } else {
        if (card) {
            card.classList.remove('ap-over-budget');
            card.classList.add('ap-selected');
        }
        hideBudgetAlert();
    }
}

function showBudgetAlert(msg) {
    DOM.budgetAlertMsg.textContent = msg;
    DOM.budgetAlert.style.display  = 'flex';
}

function hideBudgetAlert() {
    DOM.budgetAlert.style.display = 'none';
    DOM.budgetAlertMsg.textContent = '';
}

/* ════════════════════════════════
   ASSIGN PLAYER
   ════════════════════════════════ */
async function doAssign() {
    const playerId = DOM.selectedPlayerId.value;
    const status   = DOM.selStatus.value;
    const price    = parseFloat(DOM.soldPrice.value) || 0;
    const teamId   = selectedTeamId;

    // Validation
    if (!playerId) {
        showErrorToast('No player loaded. Search first.'); return;
    }
    if (status === 'sold' && !teamId) {
        showErrorToast('Please select a team.'); return;
    }
    if (status === 'sold' && price <= 0) {
        showErrorToast('Please enter a valid bid price.'); return;
    }
    if (status === 'sold' && price > selectedRemaining) {
        showBudgetAlert('Cannot assign — budget exceeded!'); return;
    }

    // Disable button
    DOM.btnAssign.disabled    = true;
    DOM.btnAssign.innerHTML   =
        '<span class="spinner-border spinner-border-sm me-2"></span>Assigning…';

    try {
        const resp = await fetch(ASSIGN_URL, {
            method:  'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept':       'application/json',
            },
            body: JSON.stringify({
                player_id:  parseInt(playerId),
                team_id:    teamId,
                sold_price: price,
                status:     status,
            }),
        });

        const data = await resp.json();

        if (data.success) {
            showSuccessToast(data.message || 'Player assigned!');

            // Auto-advance to next player after short delay
            setTimeout(() => {
                if (currentIndex < ALL_PLAYER_IDS.length - 1) {
                    doNavigate(+1);
                    // Reload stats after navigation settles
                    setTimeout(() => location.reload(), 2000);
                } else {
                    location.reload();
                }
            }, 1400);
        } else {
            showErrorToast(data.message || 'Assignment failed.');
            DOM.btnAssign.disabled  = false;
            DOM.btnAssign.innerHTML =
                '<i class="bi bi-check-circle-fill"></i> Assign Player';
        }
    } catch (err) {
        showErrorToast('Network error. Please try again.');
        DOM.btnAssign.disabled  = false;
        DOM.btnAssign.innerHTML =
            '<i class="bi bi-check-circle-fill"></i> Assign Player';
    }
}

/* ════════════════════════════════
   EDIT MODAL
   ════════════════════════════════ */
function openEditModal() {
    const playerId   = DOM.selectedPlayerId.value;
    const playerName = DOM.pName.textContent.trim();
    const playerPid  = DOM.pIdBadge.textContent.trim();
    const curStatus  = DOM.pStatusBadge.textContent.toLowerCase().trim();

    DOM.editPlayerId.value      = playerId;
    DOM.editModalSub.textContent = playerName + ' · ' + playerPid;
    DOM.editStatus.value         = (curStatus === 'sold') ? 'sold' : 'unsold';
    DOM.editSoldPrice.value      = DOM.soldPrice.value || '';

    editTeamId = null;
    DOM.editSelTeamId.value = '';
    DOM.editTeamGrid.querySelectorAll('.ap-team-card').forEach(c => {
        c.classList.remove('ap-selected');
    });

    toggleEditTeamSection();
    DOM.editModal.style.display = 'flex';
}

function closeEditModal() {
    DOM.editModal.style.display = 'none';
    editTeamId = null;
}

function toggleEditTeamSection() {
    const isUnsold = DOM.editStatus.value === 'unsold';
    DOM.editTeamSection.style.display = isUnsold ? 'none' : 'block';
    DOM.editPriceGroup.style.display  = isUnsold ? 'none' : 'flex';
}

function selectEditTeam(id, cardEl) {
    editTeamId = id;
    DOM.editSelTeamId.value = id;
    DOM.editTeamGrid.querySelectorAll('.ap-team-card').forEach(c => {
        c.classList.remove('ap-selected');
    });
    cardEl.classList.add('ap-selected');
}

async function doSaveEdit() {
    const playerId = DOM.editPlayerId.value;
    const status   = DOM.editStatus.value;
    const price    = parseFloat(DOM.editSoldPrice.value) || 0;
    const teamId   = editTeamId;

    if (status === 'sold' && !teamId) {
        showErrorToast('Please select a team.'); return;
    }
    if (status === 'sold' && price <= 0) {
        showErrorToast('Please enter a valid price.'); return;
    }

    DOM.btnSaveEdit.disabled    = true;
    DOM.btnSaveEdit.innerHTML   =
        '<span class="spinner-border spinner-border-sm me-2"></span>Saving…';

    try {
        const resp = await fetch(ASSIGN_URL, {
            method:  'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept':       'application/json',
            },
            body: JSON.stringify({
                player_id:  parseInt(playerId),
                team_id:    teamId || null,
                sold_price: price,
                status:     status,
            }),
        });

        const data = await resp.json();

        if (data.success) {
            closeEditModal();
            showSuccessToast('Assignment updated!');
            setTimeout(() => location.reload(), 1500);
        } else {
            showErrorToast(data.message || 'Update failed.');
            DOM.btnSaveEdit.disabled  = false;
            DOM.btnSaveEdit.innerHTML =
                '<i class="bi bi-check-circle-fill"></i> Save Changes';
        }
    } catch (err) {
        showErrorToast('Network error.');
        DOM.btnSaveEdit.disabled  = false;
        DOM.btnSaveEdit.innerHTML =
            '<i class="bi bi-check-circle-fill"></i> Save Changes';
    }
}

/* ════════════════════════════════
   TOAST HELPERS
   ════════════════════════════════ */
let toastTimer      = null;
let errorToastTimer = null;

function showSuccessToast(msg) {
    DOM.toastMsg.textContent     = msg;
    DOM.toastWrap.style.display  = 'flex';
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => {
        DOM.toastWrap.style.display = 'none';
    }, 3500);
}

function showErrorToast(msg) {
    DOM.errorToastMsg.textContent     = msg;
    DOM.errorToastWrap.style.display  = 'flex';
    clearTimeout(errorToastTimer);
    errorToastTimer = setTimeout(() => {
        DOM.errorToastWrap.style.display = 'none';
    }, 4000);
}
</script>
@endpush