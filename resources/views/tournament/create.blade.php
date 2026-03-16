@extends('layouts.app')

@section('title', 'Create Tournament')
@section('page-title', 'Create Tournament')

@section('content')

    <div class="page-header">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('tournament.index') }}">Tournaments</a>
                </li>
                <li class="breadcrumb-item active">Create</li>
            </ol>
        </nav>
        <h1 class="mt-2">Create New Tournament</h1>
        <p>Fill in the details to set up your cricket tournament.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-trophy-fill text-warning"></i>
                    Tournament Details
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('tournament.store') }}">
                        @csrf

                        {{-- Required Fields --}}
                        <div class="mb-4">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <div
                                    style="width:4px;height:20px;
                                        background:linear-gradient(135deg,
                                        #6c3fc5,#8b5cf6);
                                        border-radius:2px;">
                                </div>
                                <span
                                    style="font-weight:700;font-size:0.9rem;
                                         color:#374151;">
                                    Required Information
                                </span>
                            </div>

                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">
                                        Tournament Name
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="name"
                                        class="form-control
                                       @error('name') is-invalid @enderror"
                                        placeholder="e.g. Premier Cricket League 2024" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        Number of Teams
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" name="number_of_teams"
                                        class="form-control
                                       @error('number_of_teams')
                                       is-invalid @enderror"
                                        placeholder="e.g. 8" min="2" max="32"
                                        value="{{ old('number_of_teams') }}" required>
                                    @error('number_of_teams')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        Team Budget (₹)
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" name="team_budget"
                                        class="form-control
                                       @error('team_budget')
                                       is-invalid @enderror"
                                        placeholder="e.g. 1000000" min="0" value="{{ old('team_budget') }}" required>
                                    @error('team_budget')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text" style="color:#9ca3af;font-size:0.78rem;">
                                        Each team will receive this budget
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Optional Fields --}}
                        <div class="mb-4">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <div
                                    style="width:4px;height:20px;
                                        background:linear-gradient(135deg,
                                        #d97706,#f59e0b);
                                        border-radius:2px;">
                                </div>
                                <span
                                    style="font-weight:700;font-size:0.9rem;
                                         color:#374151;">
                                    Optional Information
                                </span>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Tournament Location
                                    </label>
                                    <input type="text" name="location" class="form-control"
                                        placeholder="e.g. Mumbai, Maharashtra" value="{{ old('location') }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        Tournament Start Date
                                    </label>
                                    <input type="date" name="start_date" class="form-control"
                                        value="{{ old('start_date') }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Auction Date</label>
                                    <input type="date" name="auction_date" class="form-control"
                                        value="{{ old('auction_date') }}">
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control" rows="3" placeholder="Brief description of the tournament...">{{ old('description') }}</textarea>
                                </div>
                            </div>

                            {{-- ADD THIS NEW FIELD --}}
                            <div class="col-md-6">
                                <label class="form-label">
                                    Default Player Base Price (₹)
                                </label>
                                <input type="number" name="default_base_price" class="form-control"
                                    placeholder="e.g. 50000" min="0" value="{{ old('default_base_price', 0) }}">
                                <div class="form-text" style="color:#9ca3af;font-size:0.78rem;">
                                    Auto-assigned to every registered player
                                </div>
                            </div>
                        </div>

                        {{-- Info Box --}}
                        <div class="p-3 rounded-3 mb-4"
                            style="background:linear-gradient(135deg,
                                rgba(108,63,197,0.05),
                                rgba(139,92,246,0.08));
                                border:1.5px solid rgba(108,63,197,0.15);">
                            <div class="d-flex gap-3 align-items-start">
                                <i class="bi bi-info-circle-fill"
                                    style="color:#6c3fc5;font-size:1.1rem;
                                      margin-top:2px;flex-shrink:0;"></i>
                                <div>
                                    <div
                                        style="font-weight:600;font-size:0.85rem;
                                            color:#374151;margin-bottom:4px;">
                                        What happens after creation?
                                    </div>
                                    <ul
                                        style="font-size:0.82rem;color:#6b7280;
                                           margin:0;padding-left:16px;">
                                        <li>A unique Tournament Code is auto-generated</li>
                                        <li>A public Player Registration link is created</li>
                                        <li>A public Live Viewer link is created</li>
                                        <li>You can add teams and manage players</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="bi bi-trophy-fill me-2"></i>
                                Create Tournament
                            </button>
                            <a href="{{ route('tournament.index') }}" class="btn btn-outline-primary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
