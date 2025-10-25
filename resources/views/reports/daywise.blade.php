@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6" x-data>
    {{-- 🌈 Page Header --}}
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-indigo-700 flex items-center gap-2">
            📊 <span>Day-wise Sales Summary (with Sizes & Quantity)</span>
        </h2>
        <div class="flex gap-2">
            <a href="{{ route('reports.daywise.export.pdf', request()->all()) }}" 
               class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg shadow-md flex items-center gap-2 transition">
                <i class="fa-solid fa-file-pdf"></i> PDF
            </a>
            <a href="{{ route('reports.daywise.export.excel', request()->all()) }}" 
               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow-md flex items-center gap-2 transition">
                <i class="fa-solid fa-file-excel"></i> Excel
            </a>
        </div>
    </div>

    {{-- 🔍 Filters --}}
    <form method="GET" action="{{ route('reports.daywise') }}" 
          class="bg-white shadow-sm border border-gray-200 rounded-lg p-4 mb-6 flex flex-wrap gap-3 items-end">
        <div>
            <label class="text-sm text-gray-600 mb-1 block">Start Date</label>
            <input type="date" name="start_date" value="{{ request('start_date') }}" 
                   class="border border-gray-300 focus:ring-2 focus:ring-indigo-400 p-2 rounded-lg">
        </div>

        <div>
            <label class="text-sm text-gray-600 mb-1 block">End Date</label>
            <input type="date" name="end_date" value="{{ request('end_date') }}" 
                   class="border border-gray-300 focus:ring-2 focus:ring-indigo-400 p-2 rounded-lg">
        </div>

        <div>
            <label class="text-sm text-gray-600 mb-1 block">Quick Filter</label>
            <select name="quick_filter" 
                    class="border border-gray-300 focus:ring-2 focus:ring-indigo-400 p-2 rounded-lg">
                <option value="">-- Select --</option>
                <option value="today" {{ request('quick_filter')=='today'?'selected':'' }}>Today</option>
                <option value="week" {{ request('quick_filter')=='week'?'selected':'' }}>This Week</option>
                <option value="month" {{ request('quick_filter')=='month'?'selected':'' }}>This Month</option>
            </select>
        </div>

        <div class="flex gap-3 mt-5">
            <button type="submit" 
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg shadow-md flex items-center gap-2 transition">
                <i class="fa-solid fa-filter"></i> Filter
            </button>

            @if(request()->has('start_date') || request()->has('end_date') || request()->has('quick_filter'))
            <a href="{{ route('reports.daywise') }}" 
               class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-5 py-2 rounded-lg shadow-md flex items-center gap-2 transition">
                <i class="fa-solid fa-rotate-left"></i> Clear
            </a>
            @endif
        </div>
    </form>

    {{-- 📋 Table --}}
    <div class="overflow-x-auto rounded-lg shadow-lg border border-gray-200 bg-white backdrop-blur">
        <table class="w-full border-collapse text-sm">
            <thead>
                <tr class="bg-gradient-to-r from-indigo-600 to-indigo-400 text-white text-left">
                    <th class="py-3 px-4 rounded-tl-lg">Date</th>
                    <th> </th>
                    <th class="py-3 px-4 text-right">Total Sales (Rs)</th>
                    <th class="py-3 px-4 text-right">Total Cost (Rs)</th>
                    <th class="py-3 px-4 text-right rounded-tr-lg">Profit (Rs)</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($dayWise as $day)
                    {{-- Day Summary Row --}}
                    <tr class="bg-indigo-50 font-semibold border-t border-gray-300">
                        <td class="px-4 py-3 text-indigo-800">{{ $day->day }}</td>
                        <td> </td>
                        <td class="px-4 py-3 text-right">{{ number_format($day->total_sales, 2) }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($day->total_cost, 2) }}</td>
                        <td class="px-4 py-3 text-right text-green-700">{{ number_format($day->profit, 2) }}</td>
                    </tr>

                    {{-- Detailed Product Rows --}}
                    <tr class="bg-gray-100 font-medium">
                        <th class="px-6 py-2 text-gray-700">Product (Size)</th>
                        <th class="text-right px-4 py-2 text-gray-700">Qty</th>
                        <th class="text-right px-4 py-2 text-gray-700">Selling Price</th>
                        <th class="text-right px-4 py-2 text-gray-700">Cost Price</th>
                        <th class="text-right px-4 py-2 text-gray-700">Profit</th>
                    </tr>

                    @foreach ($day->items as $item)
                        <tr class="hover:bg-gray-50 border-b border-gray-200 transition">
                            <td class="pl-10 py-2 text-gray-700">
                                — {{ $item->product->name }}
                                <span class="text-xs text-gray-500">({{ $item->size->label ?? '-' }})</span>
                            </td>
                            <td class="text-right px-4 py-2 text-gray-700 font-medium">{{ $item->qty }}</td>
                            <td class="text-right px-4 py-2 text-gray-600">
                                Rs. {{ number_format($item->selling_price, 2) }}
                            </td>
                            <td class="text-right px-4 py-2 text-gray-600">
                                Rs. {{ number_format($item->latest_cost_price ?? 0, 2) }}
                            </td>
                            <td class="text-right px-4 py-2 font-semibold 
                                {{ ($item->profit ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                Rs. {{ number_format($item->profit ?? 0, 2) }}
                            </td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-6 text-gray-500">No records found for the selected filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
