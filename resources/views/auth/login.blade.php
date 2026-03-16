@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<div class="auth-card">
    <h2>Welcome Back 👋</h2>
    <p class="subtitle">Login to your organizer account</p>

    @if(session('success'))
    <div class="alert alert-success mb-3">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger mb-3">
        <i class="bi bi-exclamation-circle-fill me-2"></i>
        @foreach($errors->all() as $error)
            {{ $error }}
        @endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">Email Address</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-envelope-fill"></i>
                </span>
                <input type="email"
                       name="email"
                       class="form-control"
                       placeholder="you@example.com"
                       value="{{ old('email') }}"
                       required autofocus>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-lock-fill"></i>
                </span>
                <input type="password"
                       name="password"
                       class="form-control"
                       placeholder="Enter your password"
                       id="passwordInput"
                       required>
                <button type="button"
                        class="input-group-text"
                        onclick="togglePassword()"
                        style="border-left:none; cursor:pointer;">
                    <i class="bi bi-eye-fill" id="eyeIcon"></i>
                </button>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox"
                       name="remember" id="remember"
                       style="background:rgba(255,255,255,0.1);
                              border-color:rgba(255,255,255,0.2);">
                <label class="form-check-label"
                       for="remember"
                       style="color:rgba(255,255,255,0.5);
                              font-size:0.82rem;">
                    Remember me
                </label>
            </div>
            <a href="{{ route('forgot.password') }}"
               style="color:var(--primary-light);
                      font-size:0.82rem;
                      text-decoration:none;
                      font-weight:600;">
                Forgot password?
            </a>
        </div>

        <button type="submit" class="btn-auth">
            <i class="bi bi-box-arrow-in-right me-2"></i>
            Login to Dashboard
        </button>
    </form>

    <div class="divider">or</div>

    <div class="auth-footer">
        Don't have an account?
        <a href="{{ route('register') }}">Create one free</a>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePassword() {
    const input   = document.getElementById('passwordInput');
    const icon    = document.getElementById('eyeIcon');
    const isText  = input.type === 'text';
    input.type    = isText ? 'password' : 'text';
    icon.className = isText ? 'bi bi-eye-fill' : 'bi bi-eye-slash-fill';
}
</script>
@endpush