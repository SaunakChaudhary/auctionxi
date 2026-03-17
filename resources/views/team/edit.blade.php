@extends('layouts.app')

@section('title', 'Edit Team')
@section('page-title', 'Edit Team')

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
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
        <h1 class="mt-2">Edit Team</h1>
        <p>Update details for {{ $team->name }}</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-pencil-fill text-warning"></i>
                    Editing: {{ $team->name }}
                </div>
                <div class="card-body">
                    <form method="POST"
                        action="{{ route('team.update', [$tournament->id, $team->id]) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">

                            {{-- Current Logo --}}
                            @if ($team->logo)
                                <div class="col-12">
                                    <div class="d-flex align-items-center gap-3
                                        p-3 rounded-3"
                                        style="background:#f8f9ff;
                                        border:1.5px solid #e8eaf0;">
                                        <img src="{{ teamLogoSrc($team) }}" alt="{{ $team->name }}"
                                            style="width:56px;height:56px;
                                            border-radius:12px;
                                            object-fit:cover;">
                                        <div>
                                            <div
                                                style="font-weight:600;
                                                font-size:0.85rem;">
                                                Current Logo
                                            </div>
                                            <div
                                                style="font-size:0.78rem;
                                                color:#9ca3af;">
                                                Upload new to replace
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="col-md-8">
                                <label class="form-label">
                                    Team Name
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="name" class="form-control"
                                    value="{{ old('name', $team->name) }}" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">New Logo</label>
                                <input type="file" name="logo" class="form-control" accept="image/*"
                                    onchange="previewLogo(this)">
                                <div id="logoPreview" style="display:none;margin-top:8px;">
                                    <img id="previewImg" src=""
                                        style="width:50px;height:50px;
                                            border-radius:10px;
                                            object-fit:cover;">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">
                                    Owner Name
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="owner_name" class="form-control"
                                    value="{{ old('owner_name', $team->owner_name) }}"
                                    required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Owner Mobile</label>
                                <input type="text" name="owner_mobile" class="form-control"
                                    value="{{ old('owner_mobile', $team->owner_mobile) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Owner Email</label>
                                <input type="email" name="owner_email" class="form-control"
                                    value="{{ old('owner_email', $team->owner_email) }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="2">{{ old('description', $team->description) }}</textarea>
                            </div>

                            <div class="col-12 d-flex gap-3 mt-2">
                                <button type="submit" class="btn btn-primary flex-fill">
                                    <i class="bi bi-check-circle-fill me-2"></i>
                                    Update Team
                                </button>
                                <a href="{{ route('team.index', $tournament->id) }}" class="btn btn-outline-primary">
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
