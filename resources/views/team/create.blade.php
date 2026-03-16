@extends('layouts.app')

@section('title', 'Add Team')
@section('page-title', 'Add Team')

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
                    <a href="{{ route('team.index', $tournament->id) }}">Teams</a>
                </li>
                <li class="breadcrumb-item active">Add Team</li>
            </ol>
        </nav>
        <h1 class="mt-2">Add New Team</h1>
        <p>Create a team for {{ $tournament->name }}</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-people-fill text-warning"></i>
                    Team Details
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('team.store', $tournament->id) }}" enctype="multipart/form-data">
                        @csrf

                        {{-- Team Info --}}
                        <div class="mb-4">
                            <div class="section-divider mb-3">
                                Team Information
                            </div>
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label">
                                        Team Name
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="name"
                                        class="form-control
                                       @error('name') is-invalid @enderror"
                                        placeholder="e.g. Thunder Strikers" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Team Logo</label>
                                    <input type="file" name="logo" class="form-control" accept="image/*"
                                        onchange="previewLogo(this)">
                                    <div id="logoPreview" style="display:none;margin-top:10px;">
                                        <img id="previewImg" src=""
                                            style="width:60px;height:60px;
                                                border-radius:12px;
                                                object-fit:cover;">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">
                                        Team Description
                                    </label>
                                    <textarea name="description" class="form-control" rows="2" placeholder="Optional team description...">{{ old('description') }}</textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Owner Info --}}
                        <div class="mb-4">
                            <div class="section-divider mb-3">
                                Owner Information
                            </div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">
                                        Owner Name
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="owner_name"
                                        class="form-control
                                       @error('owner_name')
                                       is-invalid @enderror"
                                        placeholder="Owner full name" value="{{ old('owner_name') }}" required>
                                    @error('owner_name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">
                                        Owner Mobile
                                    </label>
                                    <input type="text" name="owner_mobile" class="form-control"
                                        placeholder="Mobile number" value="{{ old('owner_mobile') }}">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">
                                        Owner Email
                                    </label>
                                    <input type="email" name="owner_email" class="form-control"
                                        placeholder="owner@email.com" value="{{ old('owner_email') }}">
                                </div>
                            </div>
                        </div>

                        {{-- Budget Info Box --}}
                        <div class="p-3 rounded-3 mb-4"
                            style="background:linear-gradient(135deg,
                                rgba(245,158,11,0.05),
                                rgba(251,191,36,0.08));
                                border:1.5px solid rgba(245,158,11,0.2);">
                            <div class="d-flex gap-3 align-items-center">
                                <i class="bi bi-wallet2-fill"
                                    style="color:#d97706;font-size:1.3rem;
                                      flex-shrink:0;"></i>
                                <div>
                                    <div
                                        style="font-weight:700;font-size:0.85rem;
                                            color:#374151;">
                                        Team Budget:
                                        ₹{{ number_format($tournament->team_budget) }}
                                    </div>
                                    <div style="font-size:0.78rem;color:#9ca3af;">
                                        Budget is automatically assigned from
                                        tournament settings
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="bi bi-people-fill me-2"></i>
                                Add Team
                            </button>
                            <a href="{{ route('team.index', $tournament->id) }}" class="btn btn-outline-primary">
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
        .section-divider {
            font-weight: 700;
            font-size: 0.82rem;
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
        function previewLogo(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    document.getElementById('previewImg').src = e.target.result;
                    document.getElementById('logoPreview').style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endpush
