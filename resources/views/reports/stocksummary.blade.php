@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    {{-- 🏷️ Header --}}
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-indigo-700 flex items-center gap-2">
            📦 Current Stock Summary
        </h2>

        {{-- 📤 Export Buttons --}}
        <div class="flex gap-2">
            <a href="{{ route('reports.stocksummary.export.pdf', request()->all()) }}" 
               class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg shadow-md flex items-center gap-2 transition">
                <i class="fa-solid fa-file-pdf"></i> PDF
            </a>
            <a href="{{ route('reports.stocksummary.export.excel', request()->all()) }}" 
               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow-md flex items-center gap-2 transition">
                <i class="fa-solid fa-file-excel"></i> Excel
            </a>
        </div>
    </div>

    {{-- 🔍 Filter Section --}}
    <form method="GET" action="{{ route('reports.stocksummary') }}" 
          class="bg-white shadow-sm border border-gray-200 rounded-lg p-4 mb-6 flex flex-wrap gap-3 items-end">
        
        {{-- 📂 Category Filter --}}
        <div class="flex flex-col">
            <label class="text-sm text-gray-600 mb-1 font-medium">Category</label>
            <select name="category_id" 
                    class="border border-gray-300 focus:ring-2 focus:ring-indigo-400 p-2 rounded-lg text-gray-700 bg-white min-w-[200px]">
                <option value="">-- All Categories --</option>
                @foreach($categories as $c)
                    <option value="{{ $c->id }}" {{ request('category_id')==$c->id?'selected':'' }}>
                        {{ $c->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- 🔘 Action Buttons --}}
        <div class="flex gap-3 mt-5">
            <button type="submit" 
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg shadow-md flex items-center gap-2 transition">
                <i class="fa-solid fa-filter"></i> Filter
            </button>

            @if(request()->has('category_id'))
            <a href="{{ route('reports.stocksummary') }}" 
               class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-5 py-2 rounded-lg shadow-md flex items-center gap-2 transition">
                <i class="fa-solid fa-rotate-left"></i> Clear
            </a>
            @endif
        </div>
    </form>

    {{-- 📊 Stock Table --}}
    <div class="overflow-x-auto rounded-lg shadow-lg border border-gray-200 bg-white backdrop-blur">
        <table class="w-full border-collapse text-sm">
            <thead>
                <tr class="bg-gradient-to-r from-indigo-600 to-indigo-400 text-white text-left">
                    <th class="py-3 px-4 rounded-tl-lg">Product</th>
                    <th class="py-3 px-4 text-center">Category</th>
                    <th class="py-3 px-4 text-center">Available Qty</th>
                    <th class="py-3 px-4 text-right rounded-tr-lg">Selling Price (Rs)</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($stock as $p)
                    <tr class="hover:bg-gray-50 border-b border-gray-200 transition">
                        <td class="px-4 py-3 text-gray-700 font-medium">{{ $p->name }}</td>
                        <td class="px-4 py-3 text-center text-gray-600">{{ $p->category->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-center text-gray-700 font-semibold">{{ $p->qty }}</td>
                        <td class="px-4 py-3 text-right text-indigo-700 font-semibold">
                            {{ number_format($p->selling_price, 2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-6 text-gray-500">No data found for the selected filter.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
