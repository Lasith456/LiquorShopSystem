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


    {{-- 📊 Monthly Table --}}
    <div class="overflow-x-auto rounded-lg shadow-lg border border-gray-200 bg-white backdrop-blur">
        <table class="w-full border-collapse text-sm">
            <thead>
                <tr class="bg-gradient-to-r from-indigo-600 to-indigo-400 text-white text-left">
                    <th class="py-3 px-4 rounded-tl-lg">Month</th>
                    <th class="py-3 px-4 text-right">Total Sales (Rs)</th>
                    <th class="py-3 px-4 text-right">Total Cost (Rs)</th>
                    <th class="py-3 px-4 text-right rounded-tr-lg">Profit (Rs)</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $grandSales = $monthly->sum('total_sales');
                    $grandCost = $monthly->sum('total_cost');
                    $grandProfit = $monthly->sum('profit');
                @endphp

                @forelse ($monthly as $data)
                    <tr class="hover:bg-gray-50 border-b border-gray-200 transition">
                        <td class="px-4 py-3 text-gray-700 font-medium">
                            {{ \Carbon\Carbon::parse($data['month'].'-01')->format('F Y') }}
                        </td>
                        <td class="px-4 py-3 text-right text-gray-700">{{ number_format($data['total_sales'], 2) }}</td>
                        <td class="px-4 py-3 text-right text-gray-700">{{ number_format($data['total_cost'], 2) }}</td>
                        <td class="px-4 py-3 text-right text-green-700 font-semibold">
                            {{ number_format($data['profit'], 2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-6 text-gray-500">No records found for the selected filters.</td>
                    </tr>
                @endforelse

                {{-- 🧮 Grand Totals --}}
                @if($monthly->count() > 0)
                <tr class="bg-indigo-100 font-bold border-t border-gray-300">
                    <td class="px-4 py-3 text-right">TOTAL</td>
                    <td class="px-4 py-3 text-right text-indigo-700">{{ number_format($grandSales, 2) }}</td>
                    <td class="px-4 py-3 text-right text-indigo-700">{{ number_format($grandCost, 2) }}</td>
                    <td class="px-4 py-3 text-right text-green-700">{{ number_format($grandProfit, 2) }}</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
<script>
document.querySelectorAll('input[type="month"]').forEach(el => {
    // fallback: if browser doesn't support type="month", use type="text" with format hint
    if (el.type !== 'month') {
        el.type = 'text';
        el.placeholder = 'YYYY-MM';
        el.pattern = '\\d{4}-\\d{2}';
    }
});
</script>
@endsection
