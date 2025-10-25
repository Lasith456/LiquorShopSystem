@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-indigo-100 py-10 px-4">
    <div class="max-w-5xl mx-auto bg-white shadow-xl rounded-2xl p-8">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b pb-4 mb-6">
            <div>
                <h2 class="text-2xl font-bold text-indigo-700 flex items-center">
                    🧾 <span class="ml-2">Sale Details</span>
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Reference No: <span class="font-semibold text-gray-800">{{ $sell->reference_no }}</span>
                </p>
            </div>
            <a href="{{ route('sells.index') }}"
               class="mt-3 sm:mt-0 px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white text-sm rounded-md shadow-md transition">
               <i class="fa-solid fa-arrow-left mr-1"></i> Back
            </a>
        </div>

        {{-- Sale Info --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8 bg-gray-50 p-4 rounded-lg border border-gray-200">
            <div>
                <p class="text-gray-600 text-sm">📅 Sale Date</p>
                <p class="font-semibold text-gray-800 mt-1">
                    {{ $sell->date ? $sell->date->format('Y-m-d H:i') : '-' }}
                </p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">👤 Recorded By</p>
                <p class="font-semibold text-gray-800 mt-1">{{ $sell->user->name ?? 'N/A' }}</p>
            </div>
        </div>

        {{-- Items Table --}}
        <div class="overflow-x-auto mb-6">
            <table class="min-w-full border border-gray-200 rounded-md overflow-hidden text-sm">
                <thead class="bg-indigo-600 text-white uppercase">
                    <tr>
                        <th class="px-3 py-2 text-left">Product</th>
                        <th class="px-3 py-2 text-center">Category</th>
                        <th class="px-3 py-2 text-center">Size</th>
                        <th class="px-3 py-2 text-center">Qty</th>
                        <th class="px-3 py-2 text-center">Price (LKR)</th>
                        <th class="px-3 py-2 text-center">Total (LKR)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @foreach ($sell->items as $item)
                        <tr class="hover:bg-indigo-50 transition">
                            <td class="px-3 py-2 font-medium text-gray-800">
                                {{ $item->product->name ?? '-' }}
                            </td>
                            <td class="px-3 py-2 text-center text-gray-700">
                                {{ $item->product->category->name ?? '-' }}
                            </td>
                            <td class="px-3 py-2 text-center text-gray-700">
                                {{ $item->size->label ?? '-' }}
                            </td>
                            <td class="px-3 py-2 text-center text-gray-700 font-semibold">{{ $item->qty }}</td>
                            <td class="px-3 py-2 text-center text-gray-700">
                                Rs. {{ number_format($item->selling_price, 2) }}
                            </td>
                            <td class="px-3 py-2 text-center text-green-700 font-semibold">
                                Rs. {{ number_format($item->total, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Summary Section --}}
        <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-5 text-right">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                <p class="text-gray-600 font-medium text-sm">💵 Total Sale Value</p>
                <p class="text-2xl font-bold text-indigo-700">
                    Rs. {{ number_format($sell->total_value, 2) }}
                </p>
            </div>
        </div>

        {{-- Footer --}}
        <div class="mt-8 text-center text-gray-500 text-sm">
            <p>🕒 Generated on {{ now()->format('Y-m-d H:i') }}</p>
            <p class="mt-1">
                <small>Powered by <span class="text-indigo-600 font-semibold">NsoftItSolutions</span></small>
            </p>
        </div>
    </div>
</div>
@endsection
