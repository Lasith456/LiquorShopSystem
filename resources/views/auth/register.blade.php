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

    .register-card {
        background: #fff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        padding: 40px 30px;
        width: 100%;
        max-width: 600px;
    }

    .register-title {
        font-weight: 700;
        font-size: 28px;
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
</style>

<div class="register-card">
    <div class="register-title">Create Account ✨</div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- Name --}}
        <div class="mb-3">
            <label for="name" class="form-label fw-semibold">Full Name</label>
            <input id="name" type="text" 
                class="form-control @error('name') is-invalid @enderror" 
                name="name" value="{{ old('name') }}" required autofocus 
                placeholder="Enter your name">

            @error('name')
                <span class="invalid-feedback d-block" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Email --}}
        <div class="mb-3">
            <label for="email" class="form-label fw-semibold">Email address</label>
            <input id="email" type="email" 
                class="form-control @error('email') is-invalid @enderror" 
                name="email" value="{{ old('email') }}" required 
                placeholder="you@example.com">

            @error('email')
                <span class="invalid-feedback d-block" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Password --}}
        <div class="mb-3">
            <label for="password" class="form-label fw-semibold">Password</label>
            <input id="password" type="password" 
                class="form-control @error('password') is-invalid @enderror" 
                name="password" required placeholder="Create a password">

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
                class="form-control" name="password_confirmation" 
                required placeholder="Re-enter your password">
        </div>

        {{-- Register Button --}}
        <div class="mb-3">
            <button type="submit" class="btn btn-primary">Register</button>
        </div>
    </form>

    {{-- Already have account --}}
    <div class="footer-text">
        Already have an account? 
        <a href="{{ route('login') }}">Login here</a>
    </div>
</div>
@endsection
