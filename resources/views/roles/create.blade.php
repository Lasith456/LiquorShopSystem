@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-6 py-8">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
            <i class="fa-solid fa-shield-halved text-blue-600"></i> Create New Role
        </h2>
        <a href="{{ route('roles.index') }}"
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

    {{-- Role Creation Form --}}
    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-8 border border-gray-200 dark:border-gray-700">
        <form method="POST" action="{{ route('roles.store') }}" class="space-y-6">
            @csrf

            {{-- Role Name --}}
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Role Name</label>
                <input type="text" id="name" name="name" placeholder="Enter role name"
                       class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
            </div>

            {{-- Permissions --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-3">Assign Permissions</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach($permission as $value)
                        <label class="flex items-center space-x-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg px-4 py-2 hover:bg-blue-50 dark:hover:bg-gray-800 transition cursor-pointer">
                            <input type="checkbox" name="permission[{{ $value->id }}]" value="{{ $value->id }}" 
                                   class="text-blue-600 focus:ring-blue-500 rounded-md">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ $value->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Submit --}}
            <div class="pt-4">
                <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg shadow-md transition transform hover:-translate-y-0.5">
                    <i class="fa-solid fa-floppy-disk mr-2"></i> Submit
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
