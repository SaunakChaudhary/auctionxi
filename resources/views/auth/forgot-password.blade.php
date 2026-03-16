@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('content')
    <div class="auth-card">
        <h2>Reset Password 🔑</h2>
        <p class="subtitle">Enter your email to receive a reset link</p>

        @if (session('success'))
            <div class="alert alert-success mb-3">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger mb-3">
                <i class="bi bi-exclamation-circle-fill me-2"></i>
                @foreach ($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('forgot.password') }}">
            @csrf

            <div class="mb-4">
                <label class="form-label">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-envelope-fill"></i>
                    </span>
                    <input type="email" name="email" class="form-control" placeholder="you@example.com"
                        value="{{ old('email') }}" required autofocus>
                </div>
            </div>

            <button type="submit" class="btn-auth">
                <i class="bi bi-send-fill me-2"></i>
                Send Reset Link
            </button>
        </form>

        <div class="divider">or</div>

        <div class="auth-footer">
            Remember your password?
            <a href="{{ route('login') }}">Back to Login</a>
        </div>
    </div>
@endsection
