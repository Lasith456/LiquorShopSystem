@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-10 px-4">
    <div class="max-w-4xl mx-auto bg-white shadow-lg rounded-2xl p-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-indigo-700 mb-4 sm:mb-0">📏 Sizes</h2>

            @can('size-create')
                <a href="{{ route('sizes.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow transition">
                    <i class="fa fa-plus mr-2"></i> Create New Size
                </a>
            @endcan
        </div>

        {{-- Success Message --}}
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
                {{ session('success') }}
            </div>
        @endif

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
                <thead class="bg-indigo-600 text-white">
                    <tr>
                        <th class="px-4 py-2 text-left text-sm font-semibold">#</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold">Label</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold">Description</th>
                        <th class="px-4 py-2 text-center text-sm font-semibold">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @foreach ($sizes as $size)
                        <tr class="hover:bg-indigo-50 transition">
                            <td class="px-4 py-3 text-sm text-gray-700">{{ ++$i }}</td>
                            <td class="px-4 py-3 text-sm text-gray-800 font-medium">{{ $size->label }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ Str::limit($size->description, 60) }}</td>
                            <td class="px-4 py-3 text-center space-x-2">
                                <a href="{{ route('sizes.show', $size->id) }}" 
                                   class="inline-flex items-center px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold rounded-md shadow">
                                    <i class="fa-solid fa-list mr-1"></i> Show
                                </a>

                                @can('size-edit')
                                    <a href="{{ route('sizes.edit', $size->id) }}" 
                                       class="inline-flex items-center px-3 py-1.5 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-semibold rounded-md shadow">
                                        <i class="fa-solid fa-pen-to-square mr-1"></i> Edit
                                    </a>
                                @endcan

                                @can('size-delete')
                                    <form action="{{ route('sizes.destroy', $size->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Are you sure you want to delete this size?')"
                                            class="inline-flex items-center px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-semibold rounded-md shadow">
                                            <i class="fa-solid fa-trash mr-1"></i> Delete
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {!! $sizes->links() !!}
        </div>

        <p class="text-center text-gray-500 text-sm mt-6">
            <small>Powered by <span class="text-indigo-600 font-semibold">NsoftItSolutions</span></small>
        </p>
    </div>
</div>
@endsection
