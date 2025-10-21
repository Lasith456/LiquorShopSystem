@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center p-6">
    <div class="bg-white/80 backdrop-blur-md shadow-xl rounded-2xl p-8 w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold text-blue-700">Welcome Back 👋</h1>
            <p class="text-gray-500 text-sm mt-2">Login to your NSoft account</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            {{-- Email Field --}}
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-gray-800 placeholder-gray-400"
                    placeholder="you@example.com">
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password Field --}}
            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                <input id="password" type="password" name="password" required
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-gray-800 placeholder-gray-400"
                    placeholder="Enter your password">
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Remember Me --}}
            <div class="flex items-center justify-between">
                <label class="flex items-center text-sm text-gray-600">
                    <input type="checkbox" name="remember" id="remember"
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        {{ old('remember') ? 'checked' : '' }}>
                    <span class="ml-2">Remember Me</span>
                </label>
                
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:underline">
                        Forgot Password?
                    </a>
                @endif
            </div>

            {{-- Submit Button --}}
            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg shadow-md transition transform hover:-translate-y-0.5">
                Login
            </button>
        </form>

        {{-- Divider --}}
        <div class="mt-6 flex items-center justify-center">
            <div class="border-t border-gray-300 w-1/3"></div>
            <p class="text-gray-500 text-sm mx-3">or</p>
            <div class="border-t border-gray-300 w-1/3"></div>
        </div>

        {{-- Register Redirect --}}
        <p class="mt-6 text-center text-gray-600 text-sm">
            Don’t have an account? 
            <a href="{{ route('register') }}" class="text-blue-600 font-semibold hover:underline">Sign up here</a>
        </p>
    </div>
</div>
@endsection
