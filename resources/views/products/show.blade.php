@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 py-10 px-4">
    <div class="bg-white shadow-lg rounded-2xl w-full max-w-4xl p-8">
        
        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-indigo-700">📦 Product Details</h2>
            <a href="{{ route('products.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-medium rounded-md shadow transition">
                <i class="fa fa-arrow-left mr-2"></i> Back
            </a>
        </div>

        {{-- Product Info --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            
            {{-- Product Name --}}
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <p class="text-sm text-gray-500 font-semibold">Product Name</p>
                <p class="text-lg font-bold text-gray-800">{{ $product->name }}</p>
            </div>

            {{-- Category --}}
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <p class="text-sm text-gray-500 font-semibold">Category</p>
                <p class="text-lg font-bold text-gray-800">
                    {{ $product->category->name ?? '—' }}
                </p>
            </div>

            {{-- Total Quantity --}}
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <p class="text-sm text-gray-500 font-semibold">Total Quantity</p>
                <p class="text-lg font-bold text-gray-800">{{ $product->qty ?? 0 }}</p>
            </div>

            {{-- Size-wise Table --}}
            <div class="sm:col-span-2 bg-gray-50 p-5 rounded-lg border border-gray-200">
                <p class="text-sm text-gray-500 font-semibold mb-3">Size-wise Stock & Price</p>

                @if ($product->sizes->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200 text-sm text-gray-700">
                            <thead class="bg-indigo-600 text-white">
                                <tr>
                                    <th class="px-3 py-2 text-left">Size</th>
                                    <th class="px-3 py-2 text-center">Quantity</th>
                                    <th class="px-3 py-2 text-center">Selling Price (LKR)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($product->sizes as $size)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="px-3 py-2 font-medium">{{ $size->label ?? $size->name }}</td>
                                        <td class="px-3 py-2 text-center">{{ $size->pivot->qty ?? 0 }}</td>
                                        <td class="px-3 py-2 text-center text-green-700">
                                            Rs. {{ number_format($size->pivot->selling_price ?? 0, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500 text-sm italic">No sizes assigned.</p>
                @endif
            </div>

            {{-- Description --}}
            <div class="sm:col-span-2 bg-gray-50 p-5 rounded-lg border border-gray-200">
                <p class="text-sm text-gray-500 font-semibold mb-1">Description</p>
                <p class="text-gray-700 leading-relaxed">{{ $product->detail ?? 'No description available.' }}</p>
            </div>

            {{-- Created / Updated Info --}}
            <div class="sm:col-span-2 text-sm text-gray-500 mt-4 text-right">
                <p>Created at: <span class="text-gray-700 font-medium">{{ $product->created_at->format('d M Y, h:i A') }}</span></p>
                <p>Last updated: <span class="text-gray-700 font-medium">{{ $product->updated_at->format('d M Y, h:i A') }}</span></p>
            </div>
        </div>

        {{-- Footer --}}
        <p class="text-center text-gray-500 text-sm mt-8">
            <small>Powered by <span class="text-indigo-600 font-semibold">NsoftItSolutions</span></small>
        </p>
    </div>
</div>
@endsection
