@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 py-10">
    <div class="bg-white shadow-lg rounded-2xl w-full max-w-3xl p-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-indigo-700">🛒 Add New Product</h2>
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

        <form action="{{ route('products.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Product Name --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Product Name</label>
                <input type="text" name="name" 
                       value="{{ old('name') }}" 
                       placeholder="Enter product name"
                       class="w-full border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 rounded-lg shadow-sm text-sm px-4 py-2" />
            </div>

            {{-- Product Detail --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Product Detail</label>
                <textarea name="detail" rows="4" 
                          placeholder="Enter product details..."
                          class="w-full border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 rounded-lg shadow-sm text-sm px-4 py-2">{{ old('detail') }}</textarea>
            </div>

            {{-- Category --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Category</label>
                <select name="category_id"
                        class="w-full border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 rounded-lg shadow-sm text-sm px-4 py-2">
                    <option value="">-- Select Category --</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Select Sizes --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Available Sizes</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    @foreach($sizes as $size)
                        <label class="flex items-center space-x-2 bg-gray-50 p-2 rounded-md border border-gray-200">
                            <input type="checkbox" name="sizes[]" value="{{ $size->id }}" 
                                class="text-indigo-600 rounded focus:ring-indigo-500">
                            <span class="text-gray-700 text-sm">{{ $size->label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>


            {{-- Quantity --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Quantity</label>
                <input type="number" name="qty" 
                       value="{{ old('qty') }}" 
                       placeholder="Enter stock quantity"
                       class="w-full border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 rounded-lg shadow-sm text-sm px-4 py-2" />
            </div>

            {{-- Selling Price --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Selling Price (LKR)</label>
                <input type="number" name="selling_price" step="0.01"
                       value="{{ old('selling_price') }}" 
                       placeholder="Enter selling price"
                       class="w-full border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 rounded-lg shadow-sm text-sm px-4 py-2" />
            </div>

            {{-- Submit Button --}}
            <div class="text-center">
                <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-6 rounded-lg shadow-md transition">
                    <i class="fa-solid fa-floppy-disk mr-2"></i> Save Product
                </button>
            </div>
        </form>

        <p class="text-center text-gray-500 text-sm mt-6">
            <small>Powered by <span class="text-indigo-600 font-semibold">NsoftItSolutions</span></small>
        </p>
    </div>
</div>
@endsection
