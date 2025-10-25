@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 py-10">
    <div class="bg-white shadow-lg rounded-2xl w-full max-w-4xl p-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-indigo-700">✏️ Edit Product (Size-wise Stock & Price)</h2>
            <a href="{{ route('products.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-medium rounded-md shadow transition">
                <i class="fa fa-arrow-left mr-2"></i> Back
            </a>
        </div>

        {{-- Error Messages --}}
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                <strong class="font-semibold">Whoops!</strong> There were some problems with your input.
                <ul class="mt-2 ml-4 list-disc">
                    @foreach ($errors->all() as $error)
                        <li class="text-sm">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('products.update', $product->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Product Info --}}
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Product Name</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}"
                           class="w-full border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 rounded-lg text-sm px-4 py-2" />
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Category</label>
                    <select name="category_id"
                            class="w-full border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 rounded-lg text-sm px-4 py-2">
                        <option value="">-- Select Category --</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" 
                                {{ (old('category_id', $product->category_id) == $category->id) ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Product Detail --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Product Details</label>
                <textarea name="detail" rows="4"
                          class="w-full border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 rounded-lg text-sm px-4 py-2">{{ old('detail', $product->detail) }}</textarea>
            </div>

            {{-- Size-wise Stock & Price --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Available Sizes with Quantity & Price</label>

                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200 text-sm text-gray-700">
                        <thead class="bg-indigo-600 text-white">
                            <tr>
                                <th class="px-3 py-2 text-center">Select</th>
                                <th class="px-3 py-2 text-left">Size</th>
                                <th class="px-3 py-2 text-center">Quantity</th>
                                <th class="px-3 py-2 text-center">Selling Price (LKR)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sizes as $size)
                            @php
                                $isChecked = array_key_exists($size->id, $sizeQuantities);
                                $qty = $sizeQuantities[$size->id] ?? 0;
                                $price = $sizePrices[$size->id] ?? 0;
                            @endphp
                            <tr class="border-b">
                                <td class="px-3 py-2 text-center">
                                    <input type="checkbox" name="sizes[{{ $size->id }}][checked]" value="1"
                                           class="text-indigo-600 focus:ring-indigo-500"
                                           {{ $isChecked ? 'checked' : '' }}>
                                </td>
                                <td class="px-3 py-2 font-medium text-gray-800">
                                    {{ $size->label ?? $size->name }}
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <input type="number" name="sizes[{{ $size->id }}][qty]" min="0"
                                           value="{{ old('sizes.'.$size->id.'.qty', $qty) }}"
                                           class="w-24 text-center border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <input type="number" step="0.01" min="0"
                                           name="sizes[{{ $size->id }}][selling_price]"
                                           value="{{ old('sizes.'.$size->id.'.selling_price', $price) }}"
                                           class="w-32 text-center border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="text-xs text-gray-500 mt-2">✔️ Adjust quantities or prices for each size as needed.</p>
            </div>

            {{-- Submit --}}
            <div class="text-center">
                <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-6 rounded-lg shadow-md transition">
                    <i class="fa-solid fa-floppy-disk mr-2"></i> Update Product
                </button>
            </div>
        </form>

        <p class="text-center text-gray-500 text-sm mt-6">
            <small>Powered by <span class="text-indigo-600 font-semibold">NsoftItSolutions</span></small>
        </p>
    </div>
</div>
@endsection
