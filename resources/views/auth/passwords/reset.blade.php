@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center p-6">
    <div class="bg-white/80 backdrop-blur-md shadow-xl rounded-2xl p-8 w-full max-w-md">
        {{-- Title --}}
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold text-blue-700">Reset Your Password 🔑</h1>
            <p class="text-gray-500 text-sm mt-2">Enter your new password below to regain access.</p>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email Address</label>
                <input id="email" type="email" name="email" value="{{ $email ?? old('email') }}" required
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-gray-800 placeholder-gray-400"
                    placeholder="you@example.com" autofocus>
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- New Password --}}
            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">New Password</label>
                <input id="password" type="password" name="password" required
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-gray-800 placeholder-gray-400"
                    placeholder="Enter new password">
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Confirm Password --}}
            <div>
                <label for="password-confirm" class="block text-sm font-semibold text-gray-700 mb-1">Confirm Password</label>
                <input id="password-confirm" type="password" name="password_confirmation" required
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-gray-800 placeholder-gray-400"
                    placeholder="Re-enter new password">
            </div>

            {{-- Reset Button --}}
            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg shadow-md transition transform hover:-translate-y-0.5">
                {{ __('Reset Password') }}
            </button>
        </form>

        {{-- Back to Login --}}
        <p class="mt-6 text-center text-gray-600 text-sm">
            Remembered your password?
            <a href="{{ route('login') }}" class="text-blue-600 font-semibold hover:underline">
                Login here
            </a>
        </p>
    </div>
</div>
@endsection
