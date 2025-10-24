@extends('layouts.app')

@section('content')
<div class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen p-6">
    <div class="max-w-7xl mx-auto bg-white rounded-2xl shadow-md p-6">
        <div class="flex justify-between items-center border-b pb-4 mb-6">
            <h2 class="text-2xl font-semibold text-indigo-700">🍾 Empty Bottle Return Report</h2>
            <div class="space-x-2">
                <a href="{{ route('reports.bottles.export.pdf', request()->query()) }}" class="px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                    <i class="fa fa-file-pdf mr-1"></i> Export PDF
                </a>
                <a href="{{ route('reports.bottles.export.excel', request()->query()) }}" class="px-3 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    <i class="fa fa-file-excel mr-1"></i> Export Excel
                </a>
            </div>
        </div>

        {{-- Filters --}}
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div>
                <label class="block text-sm text-gray-600 mb-1">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-400">
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-400">
            </div>
            <div class="md:col-span-2 flex items-end justify-end gap-3">
                <button class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    <i class="fa fa-filter mr-1"></i> Apply Filter
                </button>
                <a href="{{ route('reports.bottles') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                    Clear
                </a>
            </div>
        </form>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 text-sm">
                <thead class="bg-indigo-600 text-white">
                    <tr>
                        <th class="px-3 py-2 text-left">Date</th>
                        <th class="px-3 py-2 text-center">Quantity</th>
                        <th class="px-3 py-2 text-right">Price per Bottle (LKR)</th>
                        <th class="px-3 py-2 text-right">Total (LKR)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bottles as $bottle)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-3 py-2">{{ $bottle->date }}</td>
                            <td class="px-3 py-2 text-center">{{ $bottle->quantity }}</td>
                            <td class="px-3 py-2 text-right">Rs. {{ number_format($bottle->price_per_bottle, 2) }}</td>
                            <td class="px-3 py-2 text-right">Rs. {{ number_format($bottle->total_price, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center py-3 text-gray-500">No records found for the selected period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Total Summary --}}
        <div class="text-right mt-4 font-semibold text-gray-700">
            💰 Total Bottle Value: Rs. {{ number_format($totalValue, 2) }}
        </div>
    </div>
</div>
@endsection
