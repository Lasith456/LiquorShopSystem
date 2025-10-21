@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
            <i class="fa-solid fa-users-gear text-blue-600"></i> Users Management
        </h2>
        <a href="{{ route('users.create') }}"
           class="mt-3 sm:mt-0 inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-md transition transform hover:-translate-y-0.5">
            <i class="fa fa-plus"></i> Create New User
        </a>
    </div>

    {{-- Success Message --}}
    @if (session('success'))
        <div class="mb-5 p-4 rounded-lg bg-green-100 border border-green-300 text-green-800 text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Table Container --}}
    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700">
        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse">
                <thead class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-200 text-sm uppercase">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold">No</th>
                        <th class="px-6 py-3 text-left font-semibold">Name</th>
                        <th class="px-6 py-3 text-left font-semibold">Email</th>
                        <th class="px-6 py-3 text-left font-semibold">Roles</th>
                        <th class="px-6 py-3 text-center font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-sm text-gray-700 dark:text-gray-300">
                    @foreach ($data as $key => $user)
                        <tr class="hover:bg-blue-50 dark:hover:bg-gray-700 transition">
                            <td class="px-6 py-3">{{ ++$i }}</td>
                            <td class="px-6 py-3 font-semibold">{{ $user->name }}</td>
                            <td class="px-6 py-3">{{ $user->email }}</td>
                            <td class="px-6 py-3">
                                @if(!empty($user->getRoleNames()))
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($user->getRoleNames() as $v)
                                            <span class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-1 rounded-md border border-green-300">{{ $v }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-center space-x-1">
                                {{-- Show --}}
                                <a href="{{ route('users.show', $user->id) }}"
                                   class="inline-flex items-center gap-1 bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold px-3 py-1.5 rounded-md transition">
                                   <i class="fa-solid fa-list"></i> Show
                                </a>
                                {{-- Edit --}}
                                <a href="{{ route('users.edit', $user->id) }}"
                                   class="inline-flex items-center gap-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-semibold px-3 py-1.5 rounded-md transition">
                                   <i class="fa-solid fa-pen-to-square"></i> Edit
                                </a>
                                {{-- Delete --}}
                                <form method="POST" action="{{ route('users.destroy', $user->id) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            onclick="return confirm('Are you sure you want to delete this user?')"
                                            class="inline-flex items-center gap-1 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold px-3 py-1.5 rounded-md transition">
                                        <i class="fa-solid fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach

                    @if ($data->isEmpty())
                        <tr>
                            <td colspan="5" class="text-center py-6 text-gray-500 dark:text-gray-400">
                                No users found.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-5">
        {!! $data->links('pagination::bootstrap-5') !!}
    </div>

    {{-- Footer --}}
    <p class="text-center text-gray-500 dark:text-gray-400 text-sm mt-6">
        Powered By <span class="font-semibold text-blue-600">NsoftItSolutions</span>
    </p>
</div>
@endsection
