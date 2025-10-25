@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-6">
    <div class="max-w-7xl mx-auto bg-white shadow-lg rounded-2xl p-6">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-3">
            <h2 class="text-2xl font-semibold text-indigo-700">📦 Stock Entries</h2>

            <a href="{{ route('stocks.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-md shadow transition">
                <i class="fa fa-plus mr-2"></i> Add New Stock
            </a>
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('stocks.index') }}" class="grid sm:grid-cols-3 gap-4 mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">🔍 Search Reference</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search by reference..."
                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">📅 Date Range</label>
                <div class="flex gap-2">
                    <input type="date" name="from" value="{{ request('from') }}"
                           class="w-1/2 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <input type="date" name="to" value="{{ request('to') }}"
                           class="w-1/2 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div class="flex items-end">
                <button type="submit"
                        class="w-full sm:w-auto px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md font-semibold shadow">
                    <i class="fa-solid fa-filter mr-1"></i> Filter
                </button>
                <a href="{{ route('stocks.index') }}"
                   class="ml-2 px-3 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-md text-sm">
                    <i class="fa-solid fa-rotate-right"></i>
                </a>
            </div>
        </form>

        {{-- Success Message --}}
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
                <i class="fa-solid fa-circle-check mr-1"></i> {{ session('success') }}
            </div>
        @endif

        {{-- Stock Table --}}
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
                    @forelse ($stocks as $stock)
                        <tr class="hover:bg-indigo-50 transition">
                            <td class="px-4 py-3 text-sm text-gray-700">{{ ++$i }}</td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $stock->reference_no }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $stock->date->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">Rs. {{ number_format($stock->total_value, 2) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $stock->user->name ?? 'System' }}</td>

                            {{-- Action buttons (icon-only) --}}
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center gap-2">
                                    {{-- View --}}
                                    <a href="{{ route('stocks.show', $stock->id) }}"
                                       class="text-blue-600 hover:text-blue-800 text-lg" title="View Details">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>

                                    {{-- Delete --}}
                                    <form action="{{ route('stocks.destroy', $stock->id) }}" method="POST" onsubmit="return confirm('Delete this stock entry?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-lg" title="Delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-gray-500 py-4 italic">No stock entries found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {!! $stocks->appends(request()->query())->links() !!}
        </div>

        {{-- Footer --}}
        <p class="text-center text-gray-500 text-sm mt-6">
            <small>Powered by <span class="text-indigo-600 font-semibold">NsoftItSolutions</span></small>
        </p>
    </div>
</div>
@endsection
