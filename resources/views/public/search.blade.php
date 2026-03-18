@extends('layouts.public')

@section('title', 'Search Player — ' . $tournament->name)

@section('nav-items')
    <a href="{{ route('public.live', $tournament->code) }}" class="nav-pill">
        <i class="bi bi-arrow-left me-1"></i>Live View
    </a>
@endsection

@section('refresh-bar')
    {{-- No auto-refresh on search page --}}
@endsection

@section('content')

    {{-- Header --}}
    <div class="card mb-4">
        <div class="card-body" style="padding:20px 24px;">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <a href="{{ route('public.live', $tournament->code) }}"
                    style="display:inline-flex;align-items:center;gap:6px;
                          color:var(--muted);font-size:0.82rem;font-weight:600;
                          text-decoration:none;">
                    <i class="bi bi-arrow-left"></i> Back to Live
                </a>
                <div style="width:1px;height:20px;background:var(--border);"></div>
                <div>
                    <h1
                        style="font-family:'Poppins',sans-serif;font-weight:800;
                               font-size:1.2rem;color:var(--dark);margin:0;">
                        <i class="bi bi-person-search me-2" style="color:var(--primary-lt);"></i>
                        Player Search
                    </h1>
                    <div style="font-size:0.75rem;color:var(--muted);margin-top:2px;">
                        {{ $tournament->name }} — Search by Player ID or Name
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Search Panel --}}
    <div class="card mb-4">
        <div class="card-body" style="padding:20px 24px;">

            {{-- Search Bar --}}
            <div class="d-flex gap-2 mb-3" style="max-width:520px;">
                <input type="text" id="searchInput" class="form-control"
                    placeholder="Enter Player ID (PX1001) or Name..." style="font-weight:600;letter-spacing:0.5px;"
                    autocomplete="off" onkeydown="if(event.key==='Enter') doSearch()">
                <button onclick="doSearch()" class="btn btn-primary" style="white-space:nowrap;min-width:90px;">
                    <i class="bi bi-search me-1"></i>Search
                </button>
                <button onclick="clearSearch()" class="btn btn-outline-secondary" title="Clear">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            {{-- Prev / Next / Counter --}}
            <div class="d-flex align-items-center gap-2 mb-3" id="navBar"
                style="{{ count($allPlayerIds) > 0 ? '' : 'display:none!important;' }}">
                <button type="button" id="btnFirst" onclick="goFirst()" class="btn btn-outline-secondary btn-sm"
                    title="First Player">
                    <i class="bi bi-skip-start-fill"></i>
                </button>
                <button type="button" id="btnPrev" onclick="navigate(-1)" class="btn btn-outline-secondary btn-sm"
                    disabled>
                    <i class="bi bi-chevron-left"></i> Prev
                </button>
                <span id="navCounter"
                    style="font-size:0.8rem;font-weight:600;color:var(--muted);
                             white-space:nowrap;padding:0 8px;">
                    @if (count($allPlayerIds) > 0)
                        Player 1 of {{ count($allPlayerIds) }}
                    @else
                        No players
                    @endif
                </span>
                <button type="button" id="btnNext" onclick="navigate(1)" class="btn btn-outline-secondary btn-sm"
                    {{ count($allPlayerIds) > 1 ? '' : 'disabled' }}>
                    Next <i class="bi bi-chevron-right"></i>
                </button>
            </div>

        </div>
    </div>

    {{-- Player Result Card --}}
    <div id="playerResult" style="{{ $firstApproved ? '' : 'display:none;' }}">
        @if ($firstApproved)
            @php
                $__src = playerAvatarSrc($firstApproved);
                $__ini = strtoupper(substr($firstApproved->name ?? '?', 0, 1));
                $__bg = playerAvatarColor($firstApproved->name ?? '');
            @endphp
            <div class="card">
                <div class="card-body" style="padding:24px;">
                    <div class="d-flex gap-4 align-items-start flex-wrap">

                        {{-- Avatar --}}
                        <div id="pAvatarWrap" style="flex-shrink:0;">
                            @if ($__src)
                                <img id="pAvatarImg" src="{{ $__src }}"
                                    style="width:110px;height:130px;border-radius:12px;
                                            object-fit:cover;object-position:top;
                                            border:3px solid var(--border);"
                                    onerror="this.style.display='none';
                                              document.getElementById('pAvatarPh').style.display='flex';">
                                <div id="pAvatarPh"
                                    style="display:none;width:110px;height:130px;border-radius:12px;
                                            background:{{ $__bg }};color:#fff;font-weight:800;
                                            font-size:3rem;align-items:center;justify-content:center;">
                                    {{ $__ini }}
                                </div>
                            @else
                                <img id="pAvatarImg" src="" style="display:none;width:110px;height:130px;">
                                <div id="pAvatarPh"
                                    style="width:110px;height:130px;border-radius:12px;
                                            background:{{ $__bg }};color:#fff;font-weight:800;
                                            font-size:3rem;display:flex;align-items:center;
                                            justify-content:center;">
                                    <span id="pAvatarInitial">{{ $__ini }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div style="flex:1;min-width:200px;">
                            <div
                                style="display:flex;align-items:center;gap:10px;
                                        flex-wrap:wrap;margin-bottom:4px;">
                                <h2 id="pName"
                                    style="font-family:'Poppins',sans-serif;font-weight:800;
                                                      font-size:1.3rem;color:var(--dark);margin:0;">
                                    {{ $firstApproved->name }}
                                </h2>
                                <span id="pIdBadge" class="pid-badge">
                                    {{ $firstApproved->player_id }}
                                </span>
                                <span id="pStatusBadge" class="status-badge status-{{ $firstApproved->status }}">
                                    {{ ucfirst($firstApproved->status) }}
                                </span>
                            </div>

                            <div id="pRole" class="role-badge mb-3" style="display:inline-block;">
                                {{ $firstApproved->role }}
                            </div>

                            {{-- 2-col detail grid --}}
                            <div id="pDetailsGrid" style="display:grid;grid-template-columns:1fr 1fr;gap:7px;">
                                @php
                                    $dg = [
                                        ['Mobile', $firstApproved->mobile ?? '—'],
                                        ['City', $firstApproved->city ?? '—'],
                                        ['Age', $firstApproved->age ?? '—'],
                                        ['Jersey', $firstApproved->jersey_number ?? '—'],
                                        ['Batting', $firstApproved->batting_style ?? '—'],
                                        ['Bowling', $firstApproved->bowling_style ?? '—'],
                                        ['Experience', $firstApproved->experience ?? '—'],
                                        [
                                            'Base Price',
                                            $firstApproved->base_price > 0
                                                ? '₹' . number_format($firstApproved->base_price)
                                                : '—',
                                        ],
                                    ];
                                @endphp
                                @foreach ($dg as $item)
                                    <div
                                        style="background:#f8fafc;border:1px solid #e2e8f0;
                                                border-radius:7px;padding:8px 12px;">
                                        <div
                                            style="font-size:0.6rem;font-weight:600;color:#94a3b8;
                                                    text-transform:uppercase;letter-spacing:0.4px;
                                                    margin-bottom:2px;">
                                            {{ $item[0] }}
                                        </div>
                                        <div style="font-size:0.82rem;font-weight:600;color:#0f172a;">
                                            {{ $item[1] }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Not Found State --}}
    <div id="stateNotFound" style="display:none;">
        <div class="card">
            <div class="card-body text-center py-5">
                <div style="font-size:2.5rem;margin-bottom:12px;">🔍</div>
                <h5 style="font-weight:700;margin-bottom:6px;">Player Not Found</h5>
                <p style="color:var(--muted);font-size:0.875rem;" id="notFoundMsg">
                    No player found with that ID or name.
                </p>
            </div>
        </div>
    </div>

    {{-- Empty State --}}
    <div id="stateEmpty" style="{{ count($allPlayerIds) > 0 ? 'display:none;' : '' }}">
        <div class="card">
            <div class="card-body text-center py-5">
                <div style="font-size:2.5rem;margin-bottom:12px;">🏏</div>
                <h5 style="font-weight:700;margin-bottom:6px;">No Players Registered</h5>
                <p style="color:var(--muted);font-size:0.875rem;">
                    Players will appear once they register.
                </p>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        'use strict';

        const SEARCH_URL = "{{ route('public.search', $tournament->code) }}";
        const ALL_IDS = @json($allPlayerIds);
        let currentIdx = 0;

        // Initial display: set first approved index
        @if ($firstApproved)
            currentIdx = ALL_IDS.indexOf("{{ $firstApproved->player_id }}");
            if (currentIdx < 0) currentIdx = 0;
            updateNav();
        @endif

        // ── Search ──
        async function doSearch() {
            const q = document.getElementById('searchInput').value.trim();
            if (!q) return;

            try {
                const res = await fetch(SEARCH_URL + '?q=' + encodeURIComponent(q));
                const data = await res.json();

                if (!data) {
                    showNotFound(q);
                    return;
                }

                currentIdx = data._index ?? 0;
                renderPlayer(data);
                updateNav();
            } catch (e) {
                showNotFound(q);
            }
        }

        // ── Navigate ──
        function navigate(dir) {
            const ni = currentIdx + dir;
            if (ni < 0 || ni >= ALL_IDS.length) return;
            currentIdx = ni;
            loadById(ALL_IDS[currentIdx]);
        }

        function goFirst() {
            currentIdx = 0;
            loadById(ALL_IDS[0]);
        }

        async function loadById(pid) {
            document.getElementById('searchInput').value = pid;
            try {
                const res = await fetch(SEARCH_URL + '?q=' + encodeURIComponent(pid));
                const data = await res.json();
                if (data) {
                    renderPlayer(data);
                    updateNav();
                }
            } catch (e) {}
        }

        function updateNav() {
            const prev = document.getElementById('btnPrev');
            const next = document.getElementById('btnNext');
            const counter = document.getElementById('navCounter');
            if (prev) prev.disabled = (currentIdx <= 0);
            if (next) next.disabled = (currentIdx >= ALL_IDS.length - 1);
            if (counter) counter.textContent = 'Player ' + (currentIdx + 1) + ' of ' + ALL_IDS.length;
        }

        // ── Clear ──
        function clearSearch() {
            document.getElementById('searchInput').value = '';
            document.getElementById('playerResult').style.display = 'none';
            document.getElementById('stateNotFound').style.display = 'none';
            if (ALL_IDS.length > 0) {
                document.getElementById('stateEmpty').style.display = 'none';
                currentIdx = 0;
                loadById(ALL_IDS[0]);
            } else {
                document.getElementById('stateEmpty').style.display = 'block';
            }
        }

        function showNotFound(q) {
            document.getElementById('playerResult').style.display = 'none';
            document.getElementById('stateNotFound').style.display = 'block';
            document.getElementById('notFoundMsg').textContent =
                'No player found matching "' + q + '"';
        }

        // ── Render Player ──
        function renderPlayer(p) {
            document.getElementById('stateNotFound').style.display = 'none';
            document.getElementById('stateEmpty').style.display = 'none';
            document.getElementById('playerResult').style.display = 'block';

            // Avatar
            const src = p.photo ? p.photo :
                p.image_url ? p.image_url :
                null;
            const initial = (p.name || '?').charAt(0).toUpperCase();

            const imgEl = document.getElementById('pAvatarImg');
            const phEl = document.getElementById('pAvatarPh');
            const iniEl = document.getElementById('pAvatarInitial');

            if (src) {
                imgEl.src = src;
                imgEl.style.display = 'block';
                phEl.style.display = 'none';
                imgEl.onerror = function() {
                    this.style.display = 'none';
                    phEl.style.display = 'flex';
                    if (iniEl) iniEl.textContent = initial;
                };
            } else {
                imgEl.style.display = 'none';
                phEl.style.display = 'flex';
                if (iniEl) iniEl.textContent = initial;
            }

            // Text fields
            document.getElementById('pName').textContent = p.name || '';
            document.getElementById('pIdBadge').textContent = p.player_id || '';

            const statusBadge = document.getElementById('pStatusBadge');
            const statusLabels = {
                approved: 'Approved',
                sold: 'Sold',
                unsold: 'Unsold',
                pending: 'Pending'
            };
            statusBadge.textContent = statusLabels[p.status] || p.status;
            statusBadge.className = 'status-badge status-' + (p.status || 'approved');

            document.getElementById('pRole').textContent = p.role || '';

            // Details grid
            const fmt = v => v ? v : '—';
            const fmtPrice = v => v > 0 ? '₹' + Number(v).toLocaleString('en-IN') : '—';
            const fields = [
                ['Mobile', fmt(p.mobile)],
                ['City', fmt(p.city)],
                ['Age', fmt(p.age)],
                ['Jersey', fmt(p.jersey_number)],
                ['Batting', fmt(p.batting_style)],
                ['Bowling', fmt(p.bowling_style)],
                ['Experience', fmt(p.experience)],
                ['Base Price', fmtPrice(p.base_price)],
            ];

            document.getElementById('pDetailsGrid').innerHTML = fields.map(([lbl, val]) => `
        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:7px;padding:8px 12px;">
            <div style="font-size:0.6rem;font-weight:600;color:#94a3b8;text-transform:uppercase;
                        letter-spacing:0.4px;margin-bottom:2px;">${lbl}</div>
            <div style="font-size:0.82rem;font-weight:600;color:#0f172a;">${val}</div>
        </div>
    `).join('');
        }
    </script>
@endpush
