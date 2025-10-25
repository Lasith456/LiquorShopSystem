@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-10 px-4">
    <div class="max-w-7xl mx-auto bg-white shadow-lg rounded-2xl p-6">
        
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-3">
            <h2 class="text-2xl font-bold text-indigo-700">🛍️ Products (Size-wise)</h2>

            @can('product-create')
                <a href="{{ route('products.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow transition">
                    <i class="fa fa-plus mr-2"></i> Add Product
                </a>
            @endcan
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('products.index') }}" class="mb-5 flex flex-col sm:flex-row gap-3 items-center justify-between">
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                {{-- Search by name --}}
                <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Search by product name..."
                       class="border border-gray-300 rounded-md px-4 py-2 w-full sm:w-72 focus:ring-indigo-500 focus:border-indigo-500 text-sm">

                {{-- Category Filter --}}
                <select name="category" class="border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700 shadow">
                    <i class="fa fa-filter mr-1"></i> Filter
                </button>
            </div>

            {{-- Reset --}}
            @if(request('search') || request('category'))
                <a href="{{ route('products.index') }}" class="text-sm text-gray-500 hover:text-red-600 transition">
                    <i class="fa fa-times-circle mr-1"></i> Clear Filters
                </a>
            @endif
        </form>

        {{-- Success Message --}}
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
                {{ session('success') }}
            </div>
        @endif

        {{-- Products Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
                <thead class="bg-indigo-600 text-white">
                    <tr>
                        <th class="px-4 py-2 text-left text-sm font-semibold">#</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold">Product</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold">Category</th>
                        <th class="px-4 py-2 text-center text-sm font-semibold">Total Qty</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold">Sizes & Stock</th>
                        <th class="px-4 py-2 text-center text-sm font-semibold">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($products as $product)
                        <tr class="hover:bg-indigo-50 transition">
                            <td class="px-4 py-3 text-sm text-gray-700 align-top">{{ ++$i }}</td>

                            {{-- Product Info --}}
                            <td class="px-4 py-3 text-sm font-medium text-gray-800 align-top">
                                {{ $product->name }}
                                <p class="text-gray-500 text-xs">{{ Str::limit($product->detail, 60) ?? '-' }}</p>
                            </td>

                            {{-- Category --}}
                            <td class="px-4 py-3 text-sm text-gray-600 align-top">{{ $product->category->name ?? '-' }}</td>

                            {{-- Total Qty --}}
                            <td class="px-4 py-3 text-sm text-center font-semibold text-gray-700 align-top">{{ $product->qty ?? 0 }}</td>

                            {{-- Size Table --}}
                            <td class="px-4 py-3 text-xs text-gray-700 align-top">
                                @if ($product->sizes->isNotEmpty())
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full border border-gray-200 text-xs">
                                            <thead class="bg-gray-100 text-gray-700">
                                                <tr>
                                                    <th class="px-2 py-1 text-left">Size</th>
                                                    <th class="px-2 py-1 text-center">Qty</th>
                                                    <th class="px-2 py-1 text-center">Price</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($product->sizes as $size)
                                                    <tr>
                                                        <td class="px-2 py-1">{{ $size->label ?? $size->name }}</td>
                                                        <td class="px-2 py-1 text-center">{{ $size->pivot->qty }}</td>
                                                        <td class="px-2 py-1 text-center">
                                                            Rs. {{ number_format($size->pivot->selling_price ?? 0, 2) }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <span class="text-gray-400 italic">No sizes linked</span>
                                @endif
                            </td>

                            {{-- Compact Icon Buttons --}}
                            <td class="px-4 py-3 text-center align-top space-x-2">
                                {{-- Show --}}
                                <a href="{{ route('products.show', $product->id) }}" 
                                   class="inline-flex items-center justify-center w-8 h-8 bg-blue-500 hover:bg-blue-600 text-white rounded-full shadow" 
                                   title="View">
                                   <i class="fa-solid fa-eye text-xs"></i>
                                </a>

                                {{-- Edit --}}
                                @can('product-edit')
                                    <a href="{{ route('products.edit', $product->id) }}" 
                                       class="inline-flex items-center justify-center w-8 h-8 bg-yellow-500 hover:bg-yellow-600 text-white rounded-full shadow" 
                                       title="Edit">
                                       <i class="fa-solid fa-pen text-xs"></i>
                                    </a>
                                @endcan

                                {{-- Delete --}}
                                @can('product-delete')
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                onclick="return confirm('Are you sure you want to delete this product?')"
                                                class="inline-flex items-center justify-center w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded-full shadow" 
                                                title="Delete">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-gray-500">No products found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {!! $products->links() !!}
        </div>

        {{-- Footer --}}
        <p class="text-center text-gray-500 text-sm mt-6">
            <small>Powered by <span class="text-indigo-600 font-semibold">NsoftItSolutions</span></small>
        </p>
    </div>
</div>
@endsection
