@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-6 py-8">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
            <i class="fa-solid fa-user-pen text-blue-600"></i> Edit User
        </h2>
        <a href="{{ route('users.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-semibold rounded-lg shadow transition transform hover:-translate-y-0.5">
           <i class="fa fa-arrow-left"></i> Back
        </a>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-lg text-sm">
            <strong class="font-semibold">Whoops!</strong> There were some problems with your input:
            <ul class="list-disc pl-5 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Edit Form Card --}}
    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-8 border border-gray-200 dark:border-gray-700">
        <form method="POST" action="{{ route('users.update', $user->id) }}" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Name --}}
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Name</label>
                <input type="text" id="name" name="name" value="{{ $user->name }}" placeholder="Enter name"
                       class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Email</label>
                <input type="email" id="email" name="email" value="{{ $user->email }}" placeholder="Enter email"
                       class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Password</label>
                <input type="password" id="password" name="password" placeholder="Leave blank to keep current password"
                       class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                <p class="text-xs text-gray-500 mt-1">Leave empty if you don’t want to change the password.</p>
            </div>

            {{-- Confirm Password --}}
            <div>
                <label for="confirm-password" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Confirm Password</label>
                <input type="password" id="confirm-password" name="confirm-password" placeholder="Re-enter new password"
                       class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
            </div>

            {{-- Roles --}}
            <div>
                <label for="roles" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Assign Roles</label>
                <select id="roles" name="roles[]" multiple
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    @foreach ($roles as $value => $label)
                        <option value="{{ $value }}" {{ isset($userRole[$value]) ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Hold <kbd>Ctrl</kbd> or <kbd>Cmd</kbd> to select multiple roles.</p>
            </div>

            {{-- Submit --}}
            <div class="pt-4">
                <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg shadow-md transition transform hover:-translate-y-0.5">
                    <i class="fa-solid fa-floppy-disk mr-2"></i> Update User
                </button>
            </div>
        </form>
    </div>

    {{-- Footer --}}
    <p class="text-center text-gray-500 dark:text-gray-400 text-sm mt-6">
        Powered By <span class="font-semibold text-blue-600">NsoftItSolutions</span>
    </p>
</div>
@endsection
