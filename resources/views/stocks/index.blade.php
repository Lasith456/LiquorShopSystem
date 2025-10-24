@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-6">
    <div class="max-w-6xl mx-auto bg-white shadow-lg rounded-2xl p-6">

        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-indigo-700">📦 Stock Entries</h2>
            <a href="{{ route('stocks.create') }}"
               class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-md shadow">
                <i class="fa fa-plus mr-1"></i> Add New Stock
            </a>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
                <thead class="bg-indigo-600 text-white">
                    <tr>
                        <th class="px-4 py-2 text-left text-sm font-semibold">#</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold">Reference</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold">Date</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold">Total Value (LKR)</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold">Added By</th>
                        <th class="px-4 py-2 text-center text-sm font-semibold">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @foreach ($stocks as $stock)
                        <tr class="hover:bg-indigo-50 transition">
                            <td class="px-4 py-3 text-sm text-gray-700">{{ ++$i }}</td>
                            <td class="px-4 py-3 text-sm text-gray-800 font-medium">{{ $stock->reference_no }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $stock->date->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">Rs. {{ number_format($stock->total_value, 2) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $stock->user->name ?? 'System' }}</td>
                            <td class="px-4 py-3 text-center space-x-2">
                                <a href="{{ route('stocks.show', $stock->id) }}"
                                   class="inline-flex items-center px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold rounded-md">
                                   <i class="fa-solid fa-eye mr-1"></i> View
                                </a>

                                <form action="{{ route('stocks.destroy', $stock->id) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" onclick="return confirm('Delete this stock entry?')"
                                            class="inline-flex items-center px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-semibold rounded-md">
                                        <i class="fa-solid fa-trash mr-1"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {!! $stocks->links() !!}
        </div>
    </div>
</div>
@endsection
