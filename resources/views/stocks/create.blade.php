@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-6">
    <div class="max-w-7xl mx-auto bg-white shadow-lg rounded-2xl overflow-hidden">

        {{-- Header --}}
        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200">
            <h2 class="text-2xl font-semibold text-indigo-700">📦 Add New Stock</h2>
            <a href="{{ route('stocks.index') }}" class="text-sm px-4 py-2 bg-gray-700 text-white rounded-md hover:bg-gray-800 transition">
                <i class="fa fa-arrow-left mr-1"></i> Back
            </a>
        </div>

        {{-- Two-column layout --}}
        <div x-data="stockForm()" class="flex flex-col lg:flex-row divide-y lg:divide-y-0 lg:divide-x divide-gray-200">

            {{-- LEFT SIDE - Add products --}}
            <div class="w-full lg:w-1/2 p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">➕ Add Products</h3>

                {{-- Category Filter --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-600 mb-1">Select Category</label>
                    <select x-model="selectedCategory" @change="filterProducts" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- All Categories --</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Product Selector --}}
                <div class="mb-4 relative">
                    <label class="block text-sm font-medium text-gray-600 mb-1">Search Product</label>
                    <input type="text" x-model="searchQuery" @input="filterProducts"
                           placeholder="Type product name..." 
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">

                    {{-- Dropdown --}}
                    <ul x-show="filteredProducts.length > 0" class="absolute z-10 bg-white border border-gray-200 rounded-md mt-1 w-full max-h-48 overflow-y-auto shadow-lg">
                        <template x-for="product in filteredProducts" :key="product.id">
                            <li @click="selectProduct(product)"
                                class="px-3 py-2 text-sm hover:bg-indigo-100 cursor-pointer"
                                x-text="product.name"></li>
                        </template>
                    </ul>
                </div>

                {{-- Quantity & Cost Price --}}
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Quantity</label>
                        <input type="number" x-model="newItem.qty" min="1"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Cost Price (LKR)</label>
                        <input type="number" x-model="newItem.cost" min="0" step="0.01"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                {{-- Add Button --}}
                <button @click="addProduct"
                        class="w-full py-2 bg-indigo-600 text-white text-sm font-semibold rounded-md hover:bg-indigo-700 transition">
                    <i class="fa fa-plus mr-1"></i> Add to Stock List
                </button>
            </div>

            {{-- RIGHT SIDE - Stock list --}}
            <div class="w-full lg:w-1/2 p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">🧾 Added Products</h3>

                <template x-if="addedProducts.length === 0">
                    <p class="text-gray-400 italic">No products added yet.</p>
                </template>

                <div class="overflow-x-auto mt-3" x-show="addedProducts.length > 0">
                    <table class="min-w-full border border-gray-200 text-sm">
                        <thead class="bg-indigo-600 text-white">
                            <tr>
                                <th class="px-3 py-2 text-left">Product</th>
                                <th class="px-3 py-2 text-center">Qty</th>
                                <th class="px-3 py-2 text-center">Cost</th>
                                <th class="px-3 py-2 text-center">Total</th>
                                <th class="px-3 py-2 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, index) in addedProducts" :key="index">
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-3 py-2" x-text="item.name"></td>
                                    <td class="px-3 py-2 text-center" x-text="item.qty"></td>
                                    <td class="px-3 py-2 text-center" x-text="item.cost.toFixed(2)"></td>
                                    <td class="px-3 py-2 text-center" x-text="(item.qty * item.cost).toFixed(2)"></td>
                                    <td class="px-3 py-2 text-center">
                                        <button @click="removeProduct(index)" class="text-red-600 hover:text-red-800">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>

                    <div class="text-right mt-4 font-semibold text-gray-700">
                        Total Value: Rs. <span x-text="totalValue.toFixed(2)"></span>
                    </div>

                    {{-- Submit --}}
                    <button @click="submitForm"
                            class="w-full mt-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 font-semibold">
                        <i class="fa fa-save mr-1"></i> Save Stock Entry
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function stockForm() {
    return {
        selectedCategory: '',
        searchQuery: '',
        allProducts: @json($products),  // full product list passed from controller
        filteredProducts: [],
        addedProducts: [],
        newItem: { id: '', name: '', qty: 1, cost: 0 },

        filterProducts() {
            const query = this.searchQuery.toLowerCase();
            this.filteredProducts = this.allProducts.filter(p =>
                (!this.selectedCategory || p.category_id == this.selectedCategory) &&
                p.name.toLowerCase().includes(query)
            );
        },

        selectProduct(product) {
            this.newItem.id = product.id;
            this.newItem.name = product.name;
            this.searchQuery = product.name;
            this.filteredProducts = [];
        },

        addProduct() {
            if (!this.newItem.id || this.newItem.qty <= 0 || this.newItem.cost <= 0) {
                alert('Please fill all fields properly.');
                return;
            }
            this.addedProducts.push({ ...this.newItem });
            this.newItem = { id: '', name: '', qty: 1, cost: 0 };
            this.searchQuery = '';
        },

        removeProduct(index) {
            this.addedProducts.splice(index, 1);
        },

        get totalValue() {
            return this.addedProducts.reduce((sum, item) => sum + (item.qty * item.cost), 0);
        },

submitForm() {
    if (this.addedProducts.length === 0) {
        alert('Add at least one product.');
        return;
    }

    axios.post("{{ route('stocks.store') }}", {
        items: this.addedProducts
    })
    .then(response => {
        alert('✅ Stock entry saved successfully!');
        window.location.href = "{{ route('stocks.index') }}"; // redirect after success
    })
    .catch(error => {
        console.error(error);
        alert('❌ Failed to save stock entry. Check console for details.');
    });
}

    }
}
</script>
@endsection
