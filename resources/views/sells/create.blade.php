@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-6">
    <div class="max-w-7xl mx-auto bg-white shadow-lg rounded-2xl overflow-hidden">
        <div class="flex justify-between items-center px-6 py-4 border-b">
            <h2 class="text-2xl font-semibold text-indigo-700">🛒 Record New Sale</h2>
            <a href="{{ route('sells.index') }}" class="text-sm px-4 py-2 bg-gray-700 text-white rounded-md hover:bg-gray-800 transition">
                <i class="fa fa-arrow-left mr-1"></i> Back
            </a>
        </div>

        <div x-data="sellForm()" class="flex flex-col lg:flex-row divide-y lg:divide-x divide-gray-200">

            {{-- LEFT SIDE --}}
            <div class="w-full lg:w-1/2 p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">🧾 Add Products Sold</h3>

                {{-- Date --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-600 mb-1">Sale Date</label>
                    <input type="date" x-model="saleDate" class="w-full border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                {{-- Category Filter --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-600 mb-1">Select Category</label>
                    <select x-model="selectedCategory" @change="filterProducts" class="w-full border-gray-300 rounded-md">
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
                           class="w-full border-gray-300 rounded-md">
                    <ul x-show="filteredProducts.length > 0" class="absolute z-10 bg-white border rounded-md mt-1 w-full max-h-48 overflow-y-auto shadow-lg">
                        <template x-for="product in filteredProducts" :key="product.id">
                            <li @click="selectProduct(product)" class="px-3 py-2 hover:bg-indigo-100 cursor-pointer" x-text="product.name"></li>
                        </template>
                    </ul>
                </div>

                {{-- Qty & Price --}}
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Quantity</label>
                        <input type="number" x-model="newItem.qty" min="1"
                               class="w-full border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Selling Price (LKR)</label>
                        <input type="number" x-model="newItem.price" min="0" step="0.01"
                               class="w-full border-gray-300 rounded-md">
                    </div>
                </div>

                <button @click="addProduct" class="w-full py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    <i class="fa fa-plus mr-1"></i> Add to Sale List
                </button>
            </div>

            {{-- RIGHT SIDE --}}
            <div class="w-full lg:w-1/2 p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">📋 Products Sold</h3>

                <template x-if="addedProducts.length === 0">
                    <p class="text-gray-400 italic">No products added yet.</p>
                </template>

                <div class="overflow-x-auto mt-3" x-show="addedProducts.length > 0">
                    <table class="min-w-full border text-sm">
                        <thead class="bg-indigo-600 text-white">
                            <tr>
                                <th class="px-3 py-2 text-left">Product</th>
                                <th class="px-3 py-2 text-center">Qty</th>
                                <th class="px-3 py-2 text-center">Price</th>
                                <th class="px-3 py-2 text-center">Total</th>
                                <th class="px-3 py-2 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, index) in addedProducts" :key="index">
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-3 py-2" x-text="item.name"></td>
                                    <td class="px-3 py-2 text-center" x-text="item.qty"></td>
                                    <td class="px-3 py-2 text-center" x-text="item.price.toFixed(2)"></td>
                                    <td class="px-3 py-2 text-center" x-text="(item.qty * item.price).toFixed(2)"></td>
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
                        Total: Rs. <span x-text="totalValue.toFixed(2)"></span>
                    </div>

                    <button @click="submitForm" class="w-full mt-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        <i class="fa fa-save mr-1"></i> Save Sale Entry
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function sellForm() {
    return {
        saleDate: '',
        selectedCategory: '',
        searchQuery: '',
        allProducts: @json($products),
        filteredProducts: [],
        addedProducts: [],
        newItem: { id: '', name: '', qty: 1, price: 0 },

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
            if (!this.newItem.id || this.newItem.qty <= 0 || this.newItem.price <= 0) {
                alert('Please fill all fields correctly.');
                return;
            }
            this.addedProducts.push({ ...this.newItem });
            this.newItem = { id: '', name: '', qty: 1, price: 0 };
            this.searchQuery = '';
        },

        removeProduct(index) {
            this.addedProducts.splice(index, 1);
        },

        get totalValue() {
            return this.addedProducts.reduce((sum, item) => sum + (item.qty * item.price), 0);
        },

        submitForm() {
            if (!this.saleDate || this.addedProducts.length === 0) {
                alert('Please select a date and add at least one product.');
                return;
            }

            axios.post('{{ route('sells.store') }}', {
                date: this.saleDate,
                items: this.addedProducts
            })
            .then(res => {
                alert(res.data.message);
                window.location.href = "{{ route('sells.index') }}";
            })
            .catch(err => {
                console.error(err);
                alert('Error saving sale.');
            });
        }
    }
}
</script>
@endsection
