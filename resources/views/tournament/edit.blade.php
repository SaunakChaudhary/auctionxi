@extends('layouts.app')

@section('title', 'Edit Tournament')
@section('page-title', 'Edit Tournament')

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
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
        <h1 class="mt-2">Edit Tournament</h1>
        <p>Update your tournament details.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-pencil-fill text-warning"></i>
                    Edit: {{ $tournament->name }}
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('tournament.update', $tournament->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">
                                    Tournament Name
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="name"
                                    class="form-control
                                   @error('name') is-invalid @enderror"
                                    value="{{ old('name', $tournament->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    Number of Teams
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="number_of_teams" class="form-control" min="2"
                                    value="{{ old('number_of_teams', $tournament->number_of_teams) }}"
                                    required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    Team Budget (₹)
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="team_budget" class="form-control" min="0"
                                    value="{{ old('team_budget', $tournament->team_budget) }}"
                                    required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Location</label>
                                <input type="text" name="location" class="form-control"
                                    value="{{ old('location', $tournament->location) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">
                                    Default Player Base Price (₹)
                                </label>
                                <input type="number" name="default_base_price" class="form-control" min="0"
                                    value="{{ old('default_base_price', $tournament->default_base_price) }}">
                                <div class="form-text" style="color:#9ca3af;font-size:0.78rem;">
                                    Applied to all newly registered players
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Start Date</label>
                                <input type="date" name="start_date" class="form-control"
                                    value="{{ old(
                                        'start_date',
                                        $tournament->start_date ? \Carbon\Carbon::parse($tournament->start_date)->format('Y-m-d') : '',
                                    ) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Auction Date</label>
                                <input type="date" name="auction_date" class="form-control"
                                    value="{{ old(
                                        'auction_date',
                                        $tournament->auction_date ? \Carbon\Carbon::parse($tournament->auction_date)->format('Y-m-d') : '',
                                    ) }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description', $tournament->description) }}</textarea>
                            </div>

                            <div class="col-12 d-flex gap-3 mt-2">
                                <button type="submit" class="btn btn-primary flex-fill">
                                    <i class="bi bi-check-circle-fill me-2"></i>
                                    Update Tournament
                                </button>
                                <a href="{{ route('tournament.show', $tournament->id) }}"
                                    class="btn btn-outline-primary">
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
