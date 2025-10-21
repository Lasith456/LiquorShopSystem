@extends('layouts.app')

@section('content')
<style>
    body {
        background: linear-gradient(135deg, #f8f9fa, #e3f2fd);
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .reset-card {
        background: #fff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        padding: 40px 30px;
        width: 100%;
        max-width: 500px;
    }

    .reset-title {
        font-weight: 700;
        font-size: 26px;
        text-align: center;
        color: #0d6efd;
        margin-bottom: 25px;
    }

    .form-control {
        border-radius: 10px;
        padding: 12px 15px;
        font-size: 15px;
    }

    .btn-primary {
        background: #0d6efd;
        border: none;
        border-radius: 10px;
        width: 100%;
        padding: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background: #0b5ed7;
        transform: translateY(-1px);
    }

    .footer-text {
        text-align: center;
        font-size: 14px;
        margin-top: 25px;
        color: #6c757d;
    }

    .footer-text a {
        color: #0d6efd;
        text-decoration: none;
        font-weight: 500;
    }

    .footer-text a:hover {
        text-decoration: underline;
    }

    .invalid-feedback {
        font-size: 13px;
    }
</style>

<div class="reset-card">
    <div class="reset-title">Reset Your Password 🔑</div>

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        {{-- Email --}}
        <div class="mb-3">
            <label for="email" class="form-label fw-semibold">Email Address</label>
            <input id="email" type="email" 
                class="form-control @error('email') is-invalid @enderror" 
                name="email" value="{{ $email ?? old('email') }}" required 
                placeholder="you@example.com" autofocus>

            @error('email')
                <span class="invalid-feedback d-block" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- New Password --}}
        <div class="mb-3">
            <label for="password" class="form-label fw-semibold">New Password</label>
            <input id="password" type="password" 
                class="form-control @error('password') is-invalid @enderror" 
                name="password" required placeholder="Enter new password">

            @error('password')
                <span class="invalid-feedback d-block" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Confirm Password --}}
        <div class="mb-3">
            <label for="password-confirm" class="form-label fw-semibold">Confirm Password</label>
            <input id="password-confirm" type="password" 
                class="form-control" name="password_confirmation" required 
                placeholder="Re-enter new password">
        </div>

        {{-- Reset Button --}}
        <div class="mb-3">
            <button type="submit" class="btn btn-primary">Reset Password</button>
        </div>
    </form>

    <div class="footer-text">
        Remembered your password? <a href="{{ route('login') }}">Login here</a>
    </div>
</div>
@endsection
