@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-6">
    <div class="max-w-5xl mx-auto bg-white shadow-lg rounded-2xl p-6">
        <div class="flex justify-between items-center mb-6 border-b pb-3">
            <div>
                <h2 class="text-2xl font-bold text-indigo-700">📋 Stock Details</h2>
                <p class="text-sm text-gray-500 mt-1">Reference: {{ $stock->reference_no }}</p>
            </div>
            <a href="{{ route('stocks.index') }}" 
               class="px-4 py-2 bg-gray-700 text-white rounded-md hover:bg-gray-800 transition">
                <i class="fa fa-arrow-left mr-1"></i> Back
            </a>
        </div>

        <div class="mb-4">
            <p><strong>Date:</strong> {{ $stock->date->format('Y-m-d H:i') }}</p>
            <p><strong>Added By:</strong> {{ $stock->user->name ?? 'System' }}</p>
            <p><strong>Total Value:</strong> Rs. {{ number_format($stock->total_value, 2) }}</p>
        </div>

        <h3 class="text-lg font-semibold text-gray-700 mb-3">🧾 Products in Stock</h3>

        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 text-sm">
                <thead class="bg-indigo-600 text-white">
                    <tr>
                        <th class="px-3 py-2 text-left">Product</th>
                        <th class="px-3 py-2 text-center">Qty</th>
                        <th class="px-3 py-2 text-center">Cost</th>
                        <th class="px-3 py-2 text-center">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @foreach ($stock->items as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2">{{ $item->product->name ?? '-' }}</td>
                            <td class="px-3 py-2 text-center">{{ $item->qty }}</td>
                            <td class="px-3 py-2 text-center">{{ number_format($item->cost_price, 2) }}</td>
                            <td class="px-3 py-2 text-center">{{ number_format($item->total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="text-right mt-4 font-semibold text-gray-700">
            Total Value: Rs. {{ number_format($stock->total_value, 2) }}
        </div>
    </div>
</div>
@endsection
