@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-6">
    <div class="max-w-6xl mx-auto bg-white shadow-lg rounded-2xl p-6">

        {{-- Header --}}
        <div class="flex justify-between items-center mb-6 border-b pb-3">
            <div>
                <h2 class="text-2xl font-bold text-indigo-700 flex items-center gap-2">
                    📋 Stock Details
                    <span class="text-sm text-gray-500 font-normal">({{ $stock->reference_no }})</span>
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Added on <strong>{{ $stock->date->format('d M Y, h:i A') }}</strong>
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('stocks.index') }}" 
                   class="px-4 py-2 bg-gray-700 text-white rounded-md hover:bg-gray-800 transition">
                    <i class="fa fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>

        {{-- Stock Summary Cards --}}
        <div class="grid sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-indigo-50 border border-indigo-200 p-4 rounded-lg shadow-sm">
                <p class="text-sm text-gray-600 font-medium">📅 Date</p>
                <p class="text-lg font-semibold text-gray-800 mt-1">
                    {{ $stock->date->format('Y-m-d H:i') }}
                </p>
            </div>

            <div class="bg-green-50 border border-green-200 p-4 rounded-lg shadow-sm">
                <p class="text-sm text-gray-600 font-medium">👤 Added By</p>
                <p class="text-lg font-semibold text-gray-800 mt-1">
                    {{ $stock->user->name ?? 'System' }}
                </p>
            </div>

            <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg shadow-sm">
                <p class="text-sm text-gray-600 font-medium">💰 Total Value</p>
                <p class="text-lg font-bold text-green-700 mt-1">
                    Rs. {{ number_format($stock->total_value, 2) }}
                </p>
            </div>
        </div>

        {{-- Products Table --}}
        <h3 class="text-lg font-semibold text-gray-700 mb-3">🧾 Products in Stock</h3>

        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-full text-sm text-gray-700">
                <thead class="bg-indigo-600 text-white">
                    <tr>
                        <th class="px-4 py-2 text-left">#</th>
                        <th class="px-4 py-2 text-left">Product</th>
                        <th class="px-4 py-2 text-left">Size</th>
                        <th class="px-4 py-2 text-center">Qty</th>
                        <th class="px-4 py-2 text-center">Cost Price (LKR)</th>
                        <th class="px-4 py-2 text-center">Total (LKR)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @foreach ($stock->items as $index => $item)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-2 text-gray-600">{{ $index + 1 }}</td>
                            <td class="px-4 py-2 font-medium">{{ $item->product->name ?? '-' }}</td>
                            <td class="px-4 py-2">
                                @if ($item->size_id)
                                    <span class="px-2 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs font-semibold">
                                        {{ $item->size->label ?? $item->size->name }}
                                    </span>
                                @else
                                    <span class="text-gray-400 italic text-xs">N/A</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-center font-semibold">{{ $item->qty }}</td>
                            <td class="px-4 py-2 text-center">Rs. {{ number_format($item->cost_price, 2) }}</td>
                            <td class="px-4 py-2 text-center font-semibold text-green-700">
                                Rs. {{ number_format($item->total, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Total Summary --}}
        <div class="text-right mt-6">
            <p class="text-lg font-bold text-gray-800">
                🧮 Total Stock Value: 
                <span class="text-green-700">Rs. {{ number_format($stock->total_value, 2) }}</span>
            </p>
        </div>

        {{-- Footer --}}
        <p class="text-center text-gray-500 text-sm mt-8">
            <small>Generated on {{ now()->format('d M Y, h:i A') }} by 
                <span class="text-indigo-600 font-semibold">NsoftItSolutions</span>
            </small>
        </p>
    </div>
</div>
@endsection
