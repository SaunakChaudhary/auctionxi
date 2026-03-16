@extends('layouts.app')

@section('title', 'Edit Player')
@section('page-title', 'Edit Player')

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
                    <a href="{{ route('player.index', $tournament->id) }}">
                        Players
                    </a>
                </li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
        <h1 class="mt-2">Edit Player</h1>
        <p>Update details for {{ $player->name }}</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header d-flex align-items-center gap-3">
                    @if ($player->photo)
                        <img src="{{ Storage::url($player->photo) }}" class="player-avatar" alt="">
                    @else
                        <div class="player-avatar-placeholder">
                            {{ strtoupper(substr($player->name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <div style="font-weight:700;">{{ $player->name }}</div>
                        <div style="font-size:0.78rem;color:#9ca3af;">
                            {{ $player->player_id }}
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST"
                        action="{{ route('player.update', [$tournament->id, $player->id]) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Basic Info --}}
                        <div class="section-label mb-3">Basic Information</div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">
                                    Player Name
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="name" class="form-control"
                                    value="{{ old('name', $player->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">
                                    Role
                                    <span class="text-danger">*</span>
                                </label>
                                <select name="role" class="form-select" required>
                                    @foreach (['Batsman', 'Bowler', 'All Rounder', 'Wicket Keeper'] as $role)
                                        <option value="{{ $role }}"
                                            {{ old('role', $player->role) === $role ? 'selected' : '' }}>
                                            {{ $role }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">
                                    Mobile
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="mobile" class="form-control"
                                    value="{{ old('mobile', $player->mobile) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control"
                                    value="{{ old('email', $player->email) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Age</label>
                                <input type="number" name="age" class="form-control"
                                    value="{{ old('age', $player->age) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Jersey Number</label>
                                <input type="text" name="jersey_number" class="form-control"
                                    value="{{ old('jersey_number', $player->jersey_number) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">City</label>
                                <input type="text" name="city" class="form-control"
                                    value="{{ old('city', $player->city) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Base Price (₹)</label>
                                <input type="number" name="base_price" class="form-control" min="0"
                                    value="{{ old('base_price', $player->base_price) }}">
                            </div>
                        </div>

                        {{-- Cricket Details --}}
                        <div class="section-label mb-3">Cricket Details</div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Batting Style</label>
                                <select name="batting_style" class="form-select">
                                    <option value="">Select</option>
                                    @foreach (['Right Hand', 'Left Hand'] as $bs)
                                        <option value="{{ $bs }}"
                                            {{ old('batting_style', $player->batting_style) === $bs ? 'selected' : '' }}>
                                            {{ $bs }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Bowling Style</label>
                                <select name="bowling_style" class="form-select">
                                    <option value="">Select</option>
                                    @foreach (['Right Arm Fast', 'Right Arm Medium', 'Left Arm Fast', 'Left Arm Medium', 'Right Arm Spin', 'Left Arm Spin'] as $bls)
                                        <option value="{{ $bls }}"
                                            {{ old('bowling_style', $player->bowling_style) === $bls ? 'selected' : '' }}>
                                            {{ $bls }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Experience</label>
                                <select name="experience" class="form-select">
                                    <option value="">Select</option>
                                    @foreach (['Beginner', 'Intermediate', 'Advanced', 'Professional'] as $exp)
                                        <option value="{{ $exp }}"
                                            {{ old('experience', $player->experience) === $exp ? 'selected' : '' }}>
                                            {{ $exp }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Photo --}}
                        <div class="section-label mb-3">Profile Photo</div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <input type="file" name="photo" class="form-control" accept="image/*"
                                    onchange="previewPhoto(this)">
                                <div
                                    style="font-size:0.78rem;
                                        color:#9ca3af;margin-top:4px;">
                                    Leave empty to keep current photo
                                </div>
                            </div>
                            <div class="col-md-6 d-flex align-items-center gap-3">
                                @if ($player->photo)
                                    <img src="{{ Storage::url($player->photo) }}"
                                        style="width:56px;height:56px;
                                        border-radius:50%;object-fit:cover;
                                        border:2px solid #e8eaf0;"
                                        id="currentPhoto" alt="Current">
                                    <span style="font-size:0.78rem;color:#9ca3af;">
                                        Current Photo
                                    </span>
                                @endif
                                <img id="newPhoto" src=""
                                    style="display:none;width:56px;
                                        height:56px;border-radius:50%;
                                        object-fit:cover;
                                        border:2px solid #6c3fc5;">
                            </div>
                        </div>

                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                Update Player
                            </button>
                            <a href="{{ route('player.index', $tournament->id) }}" class="btn btn-outline-primary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <style>
        .section-label {
            font-size: 0.78rem;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding-bottom: 8px;
            border-bottom: 1.5px solid #f0f2f8;
        }
    </style>
@endpush

@push('scripts')
    <script>
        function previewPhoto(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const img = document.getElementById('newPhoto');
                    img.src = e.target.result;
                    img.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endpush
