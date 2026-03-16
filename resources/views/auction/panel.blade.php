@extends('layouts.app')

@section('title', 'Auction Panel')
@section('page-title', 'Auction Control Panel')

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
                <li class="breadcrumb-item active">Auction Panel</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center
                justify-content-between flex-wrap gap-3 mt-2">
            <div>
                <h1>🎙️ Auction Control Panel</h1>
                <p>{{ $tournament->name }} &mdash;
                    Search player by ID and assign to teams</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                {{-- Auction Status Control --}}
                <form method="POST" action="{{ route('auction.updateStatus', $tournament->id) }}">
                    @csrf
                    @if ($tournament->auction_status === 'pending')
                        <input type="hidden" name="status" value="live">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-broadcast me-2"></i>Start Auction
                        </button>
                    @elseif($tournament->auction_status === 'live')
                        <input type="hidden" name="status" value="completed">
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-stop-circle-fill me-2"></i>
                            End Auction
                        </button>
                    @else
                        <span class="badge-status badge-completed px-4 py-2">
                            Auction Completed
                        </span>
                    @endif
                </form>
                <a href="{{ route('public.live', $tournament->code) }}" target="_blank" class="btn btn-outline-primary">
                    <i class="bi bi-eye-fill me-2"></i>Live View
                </a>
                <a href="{{ route('auction.results', $tournament->id) }}" class="btn btn-warning">
                    <i class="bi bi-bar-chart-fill me-2"></i>Results
                </a>
            </div>
        </div>
    </div>

    {{-- Auction Status Banner --}}
    @if ($tournament->auction_status === 'live')
        <div class="alert mb-4"
            style="background:linear-gradient(135deg,
            rgba(239,68,68,0.1),rgba(239,68,68,0.05));
            border:1.5px solid rgba(239,68,68,0.2);
            border-radius:14px;">
            <div class="d-flex align-items-center gap-3">
                <div
                    style="width:12px;height:12px;background:#ef4444;
                    border-radius:50%;
                    animation:livePulse 1.5s infinite;
                    flex-shrink:0;">
                </div>
                <div>
                    <strong style="color:#991b1b;">Auction is LIVE!</strong>
                    <span style="color:#b91c1c;font-size:0.875rem;margin-left:8px;">
                        Viewers can watch at:
                        <a href="{{ route('public.live', $tournament->code) }}" target="_blank"
                            style="color:#6c3fc5;font-weight:700;">
                            {{ route('public.live', $tournament->code) }}
                        </a>
                    </span>
                </div>
            </div>
        </div>
    @endif

    {{-- Stats Bar --}}
    <div class="row g-3 mb-4">
        @php
            $totalApproved = $players->count();
            $soldCount = $players->where('status', 'sold')->count();
            $unsoldCount = $players->where('status', 'unsold')->count();
            $remaining = $players->where('status', 'approved')->count();
        @endphp
        <div class="col-6 col-md-3">
            <div class="card text-center p-3">
                <div style="font-size:1.6rem;font-weight:800;
                        color:#6c3fc5;">{{ $totalApproved }}
                </div>
                <div style="font-size:0.75rem;color:#9ca3af;">
                    Total Players
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center p-3">
                <div style="font-size:1.6rem;font-weight:800;
                        color:#10b981;">{{ $soldCount }}
                </div>
                <div style="font-size:0.75rem;color:#9ca3af;">Sold</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center p-3">
                <div style="font-size:1.6rem;font-weight:800;
                        color:#ef4444;">{{ $unsoldCount }}
                </div>
                <div style="font-size:0.75rem;color:#9ca3af;">Unsold</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center p-3">
                <div style="font-size:1.6rem;font-weight:800;
                        color:#d97706;">{{ $remaining }}
                </div>
                <div style="font-size:0.75rem;color:#9ca3af;">Remaining</div>
            </div>
        </div>
    </div>

    <div class="row g-4">

        {{-- LEFT: Search & Assign Panel --}}
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-search text-primary"></i>
                    Search Player by ID
                </div>
                <div class="card-body">
                    {{-- Search Box --}}
                    {{-- Search Box --}}
                    <div class="d-flex gap-2 mb-3">
                        <input type="text" id="searchPlayerInput" class="form-control"
                            placeholder="Enter Player ID e.g. PX1001"
                            style="font-size:1rem;font-weight:600;
                  letter-spacing:1px;"
                            onkeydown="if(event.key==='Enter') searchPlayer()">
                        <button onclick="searchPlayer()" class="btn btn-primary"
                            style="white-space:nowrap;min-width:100px;">
                            <i class="bi bi-search me-1"></i>Search
                        </button>
                        <button onclick="clearPanel()" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>

                    {{-- Prev / Next Navigation --}}
                    <div class="d-flex align-items-center justify-content-between mb-4" id="playerNavBar"
                        style="display:none !important;">
                        <button onclick="navigatePlayer('prev')" id="prevBtn" class="btn btn-sm btn-outline-secondary"
                            disabled>
                            <i class="bi bi-chevron-left me-1"></i>Previous
                        </button>
                        <span id="playerNavCounter" style="font-size:0.82rem;color:#9ca3af;font-weight:600;">
                        </span>
                        <button onclick="navigatePlayer('next')" id="nextBtn" class="btn btn-sm btn-outline-secondary"
                            disabled>
                            Next<i class="bi bi-chevron-right ms-1"></i>
                        </button>
                    </div>
                    {{-- Player Not Found --}}
                    <div id="notFound" style="display:none;" class="text-center py-4">
                        <div style="font-size:2.5rem;margin-bottom:10px;">
                            🔍
                        </div>
                        <div style="font-weight:700;color:#374151;">
                            Player Not Found
                        </div>
                        <div style="font-size:0.85rem;color:#9ca3af;
                                margin-top:4px;">
                            Check the Player ID and try again
                        </div>
                    </div>

                    {{-- Empty State --}}
                    <div id="emptyState" class="text-center py-4">
                        <div style="font-size:3rem;margin-bottom:12px;">🏏</div>
                        <div style="font-weight:700;color:#374151;
                                margin-bottom:6px;">
                            Ready for Auction
                        </div>
                        <div style="font-size:0.85rem;color:#9ca3af;">
                            Enter a Player ID above to begin
                        </div>
                    </div>

                    {{-- Player Card --}}
                    <div id="playerCard" style="display:none;">

                        {{-- Player Profile --}}
                        <div class="player-profile-card mb-4">
                            <div class="d-flex align-items-start gap-3">
                                <div id="playerAvatarImg" style="display:none;">
                                    <img id="playerPhoto" src=""
                                        style="width:72px;height:72px;
                                            border-radius:16px;
                                            object-fit:cover;
                                            border:3px solid rgba(108,63,197,0.2);">
                                </div>
                                <div id="playerAvatarPlaceholder"
                                    style="width:72px;height:72px;
                                        border-radius:16px;
                                        background:linear-gradient(135deg,
                                        #6c3fc5,#8b5cf6);
                                        display:flex;align-items:center;
                                        justify-content:center;
                                        font-size:1.8rem;font-weight:800;
                                        color:#fff;flex-shrink:0;">
                                    <span id="playerInitial"></span>
                                </div>
                                <div class="flex-fill">
                                    <div
                                        class="d-flex align-items-center
                                            gap-2 flex-wrap">
                                        <h4 id="playerName"
                                            style="font-weight:800;
                                               font-family:'Poppins',sans-serif;
                                               margin:0;">
                                        </h4>
                                        <code id="playerIdBadge"
                                            style="background:#f0f2f8;
                                                 color:#6c3fc5;
                                                 padding:3px 10px;
                                                 border-radius:6px;
                                                 font-size:0.8rem;
                                                 font-weight:700;"></code>
                                    </div>
                                    <div id="playerRole"
                                        style="color:#6b7280;
                                            font-size:0.875rem;
                                            margin-top:4px;">
                                    </div>
                                    <div class="d-flex gap-2 mt-2 flex-wrap">
                                        <span id="statusBadge" class="badge-status"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Player Details Grid --}}
                        <div class="row g-2 mb-4" id="playerDetailsGrid">
                        </div>

                        {{-- Already Sold — Show Edit Button --}}
                        <div id="alreadySoldWarning" style="display:none;" class="mb-4">
                            <div
                                style="background:#fef3c7;
                border:1.5px solid #fbbf24;
                border-radius:12px;
                padding:14px 18px;">
                                <div
                                    class="d-flex align-items-center
                    justify-content-between flex-wrap gap-2">
                                    <div>
                                        <div
                                            style="font-weight:700;color:#92400e;
                            font-size:0.875rem;margin-bottom:2px;">
                                            <i
                                                class="bi bi-exclamation-triangle-fill
                               me-2"></i>
                                            This player is already assigned
                                        </div>
                                        <div style="font-size:0.78rem;color:#b45309;">
                                            Use the edit button to reassign to
                                            a different team or fix the price.
                                        </div>
                                    </div>
                                    <button onclick="openEditModal()" class="btn btn-sm btn-warning"
                                        style="font-weight:700;white-space:nowrap;">
                                        <i class="bi bi-pencil-fill me-1"></i>
                                        Edit Assignment
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Assign Form --}}
                        <div id="assignForm">
                            <div
                                style="font-weight:700;font-size:0.875rem;
                                    color:#374151;margin-bottom:14px;
                                    padding-bottom:10px;
                                    border-bottom:1.5px solid #f0f2f8;">
                                <i class="bi bi-hammer me-2" style="color:#6c3fc5;"></i>
                                Assign Player
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Auction Status
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select id="auctionStatus" class="form-select" onchange="toggleTeamSelect()">
                                        <option value="sold">✅ Sold</option>
                                        <option value="unsold">
                                            ❌ Unsold
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-6" id="soldPriceCol">
                                    <label class="form-label">
                                        Final Bid Price (₹)
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" id="soldPrice" class="form-control"
                                        placeholder="Enter final bid amount" min="0">
                                    <div id="basePriceHint"
                                        style="font-size:0.75rem;
                                            color:#9ca3af;margin-top:4px;">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4" id="teamSelectCol">
                                <label class="form-label">
                                    Assign to Team
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="row g-2" id="teamGrid">
                                    @foreach ($tournament->teams as $team)
                                        <div class="col-6 col-md-4">
                                            <div class="team-select-card"
                                                onclick="selectTeam(
                                             {{ $team->id }},
                                             '{{ $team->name }}',
                                             this)"
                                                data-team-id="{{ $team->id }}">
                                                @if ($team->logo)
                                                    <img src="{{ Storage::url($team->logo) }}"
                                                        style="width:36px;height:36px;
                                                    border-radius:8px;
                                                    object-fit:cover;
                                                    margin-bottom:6px;">
                                                @else
                                                    <div
                                                        style="width:36px;height:36px;
                                                    border-radius:8px;
                                                    background:linear-gradient(
                                                    135deg,#d97706,#f59e0b);
                                                    display:flex;
                                                    align-items:center;
                                                    justify-content:center;
                                                    font-weight:700;
                                                    color:#fff;
                                                    margin:0 auto 6px;">
                                                        {{ strtoupper(substr($team->name, 0, 1)) }}
                                                    </div>
                                                @endif
                                                <div
                                                    style="font-size:0.75rem;
                                                    font-weight:600;
                                                    color:#374151;
                                                    text-align:center;
                                                    line-height:1.3;">
                                                    {{ $team->name }}
                                                </div>
                                                <div
                                                    style="font-size:0.65rem;
                                                    color:#9ca3af;
                                                    text-align:center;
                                                    margin-top:2px;">
                                                    ₹{{ number_format($team->budget - $team->spent) }}
                                                    left
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <input type="hidden" id="selectedTeamId">
                            </div>

                            <input type="hidden" id="selectedPlayerId">

                            <button onclick="assignPlayer()" class="btn btn-success w-100"
                                style="padding:14px;font-size:1rem;
                                       font-weight:700;">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                Assign Player
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT: Recent Results + Player List --}}
        <div class="col-lg-5">

            {{-- Recent Results --}}
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history text-warning"></i>
                    Recent Results
                    <a href="{{ route('auction.results', $tournament->id) }}"
                        class="ms-auto btn btn-sm btn-outline-primary">
                        View All
                    </a>
                </div>
                <div id="recentResults">
                    @php
                        $recentResults = \App\Models\AuctionResult::where('tournament_id', $tournament->id)
                            ->with(['player', 'team'])
                            ->latest()
                            ->take(8)
                            ->get();
                    @endphp
                    @if ($recentResults->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach ($recentResults as $result)
                                <div class="list-group-item px-4 py-3">
                                    <div
                                        class="d-flex align-items-center
                                    justify-content-between gap-2">
                                        <div>
                                            <div
                                                style="font-weight:600;
                                            font-size:0.875rem;">
                                                {{ $result->player->name }}
                                            </div>
                                            <div
                                                style="font-size:0.75rem;
                                            color:#9ca3af;">
                                                {{ $result->player->player_id }}
                                                &middot;
                                                {{ $result->player->role }}
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            @if ($result->status === 'sold')
                                                <div
                                                    style="font-weight:700;
                                            color:#6c3fc5;
                                            font-size:0.875rem;">
                                                    ₹{{ number_format($result->sold_price) }}
                                                </div>
                                                <div
                                                    style="font-size:0.72rem;
                                            color:#9ca3af;">
                                                    {{ $result->team->name ?? '' }}
                                                </div>
                                            @else
                                                <span class="badge-status badge-unsold">
                                                    Unsold
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <div style="font-size:2rem;margin-bottom:8px;">📋</div>
                            <div style="font-size:0.85rem;color:#9ca3af;">
                                No results yet
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Approved Players Quick List --}}
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-list-check text-success"></i>
                    Approved Players
                    <span class="ms-auto badge" style="background:#f0f2f8;color:#6b7280;">
                        {{ $remaining }} remaining
                    </span>
                </div>
                <div style="max-height:320px;overflow-y:auto;">
                    @php
                        $approvedPlayers = $players->where('status', 'approved');
                    @endphp
                    @if ($approvedPlayers->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach ($approvedPlayers as $p)
                                <div class="list-group-item list-group-item-action
                                px-4 py-2"
                                    style="cursor:pointer;" onclick="quickLoad('{{ $p->player_id }}')">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="player-avatar-placeholder"
                                            style="width:32px;height:32px;
                                        font-size:0.75rem;">
                                            {{ strtoupper(substr($p->name, 0, 1)) }}
                                        </div>
                                        <div class="flex-fill">
                                            <div
                                                style="font-size:0.82rem;
                                            font-weight:600;">
                                                {{ $p->name }}
                                            </div>
                                            <div
                                                style="font-size:0.72rem;
                                            color:#9ca3af;">
                                                {{ $p->player_id }}
                                                &middot; {{ $p->role }}
                                            </div>
                                        </div>
                                        <i class="bi bi-arrow-right-circle
                                      text-muted"
                                            style="font-size:0.9rem;"></i>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <div style="font-size:0.85rem;color:#9ca3af;">
                                No approved players remaining
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Success Toast --}}
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index:9999;">
        <div id="successToast" class="toast align-items-center text-white border-0"
            style="background:linear-gradient(135deg,#059669,#10b981);
                border-radius:14px;"
            role="alert">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2">
                    <i class="bi bi-check-circle-fill" style="font-size:1.1rem;"></i>
                    <span id="toastMessage">Player assigned successfully!</span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>
    {{-- ── EDIT ASSIGNMENT MODAL ── --}}
    <div class="modal fade" id="editAssignModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border:none;border-radius:20px;overflow:hidden;">

                {{-- Header --}}
                <div class="modal-header"
                    style="background:linear-gradient(135deg,
                        #d97706,#f59e0b);
                        border:none;padding:20px 24px;">
                    <div>
                        <h5 class="modal-title mb-1" style="color:#fff;font-weight:700;margin:0;">
                            <i class="bi bi-pencil-fill me-2"></i>
                            Edit Player Assignment
                        </h5>
                        <div style="color:rgba(255,255,255,0.8);
                                font-size:0.8rem;"
                            id="editModalSubtitle">
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                {{-- Body --}}
                <div class="modal-body p-4">

                    {{-- Budget Recalc Notice --}}
                    <div class="p-3 rounded-3 mb-4"
                        style="background:#fef3c7;
                            border:1.5px solid #fbbf24;">
                        <div style="font-size:0.82rem;color:#92400e;">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            <strong>Budget will be auto-corrected.</strong>
                            The previous team's spent amount will be
                            reversed and new team's budget updated.
                        </div>
                    </div>

                    <input type="hidden" id="editPlayerId">

                    <div class="row g-3">

                        {{-- Current Assignment Info --}}
                        <div class="col-12">
                            <div style="background:#f8f9ff;
                                    border:1.5px solid #e8eaf0;
                                    border-radius:10px;
                                    padding:12px 16px;"
                                id="currentAssignInfo">
                            </div>
                        </div>

                        {{-- New Status --}}
                        <div class="col-12">
                            <label class="form-label">
                                Auction Status
                            </label>
                            <select id="editStatus" class="form-select" onchange="toggleEditFields()">
                                <option value="sold">✅ Sold</option>
                                <option value="unsold">❌ Unsold</option>
                            </select>
                        </div>

                        {{-- New Team --}}
                        <div class="col-12" id="editTeamCol">
                            <label class="form-label">
                                Assign to Team
                                <span class="text-danger">*</span>
                            </label>
                            <div class="row g-2" id="editTeamGrid">
                                @foreach ($tournament->teams as $team)
                                    <div class="col-6">
                                        <div class="edit-team-card"
                                            onclick="selectEditTeam(
                                         {{ $team->id }}, this)"
                                            data-team-id="{{ $team->id }}">
                                            <div
                                                class="d-flex
                                                align-items-center
                                                gap-2">
                                                @if ($team->logo)
                                                    <img src="{{ Storage::url($team->logo) }}"
                                                        style="width:32px;
                                                    height:32px;
                                                    border-radius:8px;
                                                    object-fit:cover;
                                                    flex-shrink:0;">
                                                @else
                                                    <div
                                                        style="width:32px;
                                                    height:32px;
                                                    border-radius:8px;
                                                    background:linear-gradient(
                                                    135deg,#d97706,
                                                    #f59e0b);
                                                    display:flex;
                                                    align-items:center;
                                                    justify-content:center;
                                                    font-weight:700;
                                                    color:#fff;
                                                    font-size:0.8rem;
                                                    flex-shrink:0;">
                                                        {{ strtoupper(substr($team->name, 0, 1)) }}
                                                    </div>
                                                @endif
                                                <div style="min-width:0;">
                                                    <div
                                                        style="font-size:0.8rem;
                                                        font-weight:600;
                                                        color:#374151;
                                                        white-space:nowrap;
                                                        overflow:hidden;
                                                        text-overflow:ellipsis;">
                                                        {{ $team->name }}
                                                    </div>
                                                    <div
                                                        style="font-size:0.68rem;
                                                        color:#9ca3af;">
                                                        ₹{{ number_format($team->budget - $team->spent) }}
                                                        left
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <input type="hidden" id="editSelectedTeamId">
                        </div>

                        {{-- New Price --}}
                        <div class="col-12" id="editPriceCol">
                            <label class="form-label">
                                Final Sold Price (₹)
                                <span class="text-danger">*</span>
                            </label>
                            <input type="number" id="editSoldPrice" class="form-control"
                                placeholder="Enter corrected bid amount" min="0">
                        </div>

                    </div>
                </div>

                {{-- Footer --}}
                <div class="modal-footer"
                    style="border-top:1px solid #f0f2f8;
                        padding:16px 24px;">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-warning" id="saveEditBtn" onclick="saveEditAssignment()"
                        style="color:#fff;font-weight:700;">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        @keyframes livePulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.4;
                transform: scale(0.8);
            }
        }

        .player-profile-card {
            background: linear-gradient(135deg,
                    rgba(108, 63, 197, 0.05),
                    rgba(139, 92, 246, 0.08));
            border: 1.5px solid rgba(108, 63, 197, 0.15);
            border-radius: 16px;
            padding: 20px;
        }

        .team-select-card {
            border: 2px solid #e8eaf0;
            border-radius: 12px;
            padding: 12px 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .team-select-card:hover {
            border-color: #6c3fc5;
            background: rgba(108, 63, 197, 0.04);
            transform: translateY(-2px);
        }

        .team-select-card.selected {
            border-color: #6c3fc5;
            background: linear-gradient(135deg,
                    rgba(108, 63, 197, 0.08),
                    rgba(139, 92, 246, 0.12));
            box-shadow: 0 4px 15px rgba(108, 63, 197, 0.15);
        }

        .detail-box {
            background: #f8f9ff;
            border-radius: 10px;
            padding: 10px 12px;
        }

        .detail-box .d-label {
            font-size: 0.68rem;
            font-weight: 600;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .detail-box .d-value {
            font-size: 0.875rem;
            font-weight: 600;
            color: #1e1e2e;
        }

        .edit-team-card {
            border: 2px solid #e8eaf0;
            border-radius: 10px;
            padding: 10px 12px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .edit-team-card:hover {
            border-color: #d97706;
            background: rgba(217, 119, 6, 0.04);
        }

        .edit-team-card.selected {
            border-color: #d97706;
            background: linear-gradient(135deg,
                    rgba(217, 119, 6, 0.08),
                    rgba(245, 158, 11, 0.12));
            box-shadow: 0 3px 12px rgba(217, 119, 6, 0.15);
        }
    </style>
@endpush
@push('scripts')
    <script>
        const TOURNAMENT_ID = {{ $tournament->id }};
        let selectedTeamId = null;
        let currentPrevId = null;
        let currentNextId = null;

        // ── SEARCH PLAYER ──
        async function searchPlayer() {
            const pid = document.getElementById('searchPlayerInput')
                .value.trim().toUpperCase();
            if (!pid) return;

            try {
                const res = await fetch(
                    `{{ route('auction.searchPlayer', $tournament->id) }}` +
                    `?player_id=${pid}`
                );
                const data = await res.json();

                if (!data.player) {
                    showNotFound();
                    hideNavBar();
                } else {
                    showPlayerCard(data.player);
                    updateNavBar(
                        data.prevPlayerId,
                        data.nextPlayerId,
                        data.currentIndex,
                        data.totalPlayers
                    );
                }
            } catch (e) {
                showNotFound();
                hideNavBar();
            }
        }

        function quickLoad(pid) {
            document.getElementById('searchPlayerInput').value = pid;
            searchPlayer();
            document.getElementById('searchPlayerInput')
                .scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
        }

        // ── NAVIGATE PREV / NEXT ──
        function navigatePlayer(direction) {
            const pid = direction === 'prev' ?
                currentPrevId : currentNextId;
            if (!pid) return;
            quickLoad(pid);
        }

        // ── UPDATE NAV BAR ──
        function updateNavBar(prevId, nextId, index, total) {
            currentPrevId = prevId;
            currentNextId = nextId;

            const navBar = document.getElementById('playerNavBar');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const counter = document.getElementById('playerNavCounter');

            navBar.style.display = 'flex';
            navBar.style.setProperty('display', 'flex', 'important');
            prevBtn.disabled = !prevId;
            nextBtn.disabled = !nextId;
            counter.textContent = `Player ${index} of ${total}`;
        }

        function hideNavBar() {
            const navBar = document.getElementById('playerNavBar');
            navBar.style.setProperty('display', 'none', 'important');
        }

        // ── SHOW PLAYER CARD ──
        function showPlayerCard(player) {
            document.getElementById('emptyState').style.display = 'none';
            document.getElementById('notFound').style.display = 'none';
            document.getElementById('playerCard').style.display = 'block';

            if (player.photo) {
                document.getElementById('playerAvatarImg').style.display = 'block';
                document.getElementById('playerAvatarPlaceholder').style.display = 'none';
                document.getElementById('playerPhoto').src = '/storage/' + player.photo;
            } else if (player.image_url) {
                document.getElementById('playerAvatarImg').style.display = 'block';
                document.getElementById('playerAvatarPlaceholder').style.display = 'none';
                document.getElementById('playerPhoto').src = player.image_url;
                document.getElementById('playerPhoto').onerror = function() {
                    this.style.display = 'none';
                    document.getElementById('playerAvatarPlaceholder').style.display = 'flex';
                };
            } else {
                document.getElementById('playerAvatarImg').style.display = 'none';
                document.getElementById('playerAvatarPlaceholder').style.display = 'flex';
                document.getElementById('playerInitial').textContent =
                    player.name.charAt(0).toUpperCase();
            }

            document.getElementById('playerName').textContent = player.name;
            document.getElementById('playerIdBadge').textContent = player.player_id;
            document.getElementById('playerRole').textContent = player.role;

            const sb = document.getElementById('statusBadge');
            sb.textContent = player.status.toUpperCase();
            sb.className = 'badge-status badge-' + player.status;

            const details = [{
                    label: 'Mobile',
                    value: player.mobile || '—'
                },
                {
                    label: 'City',
                    value: player.city || '—'
                },
                {
                    label: 'Age',
                    value: player.age || '—'
                },
                {
                    label: 'Jersey No.',
                    value: player.jersey_number || '—'
                },
                {
                    label: 'Batting',
                    value: player.batting_style || '—'
                },
                {
                    label: 'Bowling',
                    value: player.bowling_style || '—'
                },
                {
                    label: 'Experience',
                    value: player.experience || '—'
                },
                {
                    label: 'Base Price',
                    value: player.base_price > 0 ?
                        '₹' + Number(player.base_price).toLocaleString() : '—'
                },
            ];

            document.getElementById('playerDetailsGrid').innerHTML =
                details.map(d => `
                <div class="col-6">
                    <div class="detail-box">
                        <div class="d-label">${d.label}</div>
                        <div class="d-value">${d.value}</div>
                    </div>
                </div>
            `).join('');

            if (player.base_price > 0) {
                document.getElementById('basePriceHint').textContent =
                    'Base: ₹' + Number(player.base_price).toLocaleString();
                if (!document.getElementById('soldPrice').value) {
                    document.getElementById('soldPrice').value = player.base_price;
                }
            }

            // Show assign form for ALL statuses (allow editing sold players)
            document.getElementById('alreadySoldWarning').style.display = 'none';
            document.getElementById('assignForm').style.opacity = '1';
            document.getElementById('assignForm').style.pointerEvents = 'auto';

            // If already sold — show warning but still allow editing
            if (player.status === 'sold') {
                document.getElementById('alreadySoldWarning').style.display = 'block';
            }

            document.getElementById('selectedPlayerId').value = player.id;

            // Pre-select sold/unsold status
            if (player.status === 'unsold') {
                document.getElementById('auctionStatus').value = 'unsold';
                toggleTeamSelect();
            } else {
                document.getElementById('auctionStatus').value = 'sold';
                toggleTeamSelect();
            }
        }

        function showNotFound() {
            document.getElementById('emptyState').style.display = 'none';
            document.getElementById('playerCard').style.display = 'none';
            document.getElementById('notFound').style.display = 'block';
        }

        function clearPanel() {
            document.getElementById('searchPlayerInput').value = '';
            document.getElementById('emptyState').style.display = 'block';
            document.getElementById('playerCard').style.display = 'none';
            document.getElementById('notFound').style.display = 'none';
            selectedTeamId = null;
            document.querySelectorAll('.team-select-card')
                .forEach(c => c.classList.remove('selected'));
            hideNavBar();
        }

        function toggleTeamSelect() {
            const status = document.getElementById('auctionStatus').value;
            const teamCol = document.getElementById('teamSelectCol');
            const priceCol = document.getElementById('soldPriceCol');
            teamCol.style.display = status === 'unsold' ? 'none' : 'block';
            priceCol.style.display = status === 'unsold' ? 'none' : 'block';
        }

        function selectTeam(id, name, el) {
            selectedTeamId = id;
            document.getElementById('selectedTeamId').value = id;
            document.querySelectorAll('.team-select-card')
                .forEach(c => c.classList.remove('selected'));
            el.classList.add('selected');
        }

        async function assignPlayer() {
            const playerId = document.getElementById('selectedPlayerId').value;
            const status = document.getElementById('auctionStatus').value;
            const price = document.getElementById('soldPrice').value || 0;
            const teamId = selectedTeamId;

            if (!playerId) {
                alert('Please search for a player first.');
                return;
            }
            if (status === 'sold' && !teamId) {
                alert('Please select a team.');
                return;
            }
            if (status === 'sold' && (!price || price <= 0)) {
                alert('Please enter the final bid price.');
                return;
            }

            const btn = document.querySelector('button[onclick="assignPlayer()"]');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Assigning...';

            try {
                const res = await fetch(
                    `{{ route('auction.assign', $tournament->id) }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector(
                                'meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            player_id: playerId,
                            team_id: teamId,
                            sold_price: price,
                            status: status,
                        }),
                    }
                );
                const data = await res.json();
                if (data.success) {
                    document.getElementById('toastMessage').textContent =
                        data.message;
                    new bootstrap.Toast(
                        document.getElementById('successToast')
                    ).show();
                    setTimeout(() => {
                        // Auto load next player instead of full reload
                        if (currentNextId) {
                            quickLoad(currentNextId);
                            // Reload stats after 2s
                            setTimeout(() => location.reload(), 3000);
                        } else {
                            location.reload();
                        }
                    }, 1500);
                } else {
                    alert('Error: ' + (data.message || 'Something went wrong'));
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>Assign Player';
                }
            } catch (e) {
                alert('Network error. Please try again.');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>Assign Player';
            }
        }

        // ── EDIT ASSIGNMENT MODAL ──
        let editSelectedTeamId = null;

        function openEditModal() {
            const playerId = document.getElementById('selectedPlayerId').value;
            const playerName = document.getElementById('playerName').textContent;
            const playerPid = document.getElementById('playerIdBadge').textContent;
            const status = document.getElementById('statusBadge').textContent
                .toLowerCase();

            document.getElementById('editPlayerId').value = playerId;
            document.getElementById('editModalSubtitle').textContent =
                playerName + ' · ' + playerPid;

            // Set current status
            document.getElementById('editStatus').value =
                status === 'sold' ? 'sold' : 'unsold';

            // Show current assignment info
            const infoBox = document.getElementById('currentAssignInfo');
            infoBox.innerHTML = `
        <div style="font-size:0.72rem;font-weight:600;
                    color:#9ca3af;text-transform:uppercase;
                    letter-spacing:1px;margin-bottom:6px;">
            Current Assignment
        </div>
        <div style="font-size:0.875rem;font-weight:600;
                    color:#374151;">
            Status:
            <span class="badge-status badge-${status}">
                ${status.toUpperCase()}
            </span>
        </div>`;

            // Pre-fill price
            const currentPrice = document.getElementById('soldPrice').value;
            document.getElementById('editSoldPrice').value = currentPrice || '';

            // Reset team selection
            editSelectedTeamId = null;
            document.querySelectorAll('.edit-team-card')
                .forEach(c => c.classList.remove('selected'));

            toggleEditFields();

            new bootstrap.Modal(
                document.getElementById('editAssignModal')
            ).show();
        }

        function toggleEditFields() {
            const status = document.getElementById('editStatus').value;
            const teamCol = document.getElementById('editTeamCol');
            const priceCol = document.getElementById('editPriceCol');
            const isUnsold = status === 'unsold';
            teamCol.style.display = isUnsold ? 'none' : 'block';
            priceCol.style.display = isUnsold ? 'none' : 'block';
        }

        function selectEditTeam(id, el) {
            editSelectedTeamId = id;
            document.getElementById('editSelectedTeamId').value = id;
            document.querySelectorAll('.edit-team-card')
                .forEach(c => c.classList.remove('selected'));
            el.classList.add('selected');
        }

        async function saveEditAssignment() {
            const playerId = document.getElementById('editPlayerId').value;
            const status = document.getElementById('editStatus').value;
            const price = document.getElementById('editSoldPrice').value || 0;
            const teamId = editSelectedTeamId;

            if (status === 'sold' && !teamId) {
                alert('Please select a team.');
                return;
            }
            if (status === 'sold' && (!price || price <= 0)) {
                alert('Please enter the sold price.');
                return;
            }

            const btn = document.getElementById('saveEditBtn');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm' +
                ' me-2"></span>Saving...';

            try {
                const res = await fetch(
                    `{{ route('auction.assign', $tournament->id) }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector(
                                'meta[name="csrf-token"]'
                            ).getAttribute('content'),
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            player_id: playerId,
                            team_id: teamId || null,
                            sold_price: price,
                            status: status,
                        }),
                    }
                );

                const data = await res.json();

                if (data.success) {
                    // Close modal
                    bootstrap.Modal.getInstance(
                        document.getElementById('editAssignModal')
                    ).hide();

                    // Show success toast
                    document.getElementById('toastMessage').textContent =
                        'Assignment updated successfully!';
                    new bootstrap.Toast(
                        document.getElementById('successToast')
                    ).show();

                    // Reload page after delay
                    setTimeout(() => location.reload(), 1600);

                } else {
                    alert('Error: ' + (data.message || 'Something went wrong.'));
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>' +
                        'Save Changes';
                }
            } catch (e) {
                alert('Network error. Please try again.');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>' +
                    'Save Changes';
            }
        }
    </script>
@endpush
