@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center p-6">
    <div class="bg-white/80 backdrop-blur-md shadow-xl rounded-2xl p-8 w-full max-w-md text-center">
        <div class="mb-6">
            <h1 class="text-3xl font-extrabold text-blue-700">Verify Your Email ✉️</h1>
            <p class="text-gray-500 text-sm mt-2">Let’s make sure your account is secure.</p>
        </div>

        {{-- Success Message --}}
        @if (session('resent'))
            <div class="bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded-lg text-sm mb-5">
                {{ __('A new verification link has been sent to your email address.') }}
            </div>
        @endif

        {{-- Verification Instructions --}}
        <p class="text-gray-600 text-sm leading-relaxed mb-6">
            {{ __('Before continuing, please check your email for a verification link.') }}<br>
            {{ __('If you did not receive the email, you can request another one below.') }}
        </p>

        {{-- Resend Button --}}
        <form method="POST" action="{{ route('verification.resend') }}" class="mb-6">
            @csrf
            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg shadow-md transition transform hover:-translate-y-0.5">
                {{ __('Resend Verification Email') }}
            </button>
        </form>

        {{-- Logout Option --}}
        <p class="text-gray-500 text-sm">
            <a href="{{ route('logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
               class="text-blue-600 font-medium hover:underline">
               {{ __('Logout') }}
            </a>
        </p>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>
    </div>
</div>
@endsection
