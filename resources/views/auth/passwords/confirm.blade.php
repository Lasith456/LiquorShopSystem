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

    .confirm-card {
        background: #fff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        padding: 40px 30px;
        width: 100%;
        max-width: 480px;
        text-align: center;
    }

    .confirm-title {
        font-weight: 700;
        font-size: 26px;
        color: #0d6efd;
        margin-bottom: 10px;
    }

    .confirm-text {
        font-size: 15px;
        color: #495057;
        margin-bottom: 25px;
        line-height: 1.6;
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

    .btn-link {
        display: inline-block;
        font-size: 14px;
        color: #0d6efd;
        text-decoration: none;
        margin-top: 15px;
    }

    .btn-link:hover {
        text-decoration: underline;
    }

    .invalid-feedback {
        font-size: 13px;
        text-align: left;
    }
</style>

<div class="confirm-card">
    <div class="confirm-title">Confirm Your Password 🔒</div>
    <div class="confirm-text">
        {{ __('Please confirm your password before continuing.') }}
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        {{-- Password Field --}}
        <div class="mb-3 text-start">
            <label for="password" class="form-label fw-semibold">Password</label>
            <input id="password" type="password" 
                class="form-control @error('password') is-invalid @enderror" 
                name="password" required autocomplete="current-password" 
                placeholder="Enter your password">

            @error('password')
                <span class="invalid-feedback d-block" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Confirm Button --}}
        <div class="mb-3">
            <button type="submit" class="btn btn-primary">
                {{ __('Confirm Password') }}
            </button>
        </div>

        {{-- Forgot Password --}}
        @if (Route::has('password.request'))
            <a class="btn-link" href="{{ route('password.request') }}">
                {{ __('Forgot Your Password?') }}
            </a>
        @endif
    </form>
</div>
@endsection
