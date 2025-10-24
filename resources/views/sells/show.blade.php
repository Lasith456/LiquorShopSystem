@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-10 px-4">
    <div class="max-w-4xl mx-auto bg-white shadow-xl rounded-2xl p-8">

        {{-- Header --}}
        <div class="flex justify-between items-center border-b pb-4 mb-6">
            <div>
                <h2 class="text-2xl font-bold text-indigo-700">🧾 Sale Details</h2>
                <p class="text-sm text-gray-500">Reference: <span class="font-semibold">{{ $sell->reference_no }}</span></p>
            </div>
            <a href="{{ route('sells.index') }}"
               class="px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white text-sm rounded-md">
               <i class="fa fa-arrow-left mr-1"></i> Back
            </a>
        </div>

        {{-- Sale Info --}}
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <p class="text-gray-600 text-sm">Date</p>
                <p class="font-medium text-gray-800">{{ $sell->date ? $sell->date->format('Y-m-d H:i') : '-' }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Recorded By</p>
                <p class="font-medium text-gray-800">{{ $sell->user->name ?? 'N/A' }}</p>
            </div>
        </div>

        {{-- Items Table --}}
        <div class="overflow-x-auto mb-6">
            <table class="min-w-full border border-gray-200 text-sm">
                <thead class="bg-indigo-600 text-white">
                    <tr>
                        <th class="px-3 py-2 text-left">Product</th>
                        <th class="px-3 py-2 text-center">Qty</th>
                        <th class="px-3 py-2 text-center">Price (LKR)</th>
                        <th class="px-3 py-2 text-center">Total (LKR)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @foreach ($sell->items as $item)
                        <tr>
                            <td class="px-3 py-2 text-gray-800">{{ $item->product->name ?? '-' }}</td>
                            <td class="px-3 py-2 text-center text-gray-600">{{ $item->qty }}</td>
                            <td class="px-3 py-2 text-center text-gray-600">Rs. {{ number_format($item->selling_price, 2) }}</td>
                            <td class="px-3 py-2 text-center text-gray-600">Rs. {{ number_format($item->total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Total --}}
        <div class="flex justify-end">
            <p class="text-lg font-semibold text-gray-800">
                Grand Total: <span class="text-indigo-700">Rs. {{ number_format($sell->total_value, 2) }}</span>
            </p>
        </div>

        <div class="mt-6 text-center text-gray-500 text-sm">
            <small>Generated on {{ now()->format('Y-m-d H:i') }}</small>
        </div>
    </div>
</div>
@endsection
