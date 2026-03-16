@extends('layouts.app')

@section('title', 'My Tournaments')
@section('page-title', 'My Tournaments')

@section('content')

    <div class="page-header">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h1>My Tournaments</h1>
                <p>Manage all your cricket tournaments from here.</p>
            </div>
            <a href="{{ route('tournament.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle-fill me-2"></i>Create Tournament
            </a>
        </div>
    </div>

    @if ($tournaments->count() > 0)
        <div class="row g-4">
            @foreach ($tournaments as $tournament)
                <div class="col-md-6 col-xl-4">
                    <div class="tournament-card card h-100">
                        {{-- Card Top Bar --}}
                        <div class="tournament-card-top"
                            style="background: linear-gradient(135deg,#6c3fc5,#8b5cf6);
                        padding:20px 24px;
                        border-radius:16px 16px 0 0;
                        position:relative;
                        overflow:hidden;">
                            <div
                                style="position:absolute;top:-20px;right:-20px;
                            width:100px;height:100px;border-radius:50%;
                            background:rgba(255,255,255,0.08);">
                            </div>
                            <div
                                style="position:absolute;bottom:-30px;right:30px;
                            width:70px;height:70px;border-radius:50%;
                            background:rgba(255,255,255,0.06);">
                            </div>
                            <div
                                class="d-flex align-items-start
                            justify-content-between position-relative">
                                <div>
                                    <div style="font-size:1.8rem;margin-bottom:8px;">🏆</div>
                                    <h5
                                        style="color:#fff;font-weight:700;
                                   font-family:'Poppins',sans-serif;
                                   margin:0;font-size:1.05rem;">
                                        {{ $tournament->name }}
                                    </h5>
                                    @if ($tournament->location)
                                        <div
                                            style="color:rgba(255,255,255,0.6);
                                    font-size:0.78rem;margin-top:4px;">
                                            <i class="bi bi-geo-alt-fill me-1"></i>
                                            {{ $tournament->location }}
                                        </div>
                                    @endif
                                </div>
                                <code
                                    style="background:rgba(255,255,255,0.15);
                                 color:#fff;padding:4px 10px;
                                 border-radius:6px;font-size:0.75rem;
                                 font-weight:700;letter-spacing:1px;">
                                    {{ $tournament->code }}
                                </code>
                            </div>
                        </div>

                        {{-- Card Body --}}
                        <div class="card-body p-0">
                            {{-- Stats Row --}}
                            <div class="d-flex border-bottom">
                                <div class="flex-fill text-center py-3 border-end">
                                    <div
                                        style="font-weight:700;font-size:1.1rem;
                                    color:#1e1e2e;">
                                        {{ $tournament->teams->count() }}
                                        <small style="color:#9ca3af;font-size:0.7rem;">
                                            /{{ $tournament->number_of_teams }}
                                        </small>
                                    </div>
                                    <div
                                        style="font-size:0.72rem;color:#9ca3af;
                                    font-weight:500;">
                                        Teams</div>
                                </div>
                                <div class="flex-fill text-center py-3 border-end">
                                    <div
                                        style="font-weight:700;font-size:1.1rem;
                                    color:#1e1e2e;">
                                        {{ $tournament->players->count() }}
                                    </div>
                                    <div
                                        style="font-size:0.72rem;color:#9ca3af;
                                    font-weight:500;">
                                        Players</div>
                                </div>
                                <div class="flex-fill text-center py-3">
                                    <div
                                        style="font-weight:700;font-size:1rem;
                                    color:#1e1e2e;">
                                        ₹{{ number_format($tournament->team_budget) }}
                                    </div>
                                    <div
                                        style="font-size:0.72rem;color:#9ca3af;
                                    font-weight:500;">
                                        Budget/Team</div>
                                </div>
                            </div>

                            {{-- Status + Date --}}
                            <div
                                class="px-4 py-3 d-flex
                            align-items-center justify-content-between">
                                <div class="d-flex gap-2">
                                    <span
                                        class="badge-status
                            badge-{{ $tournament->registration_status }}">
                                        Reg: {{ ucfirst($tournament->registration_status) }}
                                    </span>
                                    <span
                                        class="badge-status
                            badge-{{ $tournament->auction_status }}">
                                        {{ ucfirst($tournament->auction_status) }}
                                    </span>
                                </div>
                                @if ($tournament->auction_date)
                                    <small class="text-muted">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        {{ \Carbon\Carbon::parse($tournament->auction_date)->format('d M Y') }}
                                    </small>
                                @endif
                            </div>

                            {{-- Public Links --}}
                            <div class="px-4 pb-3">
                                <div class="p-3 rounded-3 mb-2"
                                    style="background:#f8f9ff;
                                border:1.5px solid #e8eaf0;">
                                    <div
                                        style="font-size:0.7rem;font-weight:600;
                                    color:#9ca3af;text-transform:uppercase;
                                    letter-spacing:1px;margin-bottom:4px;">
                                        Player Registration Link
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <code
                                            style="font-size:0.72rem;color:#6c3fc5;
                                         flex:1;overflow:hidden;
                                         text-overflow:ellipsis;
                                         white-space:nowrap;">
                                            /auctionxi/player-register/{{ $tournament->code }}
                                        </code>
                                        <button
                                            onclick="copyLink(
                                        '{{ route('public.player.register', $tournament->code) }}',
                                        this)"
                                            class="btn btn-sm"
                                            style="padding:3px 8px;
                                           background:#6c3fc5;
                                           color:#fff;border-radius:6px;
                                           font-size:0.7rem;border:none;">
                                            <i class="bi bi-copy"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="p-3 rounded-3"
                                    style="background:#f8f9ff;
                                border:1.5px solid #e8eaf0;">
                                    <div
                                        style="font-size:0.7rem;font-weight:600;
                                    color:#9ca3af;text-transform:uppercase;
                                    letter-spacing:1px;margin-bottom:4px;">
                                        Live Viewer Link
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <code
                                            style="font-size:0.72rem;color:#059669;
                                         flex:1;overflow:hidden;
                                         text-overflow:ellipsis;
                                         white-space:nowrap;">
                                            /auctionxi/live/{{ $tournament->code }}
                                        </code>
                                        <button
                                            onclick="copyLink(
                                        '{{ route('public.live', $tournament->code) }}',
                                        this)"
                                            class="btn btn-sm"
                                            style="padding:3px 8px;
                                           background:#059669;
                                           color:#fff;border-radius:6px;
                                           font-size:0.7rem;border:none;">
                                            <i class="bi bi-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="px-4 pb-4 d-flex gap-2 flex-wrap">
                                <a href="{{ route('tournament.show', $tournament->id) }}"
                                    class="btn btn-primary btn-sm flex-fill">
                                    <i class="bi bi-gear-fill me-1"></i>Manage
                                </a>
                                <a href="{{ route('auction.panel', $tournament->id) }}"
                                    class="btn btn-warning btn-sm flex-fill">
                                    <i class="bi bi-broadcast me-1"></i>Auction
                                </a>
                                <a href="{{ route('public.live', $tournament->code) }}" target="_blank"
                                    class="btn btn-success btn-sm flex-fill">
                                    <i class="bi bi-eye-fill me-1"></i>Live
                                </a>
                                <form method="POST"
                                    action="{{ route('tournament.destroy', $tournament->id) }}"
                                    onsubmit="return confirm(
                              'Delete this tournament? This cannot be undone.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
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
        {{-- Empty State --}}
        <div class="card">
            <div class="card-body text-center py-5">
                <div style="font-size:4rem;margin-bottom:20px;">🏆</div>
                <h4 style="font-weight:700;color:#1e1e2e;margin-bottom:10px;">
                    No Tournaments Yet
                </h4>
                <p class="text-muted mb-4">
                    Create your first cricket tournament and start<br>
                    managing your player auction professionally.
                </p>
                <a href="{{ route('tournament.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle-fill me-2"></i>
                    Create First Tournament
                </a>
            </div>
        </div>
    @endif

@endsection

@push('scripts')
    <script>
        function copyLink(url, btn) {
            navigator.clipboard.writeText(url).then(() => {
                const original = btn.innerHTML;
                btn.innerHTML = '<i class="bi bi-check-lg"></i>';
                btn.style.background = '#10b981';
                setTimeout(() => {
                    btn.innerHTML = original;
                    btn.style.background = '';
                }, 2000);
            });
        }
    </script>
@endpush
