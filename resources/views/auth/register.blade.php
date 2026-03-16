@extends('layouts.auth')

@section('title', 'Register')

@section('content')
    <div class="auth-card">
        <h2>Create Account 🏏</h2>
        <p class="subtitle">Register as a tournament organizer</p>

        @if ($errors->any())
            <div class="alert alert-danger mb-3">
                <i class="bi bi-exclamation-circle-fill me-2"></i>
                <ul class="mb-0 mt-1 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
            @csrf

            {{-- Name --}}
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-person-fill"></i>
                    </span>
                    <input type="text" name="name" class="form-control" placeholder="Your full name"
                        value="{{ old('name') }}" required autofocus>
                </div>
            </div>

            {{-- Email --}}
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-envelope-fill"></i>
                    </span>
                    <input type="email" name="email" class="form-control" placeholder="you@example.com"
                        value="{{ old('email') }}" required>
                </div>
            </div>

            {{-- Mobile --}}
            <div class="mb-3">
                <label class="form-label">Mobile Number</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-phone-fill"></i>
                    </span>
                    <input type="text" name="mobile" class="form-control" placeholder="10-digit mobile number"
                        value="{{ old('mobile') }}" required>
                </div>
            </div>

            {{-- Password --}}
            <div class="mb-3">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-lock-fill"></i>
                    </span>
                    <input type="password" name="password" class="form-control" placeholder="Min 6 characters"
                        id="passwordInput" required>
                    <button type="button" class="input-group-text" onclick="togglePassword('passwordInput','eyeIcon1')"
                        style="border-left:none; cursor:pointer;">
                        <i class="bi bi-eye-fill" id="eyeIcon1"></i>
                    </button>
                </div>
            </div>

            {{-- Confirm Password --}}
            <div class="mb-3">
                <label class="form-label">Confirm Password</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-lock-fill"></i>
                    </span>
                    <input type="password" name="password_confirmation" class="form-control"
                        placeholder="Repeat your password" id="confirmInput" required>
                    <button type="button" class="input-group-text" onclick="togglePassword('confirmInput','eyeIcon2')"
                        style="border-left:none; cursor:pointer;">
                        <i class="bi bi-eye-fill" id="eyeIcon2"></i>
                    </button>
                </div>
            </div>

            {{-- Profile Photo --}}
            <div class="mb-4">
                <label class="form-label">Profile Photo
                    <span
                        style="color:rgba(255,255,255,0.3);
                             font-weight:400;
                             text-transform:none;
                             letter-spacing:0;">
                        (optional)
                    </span>
                </label>
                <input type="file" name="profile_photo" class="form-control" accept="image/*" id="photoInput"
                    onchange="previewPhoto(this)">
                <div id="photoPreview"
                    style="display:none;
                        margin-top:10px;
                        text-align:center;">
                    <img id="previewImg" src="" alt="Preview"
                        style="width:64px;
                            height:64px;
                            border-radius:50%;
                            object-fit:cover;
                            border:2px solid rgba(108,63,197,0.5);">
                </div>
            </div>

            <button type="submit" class="btn-auth">
                <i class="bi bi-person-plus-fill me-2"></i>
                Create My Account
            </button>
        </form>

        <div class="divider">or</div>

        <div class="auth-footer">
            Already have an account?
            <a href="{{ route('login') }}">Login here</a>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            const isText = input.type === 'text';
            input.type = isText ? 'password' : 'text';
            icon.className = isText ? 'bi bi-eye-fill' : 'bi bi-eye-slash-fill';
        }

        function previewPhoto(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    document.getElementById('previewImg').src = e.target.result;
                    document.getElementById('photoPreview').style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endpush
