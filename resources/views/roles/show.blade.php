@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-6 py-8">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
            <i class="fa-solid fa-shield-halved text-blue-600"></i> Role Details
        </h2>
        <a href="{{ route('roles.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-semibold rounded-lg shadow transition transform hover:-translate-y-0.5">
           <i class="fa fa-arrow-left"></i> Back
        </a>
    </div>

    {{-- Role Info Card --}}
    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-8 border border-gray-200 dark:border-gray-700">
        <div class="space-y-6">
            
            {{-- Role Name --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-300 uppercase mb-1">Role Name</h3>
                <p class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $role->name }}</p>
            </div>

            {{-- Permissions --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-300 uppercase mb-2">Assigned Permissions</h3>
                @if(!empty($rolePermissions))
                    <div class="flex flex-wrap gap-2">
                        @foreach($rolePermissions as $v)
                            <span class="px-3 py-1 text-sm bg-green-100 text-green-700 border border-green-300 rounded-md font-medium">
                                {{ $v->name }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 dark:text-gray-400 text-sm">No permissions assigned to this role.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <p class="text-center text-gray-500 dark:text-gray-400 text-sm mt-6">
        Powered By <span class="font-semibold text-blue-600">NsoftItSolutions</span>
    </p>
</div>
@endsection
