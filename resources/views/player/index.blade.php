@extends('layouts.app')

@section('title', 'Players')
@section('page-title', 'Player Management')

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
                <li class="breadcrumb-item active">Players</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center
                justify-content-between flex-wrap gap-3 mt-2">
            <div>
                <h1>Player Management</h1>
                <p>{{ $tournament->name }} &mdash;
                    {{ $players->count() }} players registered</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('player.import', $tournament->id) }}" class="btn btn-outline-primary">
                    <i class="bi bi-upload me-2"></i>Import CSV
                </a>
                <a href="{{ route('public.player.register', $tournament->code) }}" target="_blank" class="btn btn-success">
                    <i class="bi bi-box-arrow-up-right me-2"></i>
                    Registration Link
                </a>
            </div>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="row g-3 mb-4">
        @php
            $pending = $players->where('status', 'pending')->count();
            $approved = $players->where('status', 'approved')->count();
            $sold = $players->where('status', 'sold')->count();
            $unsold = $players->where('status', 'unsold')->count();
        @endphp
        <div class="col-6 col-md-3">
            <div class="card text-center p-3">
                <div style="font-size:1.5rem;font-weight:800;
                        color:#92400e;">{{ $pending }}
                </div>
                <div style="font-size:0.75rem;color:#9ca3af;">Pending</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center p-3">
                <div style="font-size:1.5rem;font-weight:800;
                        color:#065f46;">{{ $approved }}
                </div>
                <div style="font-size:0.75rem;color:#9ca3af;">Approved</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center p-3">
                <div style="font-size:1.5rem;font-weight:800;
                        color:#4c1d95;">{{ $sold }}
                </div>
                <div style="font-size:0.75rem;color:#9ca3af;">Sold</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center p-3">
                <div style="font-size:1.5rem;font-weight:800;
                        color:#991b1b;">{{ $unsold }}
                </div>
                <div style="font-size:0.75rem;color:#9ca3af;">Unsold</div>
            </div>
        </div>
    </div>

    {{-- Filter Tabs --}}
    <div class="card mb-4">
        <div class="card-body py-2 px-3">
            <div class="d-flex gap-2 flex-wrap">
                <button class="filter-tab active" onclick="filterPlayers('all', this)">
                    All ({{ $players->count() }})
                </button>
                <button class="filter-tab" onclick="filterPlayers('pending', this)">
                    Pending ({{ $pending }})
                </button>
                <button class="filter-tab" onclick="filterPlayers('approved', this)">
                    Approved ({{ $approved }})
                </button>
                <button class="filter-tab" onclick="filterPlayers('sold', this)">
                    Sold ({{ $sold }})
                </button>
                <button class="filter-tab" onclick="filterPlayers('unsold', this)">
                    Unsold ({{ $unsold }})
                </button>
            </div>
        </div>
    </div>

    {{-- Players Table --}}
    <div class="card">
        <div class="card-header d-flex align-items-center
                justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-person-badge-fill text-primary"></i>
                Registered Players
            </div>
            {{-- Search --}}
            <div class="d-flex gap-2">
                <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Search player..."
                    style="width:200px;" oninput="searchPlayers(this.value)">
            </div>
        </div>

        @if ($players->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="playersTable">
                    <thead>
                        <tr>
                            <th>Player</th>
                            <th>Player ID</th>
                            <th>Role</th>
                            <th>Contact</th>
                            <th>Base Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($players as $player)
                            <tr class="player-row" data-status="{{ $player->status }}"
                                data-name="{{ strtolower($player->name) }}">
                                {{-- Player Info --}}
                                <td>
                                    <div class="d-flex align-items-center gap-2">@php
                                        $__src = playerAvatarSrc($player);
                                        $__initial = strtoupper(substr($player->name ?? '?', 0, 1));
                                        $__bg = playerAvatarColor($player->name ?? '');
                                    @endphp
                                        @if ($__src)
                                            <img src="{{ $__src }}" alt="{{ $player->name }}"
                                                style="width:40px;height:40px;border-radius:50%;
                object-fit:cover;flex-shrink:0;
                border:2px solid #e8eaf0;"
                                                onerror="this.style.display='none';
                  this.nextElementSibling.style.display='flex';">
                                            <div
                                                style="display:none;width:40px;height:40px;
                border-radius:50%;background:{{ $__bg }};
                color:#fff;font-weight:700;font-size:0.875rem;
                align-items:center;justify-content:center;
                flex-shrink:0;">
                                                {{ $__initial }}
                                            </div>
                                        @else
                                            <div
                                                style="width:40px;height:40px;border-radius:50%;
                background:{{ $__bg }};color:#fff;font-weight:700;
                font-size:0.875rem;display:flex;align-items:center;
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
                                            <small class="text-muted">
                                                {{ $player->city ?? '' }}
                                                {{ $player->age ? '· Age ' . $player->age : '' }}
                                            </small>
                                        </div>
                                    </div>
                                </td>

                                {{-- Player ID --}}
                                <td>
                                    <code
                                        style="background:#f0f2f8;color:#6c3fc5;
                                     padding:3px 8px;border-radius:6px;
                                     font-size:0.8rem;font-weight:700;">
                                        {{ $player->player_id }}
                                    </code>
                                </td>

                                {{-- Role --}}
                                <td>
                                    <span class="badge-status badge-approved" style="font-size:0.7rem;">
                                        {{ $player->role }}
                                    </span>
                                </td>

                                {{-- Contact --}}
                                <td>
                                    <div style="font-size:0.82rem;">
                                        {{ $player->mobile }}
                                    </div>
                                    @if ($player->email)
                                        <small class="text-muted">
                                            {{ $player->email }}
                                        </small>
                                    @endif
                                </td>

                                {{-- Base Price --}}
                                <td>
                                    <span style="font-weight:600;color:#374151;">
                                        @if ($player->base_price > 0)
                                            ₹{{ number_format($player->base_price) }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </span>
                                </td>

                                {{-- Status --}}
                                <td>
                                    <span class="badge-status
                            badge-{{ $player->status }}">
                                        {{ ucfirst($player->status) }}
                                    </span>
                                </td>

                                {{-- Actions --}}
                                <td>
                                    <div class="d-flex gap-1">
                                        {{-- Approve --}}
                                        @if ($player->status === 'pending')
                                            <form method="POST"
                                                action="{{ route('player.approve', [$tournament->id, $player->id]) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" title="Approve">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Reject/Pending --}}
                                        @if ($player->status === 'approved')
                                            <form method="POST"
                                                action="{{ route('player.reject', [$tournament->id, $player->id]) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-warning" title="Set Pending">
                                                    <i class="bi bi-arrow-counterclockwise">
                                                    </i>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Edit --}}
                                        <a href="{{ route('player.edit', [$tournament->id, $player->id]) }}"
                                            class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>

                                        {{-- View Modal --}}
                                        <button class="btn btn-sm btn-outline-secondary"
                                            onclick="viewPlayer({{ $player }})" title="View Details">
                                            <i class="bi bi-eye-fill"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="card-body text-center py-5">
                <div style="font-size:3.5rem;margin-bottom:16px;">🏏</div>
                <h5 style="font-weight:700;color:#1e1e2e;margin-bottom:8px;">
                    No Players Registered Yet
                </h5>
                <p class="text-muted mb-4">
                    Share the registration link with players.
                </p>
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <a href="{{ route('public.player.register', $tournament->code) }}" target="_blank"
                        class="btn btn-success">
                        <i class="bi bi-box-arrow-up-right me-2"></i>
                        Open Registration Link
                    </a>
                    <a href="{{ route('player.import', $tournament->id) }}" class="btn btn-outline-primary">
                        <i class="bi bi-upload me-2"></i>Import CSV
                    </a>
                </div>
            </div>
        @endif
    </div>

    {{-- Player Detail Modal --}}
    <div class="modal fade" id="playerModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border:none;border-radius:20px;overflow:hidden;">
                <div class="modal-header"
                    style="background:linear-gradient(135deg,#6c3fc5,#8b5cf6);
                        border:none;padding:20px 24px;">
                    <div class="d-flex align-items-center gap-3">
                        <div id="modalAvatar"
                            style="width:52px;height:52px;border-radius:14px;
                                background:rgba(255,255,255,0.2);
                                display:flex;align-items:center;
                                justify-content:center;font-size:1.4rem;
                                font-weight:800;color:#fff;">
                        </div>
                        <div>
                            <h5 class="modal-title mb-0" style="color:#fff;font-weight:700;" id="modalName"></h5>
                            <div style="color:rgba(255,255,255,0.7);
                                    font-size:0.8rem;"
                                id="modalRole"></div>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3" id="modalContent"></div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <style>
        .filter-tab {
            background: #f8f9ff;
            border: 1.5px solid #e8eaf0;
            border-radius: 8px;
            padding: 6px 16px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.2s;
        }

        .filter-tab:hover {
            border-color: #6c3fc5;
            color: #6c3fc5;
        }

        .filter-tab.active {
            background: #6c3fc5;
            border-color: #6c3fc5;
            color: #fff;
        }

        .player-row.hidden {
            display: none;
        }
    </style>
@endpush

@push('scripts')
    <script>
        function filterPlayers(status, btn) {
            document.querySelectorAll('.filter-tab')
                .forEach(t => t.classList.remove('active'));
            btn.classList.add('active');

            document.querySelectorAll('.player-row').forEach(row => {
                if (status === 'all' ||
                    row.dataset.status === status) {
                    row.classList.remove('hidden');
                } else {
                    row.classList.add('hidden');
                }
            });
        }

        function searchPlayers(val) {
            const q = val.toLowerCase();
            document.querySelectorAll('.player-row').forEach(row => {
                if (row.dataset.name.includes(q)) {
                    row.classList.remove('hidden');
                } else {
                    row.classList.add('hidden');
                }
            });
        }

        function viewPlayer(player) {
            document.getElementById('modalName').textContent = player.name;
            document.getElementById('modalRole').textContent = player.role;
            document.getElementById('modalAvatar').textContent =
                player.name.charAt(0).toUpperCase();

            const fields = [{
                    label: 'Player ID',
                    value: player.player_id
                },
                {
                    label: 'Mobile',
                    value: player.mobile
                },
                {
                    label: 'Email',
                    value: player.email || '—'
                },
                {
                    label: 'Age',
                    value: player.age || '—'
                },
                {
                    label: 'City',
                    value: player.city || '—'
                },
                {
                    label: 'Batting Style',
                    value: player.batting_style || '—'
                },
                {
                    label: 'Bowling Style',
                    value: player.bowling_style || '—'
                },
                {
                    label: 'Experience',
                    value: player.experience || '—'
                },
                {
                    label: 'Jersey No.',
                    value: player.jersey_number || '—'
                },
                {
                    label: 'Base Price',
                    value: player.base_price > 0 ?
                        '₹' + Number(player.base_price).toLocaleString() : '—'
                },
                {
                    label: 'Status',
                    value: player.status.toUpperCase()
                },
            ];

            const content = document.getElementById('modalContent');
            content.innerHTML = fields.map(f => `
        <div class="col-6">
            <div style="background:#f8f9ff;border-radius:10px;
                        padding:10px 12px;">
                <div style="font-size:0.68rem;font-weight:600;
                            color:#9ca3af;text-transform:uppercase;
                            letter-spacing:0.5px;margin-bottom:2px;">
                    ${f.label}
                </div>
                <div style="font-size:0.875rem;font-weight:600;
                            color:#1e1e2e;">
                    ${f.value}
                </div>
            </div>
        </div>
    `).join('');

            new bootstrap.Modal(
                document.getElementById('playerModal')
            ).show();
        }
    </script>
@endpush
