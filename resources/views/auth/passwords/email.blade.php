@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center p-6">
    <div class="bg-white/80 backdrop-blur-md shadow-xl rounded-2xl p-8 w-full max-w-md">
        {{-- Title --}}
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold text-blue-700">Forgot Password? 🔐</h1>
            <p class="text-gray-500 text-sm mt-2">No worries — we’ll send you a link to reset it.</p>
        </div>

        {{-- Success Message --}}
        @if (session('status'))
            <div class="bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded-lg text-sm mb-5">
                {{ session('status') }}
            </div>
        @endif

        {{-- Form --}}
        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf

            {{-- Email Address --}}
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-gray-800 placeholder-gray-400"
                    placeholder="you@example.com">

                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Send Button --}}
            <div>
                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg shadow-md transition transform hover:-translate-y-0.5">
                    {{ __('Send Password Reset Link') }}
                </button>
            </div>
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
@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center p-6">
    <div class="bg-white/80 backdrop-blur-md shadow-xl rounded-2xl p-8 w-full max-w-md">
        {{-- Title --}}
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold text-blue-700">Forgot Password? 🔐</h1>
            <p class="text-gray-500 text-sm mt-2">No worries — we’ll send you a link to reset it.</p>
        </div>

        {{-- Success Message --}}
        @if (session('status'))
            <div class="bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded-lg text-sm mb-5">
                {{ session('status') }}
            </div>
        @endif

        {{-- Form --}}
        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf

            {{-- Email Address --}}
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-gray-800 placeholder-gray-400"
                    placeholder="you@example.com">

                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Send Button --}}
            <div>
                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg shadow-md transition transform hover:-translate-y-0.5">
                    {{ __('Send Password Reset Link') }}
                </button>
            </div>
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
