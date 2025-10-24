@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-10 px-4">
    <div class="max-w-7xl mx-auto bg-white shadow-lg rounded-2xl p-6">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-indigo-700 mb-4 sm:mb-0">📊 Reports & Analytics</h2>
            
            {{-- Export Buttons --}}
            <div class="space-x-2">
                <a href="{{ route('reports.export.excel') }}" class="px-3 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700 transition">
                    <i class="fa fa-file-excel mr-1"></i> Export Excel
                </a>
                <a href="{{ route('reports.export.pdf') }}" class="px-3 py-2 bg-red-600 text-white text-sm rounded-md hover:bg-red-700 transition">
                    <i class="fa fa-file-pdf mr-1"></i> Export PDF
                </a>
            </div>
        </div>

        {{-- Quick Filters --}}
        <div class="flex flex-wrap gap-3 mb-6">
            <a href="{{ route('reports.index', ['quick_filter' => 'today']) }}" 
               class="px-4 py-2 rounded-md text-sm font-medium {{ $filter == 'today' ? 'bg-indigo-700 text-white' : 'bg-gray-200 hover:bg-indigo-100' }}">
                📅 Today
            </a>
            <a href="{{ route('reports.index', ['quick_filter' => 'week']) }}" 
               class="px-4 py-2 rounded-md text-sm font-medium {{ $filter == 'week' ? 'bg-indigo-700 text-white' : 'bg-gray-200 hover:bg-indigo-100' }}">
                📆 This Week
            </a>
            <a href="{{ route('reports.index') }}" 
               class="px-4 py-2 rounded-md text-sm font-medium {{ !$filter ? 'bg-indigo-700 text-white' : 'bg-gray-200 hover:bg-indigo-100' }}">
                🗓️ This Month
            </a>
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
            <div>
                <label class="block text-sm text-gray-600">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="w-full border-gray-300 rounded-md">
            </div>
            <div>
                <label class="block text-sm text-gray-600">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="w-full border-gray-300 rounded-md">
            </div>
            <div>
                <label class="block text-sm text-gray-600">Category</label>
                <select name="category_id" class="w-full border-gray-300 rounded-md">
                    <option value="">All</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-600">Product</label>
                <select name="product_id" class="w-full border-gray-300 rounded-md">
                    <option value="">All</option>
                    @foreach ($products as $p)
                        <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-4 text-right">
                <button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md">
                    <i class="fa fa-filter mr-1"></i> Apply Filters
                </button>
            </div>
        </form>

        {{-- Existing report tables (day-wise, monthly, product-wise, stock summary) --}}
        @include('reports.partials.tables', [
            'dayWise' => $dayWise,
            'monthly' => $monthly,
            'sellItems' => $sellItems,
            'stock' => $stock
        ])

        <p class="text-center text-gray-500 text-sm mt-6">
            <small>Generated on {{ now()->format('Y-m-d H:i') }} | Powered by <span class="text-indigo-600 font-semibold">NsoftItSolutions</span></small>
        </p>
    </div>
</div>
@endsection
