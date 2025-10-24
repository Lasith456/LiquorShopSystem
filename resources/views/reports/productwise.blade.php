@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">

    {{-- 🌟 Header --}}
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-indigo-700 flex items-center gap-2">
            🧾 Product-wise Sales, Cost & Profit Report
        </h2>
        <div class="flex gap-2">
            <a href="{{ route('reports.productwise.export.pdf', request()->all()) }}" 
               class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg shadow-md flex items-center gap-2 transition">
                <i class="fa-solid fa-file-pdf"></i> PDF
            </a>
            <a href="{{ route('reports.productwise.export.excel', request()->all()) }}" 
               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow-md flex items-center gap-2 transition">
                <i class="fa-solid fa-file-excel"></i> Excel
            </a>
        </div>
    </div>

    {{-- 🔍 Filters --}}
    <form method="GET" action="{{ route('reports.productwise') }}" 
          class="bg-white shadow-sm border border-gray-200 rounded-lg p-4 mb-6 flex flex-wrap gap-3 items-end">

        <div class="flex flex-col">
            <label class="text-sm text-gray-600 mb-1">Category</label>
            <select name="category_id" 
                class="border border-gray-300 focus:ring-2 focus:ring-indigo-400 p-2 rounded-lg w-56">
                <option value="">-- All Categories --</option>
                @foreach($categories as $c)
                    <option value="{{ $c->id }}" {{ request('category_id')==$c->id?'selected':'' }}>
                        {{ $c->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex flex-col">
            <label class="text-sm text-gray-600 mb-1">Product</label>
            <select name="product_id" 
                class="border border-gray-300 focus:ring-2 focus:ring-indigo-400 p-2 rounded-lg w-56">
                <option value="">-- All Products --</option>
                @foreach($products as $p)
                    <option value="{{ $p->id }}" {{ request('product_id')==$p->id?'selected':'' }}>
                        {{ $p->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex gap-3 mt-5">
            <button type="submit" 
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg shadow-md flex items-center gap-2 transition">
                <i class="fa-solid fa-filter"></i> Filter
            </button>

            @if(request()->has('category_id') || request()->has('product_id'))
            <a href="{{ route('reports.productwise') }}" 
               class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-5 py-2 rounded-lg shadow-md flex items-center gap-2 transition">
                <i class="fa-solid fa-rotate-left"></i> Clear
            </a>
            @endif
        </div>
    </form>

    {{-- 📊 Table --}}
    <div class="overflow-x-auto rounded-lg shadow-lg border border-gray-200 bg-white backdrop-blur">
        <table class="w-full border-collapse text-sm">
            <thead>
                <tr class="bg-gradient-to-r from-indigo-600 to-indigo-400 text-white">
                    <th class="py-3 px-4 text-left">Product</th>
                    <th class="py-3 px-4 text-left">Category</th>
                    <th class="py-3 px-4 text-center">Qty Sold</th>
                    <th class="py-3 px-4 text-right">Total Sales (Rs)</th>
                    <th class="py-3 px-4 text-right">Total Cost (Rs)</th>
                    <th class="py-3 px-4 text-right">Profit (Rs)</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $grandSales = 0;
                    $grandCost = 0;
                    $grandProfit = 0;
                @endphp

                @forelse ($productWise as $row)
                    @php
                        $grandSales += $row['total_sales'];
                        $grandCost += $row['total_cost'];
                        $grandProfit += $row['profit'];
                    @endphp
                    <tr class="hover:bg-gray-50 border-b border-gray-200 transition">
                        <td class="px-4 py-3 text-gray-700">{{ $row['product_name'] }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $row['category'] }}</td>
                        <td class="px-4 py-3 text-center text-gray-700">{{ $row['total_qty'] }}</td>
                        <td class="px-4 py-3 text-right text-indigo-700">{{ number_format($row['total_sales'], 2) }}</td>
                        <td class="px-4 py-3 text-right text-gray-700">{{ number_format($row['total_cost'], 2) }}</td>
                        <td class="px-4 py-3 text-right text-green-700 font-semibold">{{ number_format($row['profit'], 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-6 text-gray-500">No data found for selected filters.</td>
                    </tr>
                @endforelse

                @if($productWise->count() > 0)
                <tr class="bg-indigo-100 font-bold border-t border-gray-300">
                    <td colspan="3" class="px-4 py-3 text-right">TOTAL</td>
                    <td class="px-4 py-3 text-right text-indigo-700">{{ number_format($grandSales, 2) }}</td>
                    <td class="px-4 py-3 text-right text-indigo-700">{{ number_format($grandCost, 2) }}</td>
                    <td class="px-4 py-3 text-right text-green-700">{{ number_format($grandProfit, 2) }}</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
