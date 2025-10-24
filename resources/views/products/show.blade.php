@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 py-10 px-4">
    <div class="bg-white shadow-lg rounded-2xl w-full max-w-3xl p-8">
        
        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-indigo-700">📦 Product Details</h2>
            <a href="{{ route('products.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-medium rounded-md shadow transition">
                <i class="fa fa-arrow-left mr-2"></i> Back
            </a>
        </div>

        {{-- Product Info Card --}}
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

            {{-- Sizes --}}
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <p class="text-sm text-gray-500 font-semibold mb-1">Available Sizes</p>
                @if ($product->sizes->count())
                    <div class="flex flex-wrap gap-2">
                        @foreach($product->sizes as $size)
                            <span class="bg-indigo-100 text-indigo-700 text-sm px-3 py-1 rounded-full font-medium">
                                {{ $size->label }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-sm">No sizes assigned.</p>
                @endif
            </div>

            {{-- Quantity --}}
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <p class="text-sm text-gray-500 font-semibold">Quantity</p>
                <p class="text-lg font-bold text-gray-800">{{ $product->qty ?? 0 }}</p>
            </div>

            {{-- Selling Price --}}
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 sm:col-span-2">
                <p class="text-sm text-gray-500 font-semibold">Selling Price</p>
                <p class="text-lg font-bold text-green-700">
                    Rs. {{ number_format($product->selling_price ?? 0, 2) }}
                </p>
            </div>

            {{-- Description --}}
            <div class="sm:col-span-2 bg-gray-50 p-5 rounded-lg border border-gray-200">
                <p class="text-sm text-gray-500 font-semibold mb-1">Description</p>
                <p class="text-gray-700 leading-relaxed">{{ $product->detail }}</p>
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
