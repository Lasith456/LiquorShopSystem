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

    .verify-card {
        background: #fff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        padding: 40px 30px;
        width: 100%;
        max-width: 480px;
        text-align: center;
    }

    .verify-title {
        font-weight: 700;
        font-size: 26px;
        color: #0d6efd;
        margin-bottom: 15px;
    }

    .verify-text {
        font-size: 15px;
        color: #495057;
        margin-bottom: 25px;
        line-height: 1.6;
    }

    .btn-primary {
        background: #0d6efd;
        border: none;
        border-radius: 10px;
        padding: 12px 30px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background: #0b5ed7;
        transform: translateY(-1px);
    }

    .alert {
        border-radius: 10px;
        font-size: 14px;
        margin-bottom: 20px;
    }

    .footer-note {
        margin-top: 20px;
        color: #6c757d;
        font-size: 14px;
    }

    .footer-note a {
        color: #0d6efd;
        text-decoration: none;
        font-weight: 500;
    }

    .footer-note a:hover {
        text-decoration: underline;
    }
</style>

<div class="verify-card">
    <div class="verify-title">Verify Your Email ✉️</div>

    @if (session('resent'))
        <div class="alert alert-success" role="alert">
            {{ __('A new verification link has been sent to your email address.') }}
        </div>
    @endif

    <div class="verify-text">
        {{ __('Before continuing, please check your email for a verification link.') }}<br>
        {{ __('If you did not receive the email, you can request another below.') }}
    </div>

    <form method="POST" action="{{ route('verification.resend') }}">
        @csrf
        <button type="submit" class="btn btn-primary">
            {{ __('Resend Verification Email') }}
        </button>
    </form>

    <div class="footer-note mt-4">
        <a href="{{ route('logout') }}"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            {{ __('Logout') }}
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
    </div>
</div>
@endsection
