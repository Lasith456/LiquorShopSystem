@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-6 py-8">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
            <i class="fa-solid fa-shield-halved text-blue-600"></i> Role Management
        </h2>
        @can('role-create')
        <a href="{{ route('roles.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow transition transform hover:-translate-y-0.5">
           <i class="fa fa-plus"></i> Create New Role
        </a>
        @endcan
    </div>

    {{-- Success Message --}}
    @session('success')
        <div class="mb-6 bg-green-50 border border-green-300 text-green-700 px-4 py-3 rounded-lg text-sm shadow-sm">
            <i class="fa-solid fa-circle-check text-green-600 mr-1"></i> {{ $value }}
        </div>
    @endsession

    {{-- Roles Table --}}
    <div class="overflow-x-auto bg-white dark:bg-gray-800 shadow-lg rounded-2xl border border-gray-200 dark:border-gray-700">
        <table class="min-w-full text-sm text-left text-gray-600 dark:text-gray-300">
            <thead class="bg-gray-100 dark:bg-gray-900 text-gray-700 dark:text-gray-100 uppercase text-xs">
                <tr>
                    <th class="px-6 py-3 w-20 text-center">No</th>
                    <th class="px-6 py-3">Role Name</th>
                    <th class="px-6 py-3 text-center w-70">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach ($roles as $key => $role)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    <td class="px-6 py-3 text-center font-medium text-gray-800 dark:text-gray-100">{{ ++$i }}</td>
                    <td class="px-6 py-3 font-medium">{{ $role->name }}</td>
                    <td class="px-6 py-3 text-center flex justify-center gap-2 flex-wrap">
                        {{-- Show --}}
                        <a href="{{ route('roles.show', $role->id) }}"
                           class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-md font-medium text-xs transition">
                            <i class="fa-solid fa-list"></i> Show
                        </a>

                        {{-- Edit --}}
                        @can('role-edit')
                        <a href="{{ route('roles.edit', $role->id) }}"
                           class="inline-flex items-center gap-1 px-3 py-1.5 bg-yellow-100 hover:bg-yellow-200 text-yellow-700 rounded-md font-medium text-xs transition">
                            <i class="fa-solid fa-pen-to-square"></i> Edit
                        </a>
                        @endcan

                        {{-- Delete --}}
                        @can('role-delete')
                        <form method="POST" action="{{ route('roles.destroy', $role->id) }}" onsubmit="return confirm('Are you sure you want to delete this role?')" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 rounded-md font-medium text-xs transition">
                                <i class="fa-solid fa-trash"></i> Delete
                            </button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {!! $roles->links('pagination::bootstrap-5') !!}
    </div>

    {{-- Footer --}}
    <p class="text-center text-gray-500 dark:text-gray-400 text-sm mt-6">
        Powered By <span class="font-semibold text-blue-600">NsoftItSolutions</span>
    </p>
</div>
@endsection
