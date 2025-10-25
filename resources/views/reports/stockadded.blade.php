@extends('layouts.app')

@section('content')
<div class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen p-6">
    <div class="max-w-7xl mx-auto bg-white rounded-2xl shadow-md p-6">
        
        {{-- 📦 Header --}}
        <div class="flex justify-between items-center border-b pb-4 mb-6">
            <h2 class="text-2xl font-semibold text-indigo-700">📦 Stock Added Report</h2>
            <div class="space-x-2">
                <a href="{{ route('reports.stockadded.export.pdf', request()->query()) }}" 
                   class="px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                    <i class="fa fa-file-pdf mr-1"></i> Export PDF
                </a>
                <a href="{{ route('reports.stockadded.export.excel', request()->query()) }}" 
                   class="px-3 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    <i class="fa fa-file-excel mr-1"></i> Export Excel
                </a>
            </div>
        </div>

        {{-- 🔍 Filters --}}
        <form method="GET" id="filterForm" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6" x-data="filterHandler()">
            <div>
                <label class="block text-sm text-gray-600">Start Date</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" 
                       class="w-full border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-400">
            </div>

            <div>
                <label class="block text-sm text-gray-600">End Date</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" 
                       class="w-full border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-400">
            </div>

            <div>
                <label class="block text-sm text-gray-600">Category</label>
                <select name="category_id" x-model="selectedCategory" @change="filterProducts" 
                        class="w-full border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-400">
                    <option value="">All</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm text-gray-600">Product</label>
                <select name="product_id" id="productSelect" 
                        class="w-full border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-400">
                    <option value="">All</option>
                    @foreach ($products as $p)
                        <option value="{{ $p->id }}" data-category="{{ $p->category_id }}" 
                                {{ request('product_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- 🔘 Action Buttons --}}
            <div class="md:col-span-4 flex justify-end gap-3 mt-2">
                <button class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 flex items-center gap-1">
                    <i class="fa fa-filter"></i> Apply Filter
                </button>

                @if(request()->has('start_date') || request()->has('end_date') || request()->has('category_id') || request()->has('product_id'))
                    <a href="{{ route('reports.stockadded') }}" 
                       class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 flex items-center gap-1">
                        <i class="fa fa-rotate-left"></i> Clear
                    </a>
                @endif
            </div>
        </form>

        {{-- 📊 Table --}}
        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-full text-sm">
                <thead class="bg-indigo-600 text-white">
                    <tr>
                        <th class="px-3 py-2 text-left">Date</th>
                        <th class="px-3 py-2 text-left">Product</th>
                        <th class="px-3 py-2 text-center">Category</th>
                        <th class="px-3 py-2 text-center">Size</th>
                        <th class="px-3 py-2 text-center">Qty Added</th>
                        <th class="px-3 py-2 text-right">Cost Price (Rs)</th>
                        <th class="px-3 py-2 text-right">Total Cost (Rs)</th>
                    </tr>
                </thead>
                <tbody>
                    @php 
                        $totalQty = 0; 
                        $totalCost = 0; 
                    @endphp

                    @forelse ($stockItems as $item)
                        @php 
                            $totalQty += $item->qty ?? 0;
                            $totalCost += $item->total ?? 0; 
                        @endphp
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-3 py-2">{{ $item->stock->created_at->format('Y-m-d') }}</td>
                            <td class="px-3 py-2">{{ $item->product->name ?? '-' }}</td>
                            <td class="px-3 py-2 text-center">{{ $item->product->category->name ?? '-' }}</td>
                            <td class="px-3 py-2 text-center">{{ $item->size->label ?? '-' }}</td>
                            <td class="px-3 py-2 text-center font-semibold text-gray-800">{{ $item->qty }}</td>
                            <td class="px-3 py-2 text-right">Rs. {{ number_format($item->cost_price, 2) }}</td>
                            <td class="px-3 py-2 text-right font-semibold text-indigo-700">
                                Rs. {{ number_format($item->total, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-6 text-gray-500">
                                No stock records found for selected filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- 💰 Total --}}
        <div class="text-right mt-5 font-semibold text-gray-700">
            🧾 Total Quantity Added: {{ number_format($totalQty) }} <br>
            💰 Total Stock Added Value: Rs. {{ number_format($totalCost, 2) }}
        </div>
    </div>
</div>

{{-- 🧠 Dynamic Filter Script (Alpine.js) --}}
<script>
function filterHandler() {
    return {
        selectedCategory: '{{ request('category_id') ?? '' }}',
        filterProducts() {
            const selected = this.selectedCategory;
            const options = document.querySelectorAll('#productSelect option');
            options.forEach(opt => {
                if (!opt.value) return; // skip "All"
                const cat = opt.getAttribute('data-category');
                opt.style.display = (!selected || selected === cat) ? 'block' : 'none';
            });
            // reset product if it doesn't belong to category
            const currentProduct = document.querySelector('#productSelect').value;
            const visible = Array.from(options).filter(opt => opt.style.display === 'block').map(opt => opt.value);
            if (!visible.includes(currentProduct)) {
                document.querySelector('#productSelect').value = '';
            }
        }
    };
}
</script>
@endsection
