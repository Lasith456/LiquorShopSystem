@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    {{-- 🌙 Header --}}
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-indigo-700 flex items-center gap-2">
            📆 Monthly Sales, Cost & Profit Report
        </h2>
        <div class="flex gap-2">
            <a href="{{ route('reports.monthly.export.pdf', request()->all()) }}" 
               class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg shadow-md flex items-center gap-2 transition">
                <i class="fa-solid fa-file-pdf"></i> PDF
            </a>
            <a href="{{ route('reports.monthly.export.excel', request()->all()) }}" 
               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow-md flex items-center gap-2 transition">
                <i class="fa-solid fa-file-excel"></i> Excel
            </a>
        </div>
    </div>

{{-- 🔍 Filters --}}
<form method="GET" action="{{ route('reports.monthly') }}" 
      class="bg-white shadow-sm border border-gray-200 rounded-lg p-4 mb-6 flex flex-wrap gap-3 items-end">

    {{-- 🗓️ Start Month --}}
    <div class="flex flex-col">
        <label class="text-sm text-gray-600 mb-1">Start Month</label>
        <select name="start_date" 
                class="border border-gray-300 focus:ring-2 focus:ring-indigo-400 p-2 rounded-lg w-52 text-gray-700 bg-white cursor-pointer">
            <option value="">-- Select Start Month --</option>
            @foreach (range(0, 35) as $i)
                @php
                    $monthValue = \Carbon\Carbon::now()->subMonths($i)->format('Y-m');
                    $monthName = \Carbon\Carbon::now()->subMonths($i)->format('F Y');
                @endphp
                <option value="{{ $monthValue }}" {{ request('start_date') == $monthValue ? 'selected' : '' }}>
                    {{ $monthName }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- 🗓️ End Month --}}
    <div class="flex flex-col">
        <label class="text-sm text-gray-600 mb-1">End Month</label>
        <select name="end_date" 
                class="border border-gray-300 focus:ring-2 focus:ring-indigo-400 p-2 rounded-lg w-52 text-gray-700 bg-white cursor-pointer">
            <option value="">-- Select End Month --</option>
            @foreach (range(0, 35) as $i)
                @php
                    $monthValue = \Carbon\Carbon::now()->subMonths($i)->format('Y-m');
                    $monthName = \Carbon\Carbon::now()->subMonths($i)->format('F Y');
                @endphp
                <option value="{{ $monthValue }}" {{ request('end_date') == $monthValue ? 'selected' : '' }}>
                    {{ $monthName }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Buttons --}}
    <div class="flex gap-3 mt-5">
        <button type="submit" 
            class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg shadow-md flex items-center gap-2 transition">
            <i class="fa-solid fa-filter"></i> Filter
        </button>

        @if(request()->has('start_date') || request()->has('end_date'))
        <a href="{{ route('reports.monthly') }}" 
           class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-5 py-2 rounded-lg shadow-md flex items-center gap-2 transition">
            <i class="fa-solid fa-rotate-left"></i> Clear
        </a>
        @endif
    </div>
</form>


    {{-- 📊 Monthly Summary --}}
    <div class="space-y-8">
        @forelse ($monthly as $month)
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                {{-- 🗓️ Month Header --}}
                <div class="bg-indigo-600 text-white px-6 py-3 flex justify-between items-center">
                    <h3 class="text-lg font-semibold">
                        {{ \Carbon\Carbon::parse($month->month.'-01')->format('F Y') }}
                    </h3>
                    <p class="font-medium">
                        Total Profit: 
                        <span class="text-green-300 font-semibold">Rs. {{ number_format($month->profit, 2) }}</span>
                    </p>
                </div>

                {{-- 📦 Product Breakdown --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="px-4 py-2 text-left">Product (Size)</th>
                                <th class="px-4 py-2 text-center">Qty</th>
                                <th class="px-4 py-2 text-right">Selling Price (Rs)</th>
                                <th class="px-4 py-2 text-right">Cost Price (Rs)</th>
                                <th class="px-4 py-2 text-right">Total Sales (Rs)</th>
                                <th class="px-4 py-2 text-right">Total Cost (Rs)</th>
                                <th class="px-4 py-2 text-right">Profit (Rs)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($month->items as $item)
                                @php
                                    $totalSales = $item->qty * $item->selling_price;
                                    $totalCost = $item->qty * ($item->latest_cost_price ?? 0);
                                    $profit = $totalSales - $totalCost;
                                @endphp
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-2 font-medium text-gray-800">
                                        {{ $item->product->name ?? '-' }}
                                        <span class="text-gray-500 text-xs">({{ $item->size->label ?? '-' }})</span>
                                    </td>
                                    <td class="px-4 py-2 text-center">{{ $item->qty }}</td>
                                    <td class="px-4 py-2 text-right">{{ number_format($item->selling_price, 2) }}</td>
                                    <td class="px-4 py-2 text-right">{{ number_format($item->latest_cost_price ?? 0, 2) }}</td>
                                    <td class="px-4 py-2 text-right">{{ number_format($totalSales, 2) }}</td>
                                    <td class="px-4 py-2 text-right">{{ number_format($totalCost, 2) }}</td>
                                    <td class="px-4 py-2 text-right font-semibold {{ $profit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ number_format($profit, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        {{-- 🧾 Monthly Totals --}}
                        <tfoot class="bg-indigo-50 font-semibold text-gray-800">
                            <tr>
                                <td class="px-4 py-2 text-right">Monthly Total</td>
                                <td class="px-4 py-2 text-center">—</td>
                                <td class="px-4 py-2 text-right">—</td>
                                <td class="px-4 py-2 text-right">—</td>
                                <td class="px-4 py-2 text-right">{{ number_format($month->total_sales, 2) }}</td>
                                <td class="px-4 py-2 text-right">{{ number_format($month->total_cost, 2) }}</td>
                                <td class="px-4 py-2 text-right text-green-700">{{ number_format($month->profit, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @empty
            <div class="text-center text-gray-500 py-8 text-lg">
                No records found for the selected filters.
            </div>
        @endforelse
    </div>
</div>
@endsection
