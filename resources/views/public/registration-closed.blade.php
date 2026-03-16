@extends('layouts.public')

@section('title', 'Registration Closed')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6 text-center py-5">
            <div style="font-size:5rem;margin-bottom:20px;">🔒</div>
            <h2
                style="font-family:'Poppins',sans-serif;font-weight:800;
                   color:#1e1e2e;margin-bottom:10px;">
                Registration Closed
            </h2>
            <p style="color:#6b7280;margin-bottom:8px;">
                Player registration for
                <strong>{{ $tournament->name }}</strong>
                is currently closed.
            </p>
            <p style="color:#9ca3af;font-size:0.875rem;margin-bottom:32px;">
                Please contact the tournament organizer for more information.
            </p>
            <a href="{{ route('public.live', $tournament->code) }}" class="btn btn-primary">
                <i class="bi bi-eye-fill me-2"></i>
                Watch Live Auction
            </a>
        </div>
    </div>
@endsection
