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

    .login-card {
        background: #fff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        padding: 40px 30px;
        width: 100%;
        max-width: 600px;
    }

    .login-title {
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

    .form-check-label {
        font-size: 14px;
    }

    .forgot-password {
        display: block;
        text-align: right;
        font-size: 14px;
        margin-top: 10px;
        color: #0d6efd;
        text-decoration: none;
    }

    .forgot-password:hover {
        text-decoration: underline;
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

<div class="login-card">
    <div class="login-title">Welcome Back 👋</div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Email Field --}}
        <div class="mb-3">
            <label for="email" class="form-label fw-semibold">Email address</label>
            <input id="email" type="email" 
                class="form-control @error('email') is-invalid @enderror" 
                name="email" value="{{ old('email') }}" required autofocus 
                placeholder="you@example.com">

            @error('email')
                <span class="invalid-feedback d-block" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Password Field --}}
        <div class="mb-3">
            <label for="password" class="form-label fw-semibold">Password</label>
            <input id="password" type="password" 
                class="form-control @error('password') is-invalid @enderror" 
                name="password" required placeholder="Enter your password">

            @error('password')
                <span class="invalid-feedback d-block" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Remember Me --}}
        <div class="mb-3 form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="remember" 
                {{ old('remember') ? 'checked' : '' }}>
            <label class="form-check-label" for="remember">
                Remember Me
            </label>
        </div>

        {{-- Login Button --}}
        <div class="mb-3">
            <button type="submit" class="btn btn-primary">Login</button>
        </div>

        {{-- Forgot Password --}}
        @if (Route::has('password.request'))
            <a class="forgot-password" href="{{ route('password.request') }}">
                Forgot your password?
            </a>
        @endif
    </form>

    {{-- Register Redirect --}}
    <div class="footer-text">
        Don’t have an account? 
        <a href="{{ route('register') }}">Sign up here</a>
    </div>
</div>
@endsection
