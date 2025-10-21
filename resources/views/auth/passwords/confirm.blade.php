@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center p-6">
    <div class="bg-white/80 backdrop-blur-md shadow-xl rounded-2xl p-8 w-full max-w-md text-center">
        {{-- Title --}}
        <div class="mb-6">
            <h1 class="text-3xl font-extrabold text-blue-700">Confirm Password 🔒</h1>
            <p class="text-gray-500 text-sm mt-2">Please confirm your password before continuing.</p>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
            @csrf

            {{-- Password Field --}}
            <div class="text-left">
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-gray-800 placeholder-gray-400"
                    placeholder="Enter your password">
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Confirm Button --}}
            <div>
                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg shadow-md transition transform hover:-translate-y-0.5">
                    {{ __('Confirm Password') }}
                </button>
            </div>

            {{-- Forgot Password --}}
            @if (Route::has('password.request'))
                <div class="mt-3">
                    <a href="{{ route('password.request') }}"
                       class="text-sm text-blue-600 hover:underline font-medium">
                       {{ __('Forgot Your Password?') }}
                    </a>
                </div>
            @endif
        </form>
    </div>
</div>
@endsection
