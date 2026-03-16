@extends('layouts.public')

@section('title', 'Register — ' . $tournament->name)

@section('nav-items')
<a href="{{ route('home') }}" class="nav-pill">Home</a>
<a href="{{ route('public.live', $tournament->code) }}"
   class="nav-pill">Live Auction</a>
@endsection

@section('content')

{{-- Header --}}
<div class="card mb-4">
    <div class="card-body" style="padding:24px;">
        <div class="d-flex align-items-center gap-4 flex-wrap">
            <div style="width:56px;height:56px;background:var(--primary);
                        border-radius:12px;display:flex;align-items:center;
                        justify-content:center;font-size:1.6rem;
                        flex-shrink:0;">🏏</div>
            <div>
                <div style="font-size:0.72rem;font-weight:600;
                            color:var(--muted);text-transform:uppercase;
                            letter-spacing:1px;margin-bottom:4px;">
                    Player Registration
                </div>
                <h1 style="font-family:'Poppins',sans-serif;
                           font-weight:800;font-size:1.5rem;
                           color:var(--dark);margin:0 0 6px;">
                    {{ $tournament->name }}
                </h1>
                <div class="d-flex flex-wrap gap-3">
                    @if($tournament->location)
                    <span style="font-size:0.82rem;color:var(--muted);">
                        <i class="bi bi-geo-alt me-1"></i>
                        {{ $tournament->location }}
                    </span>
                    @endif
                    @if($tournament->auction_date)
                    <span style="font-size:0.82rem;color:var(--muted);">
                        <i class="bi bi-calendar3 me-1"></i>
                        Auction:
                        {{ \Carbon\Carbon::parse($tournament->auction_date)
                           ->format('d M Y') }}
                    </span>
                    @endif
                    <span class="status-badge status-approved">
                        Registration Open
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Success Message --}}
@if(session('success'))
<div class="alert alert-success mb-4 d-flex align-items-start gap-3"
     style="padding:16px 20px;">
    <i class="bi bi-check-circle-fill"
       style="font-size:1.2rem;margin-top:1px;flex-shrink:0;"></i>
    <div>
        <div style="font-weight:700;margin-bottom:2px;">
            Registration Submitted!
        </div>
        <div>{{ session('success') }}</div>
        <div style="font-size:0.82rem;margin-top:4px;
                    color:#166534;opacity:0.8;">
            Save your Player ID — you'll need it during the auction.
        </div>
    </div>
</div>
@endif

{{-- Errors --}}
@if($errors->any())
<div class="alert alert-danger mb-4">
    <i class="bi bi-exclamation-circle me-2"></i>
    <ul class="mb-0 mt-1 ps-3">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

{{-- Form --}}
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-person-plus me-1"></i>
                Player Registration Form
            </div>
            <div class="card-body" style="padding:28px;">
                <form method="POST"
                      action="{{ route('public.player.store',
                               $tournament->code) }}"
                      enctype="multipart/form-data">
                    @csrf

                    {{-- Required --}}
                    <div class="form-section mb-4">
                        <div class="form-section-title">
                            Required Information
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">
                                    Full Name <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       name="name"
                                       class="form-control"
                                       placeholder="Your full name"
                                       value="{{ old('name') }}"
                                       required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">
                                    Playing Role
                                    <span class="text-danger">*</span>
                                </label>
                                <select name="role"
                                        class="form-select" required>
                                    <option value="">Select role</option>
                                    @foreach([
                                        'Batsman'       => '🏏 Batsman',
                                        'Bowler'        => '⚾ Bowler',
                                        'All Rounder'   => '🌟 All Rounder',
                                        'Wicket Keeper' => '🧤 Wicket Keeper',
                                    ] as $val => $label)
                                    <option value="{{ $val }}"
                                        {{ old('role')===$val
                                           ?'selected':'' }}>
                                        {{ $label }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">
                                    Mobile Number
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       name="mobile"
                                       class="form-control"
                                       placeholder="10-digit mobile number"
                                       value="{{ old('mobile') }}"
                                       required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email"
                                       name="email"
                                       class="form-control"
                                       placeholder="your@email.com"
                                       value="{{ old('email') }}">
                            </div>
                        </div>
                    </div>

                    {{-- Optional --}}
                    <div class="form-section mb-4">
                        <div class="form-section-title">
                            Additional Information
                            <span style="font-weight:400;
                                         text-transform:none;
                                         letter-spacing:0;
                                         color:var(--muted);">
                                — optional
                            </span>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Age</label>
                                <input type="number"
                                       name="age"
                                       class="form-control"
                                       placeholder="Your age"
                                       min="10" max="60"
                                       value="{{ old('age') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">City</label>
                                <input type="text"
                                       name="city"
                                       class="form-control"
                                       placeholder="Your city"
                                       value="{{ old('city') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">
                                    Jersey Number
                                </label>
                                <input type="text"
                                       name="jersey_number"
                                       class="form-control"
                                       placeholder="e.g. 7"
                                       value="{{ old('jersey_number') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">
                                    Batting Style
                                </label>
                                <select name="batting_style"
                                        class="form-select">
                                    <option value="">Select</option>
                                    <option value="Right Hand"
                                        {{ old('batting_style')==='Right Hand'
                                           ?'selected':'' }}>
                                        Right Hand
                                    </option>
                                    <option value="Left Hand"
                                        {{ old('batting_style')==='Left Hand'
                                           ?'selected':'' }}>
                                        Left Hand
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">
                                    Bowling Style
                                </label>
                                <select name="bowling_style"
                                        class="form-select">
                                    <option value="">Select</option>
                                    @foreach([
                                        'Right Arm Fast',
                                        'Right Arm Medium',
                                        'Left Arm Fast',
                                        'Left Arm Medium',
                                        'Right Arm Spin',
                                        'Left Arm Spin',
                                    ] as $bs)
                                    <option value="{{ $bs }}"
                                        {{ old('bowling_style')===$bs
                                           ?'selected':'' }}>
                                        {{ $bs }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Experience</label>
                                <select name="experience"
                                        class="form-select">
                                    <option value="">Select</option>
                                    @foreach([
                                        'Beginner',
                                        'Intermediate',
                                        'Advanced',
                                        'Professional',
                                    ] as $exp)
                                    <option value="{{ $exp }}"
                                        {{ old('experience')===$exp
                                           ?'selected':'' }}>
                                        {{ $exp }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">
                                    Player Photo
                                </label>
                                <input type="file"
                                       name="photo"
                                       class="form-control"
                                       accept="image/*"
                                       onchange="previewPhoto(this)">
                                <div id="photoPreview"
                                     style="display:none;margin-top:10px;">
                                    <img id="previewImg" src=""
                                         style="width:56px;height:56px;
                                                border-radius:8px;
                                                object-fit:cover;
                                                border:1px solid var(--border);">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Note --}}
                    <div style="background:#eff6ff;
                                border:1px solid #bfdbfe;
                                border-radius:8px;
                                padding:12px 16px;
                                font-size:0.82rem;
                                color:#1e40af;
                                margin-bottom:20px;">
                        <i class="bi bi-info-circle me-2"></i>
                        After submitting, your registration will be reviewed
                        by the organizer. You'll receive a unique
                        <strong>Player ID</strong> (e.g. PX1001) which
                        will be used during the auction.
                    </div>

                    <button type="submit"
                            class="btn btn-primary w-100"
                            style="padding:12px;font-size:0.95rem;">
                        <i class="bi bi-person-check me-2"></i>
                        Submit Registration
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .form-section-title {
        font-size: 0.72rem;
        font-weight: 700;
        color: var(--mid);
        text-transform: uppercase;
        letter-spacing: 1px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--border);
        margin-bottom: 16px;
    }
</style>
@endpush

@push('scripts')
<script>
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('photoPreview').style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush