@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-6">
  <div class="max-w-7xl mx-auto bg-white shadow-lg rounded-2xl overflow-hidden">
    
    {{-- Header --}}
    <div class="flex justify-between items-center px-6 py-4 border-b">
      <h2 class="text-2xl font-semibold text-indigo-700">🛒 Record New Sale (Size-wise)</h2>
      <a href="{{ route('sells.index') }}" class="text-sm px-4 py-2 bg-gray-700 text-white rounded-md hover:bg-gray-800 transition">
        <i class="fa fa-arrow-left mr-1"></i> Back
      </a>
    </div>

    {{-- Body --}}
    <div x-data="sellForm()" class="p-6">
      
      {{-- Sale Date --}}
      <div class="mb-5">
        <label class="block text-sm font-medium text-gray-700 mb-1">Sale Date</label>
        <input type="date" x-model="saleDate"
               class="w-full border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
      </div>

      {{-- Product Table --}}
      <div class="overflow-x-auto bg-white rounded-md border border-gray-200">
        <table class="min-w-full text-sm text-gray-700">
          <thead class="bg-indigo-600 text-white">
            <tr>
              <th class="px-3 py-2 text-left">Product</th>
              <th class="px-3 py-2 text-center">Category</th>
              <th class="px-3 py-2 text-center">Size</th>
              <th class="px-3 py-2 text-center">Current Stock</th>
              <th class="px-3 py-2 text-center">Balance Stock</th>
              <th class="px-3 py-2 text-center">Selling Price (LKR)</th>
              <th class="px-3 py-2 text-center">Qty Sold</th>
              <th class="px-3 py-2 text-right">Total (LKR)</th>
            </tr>
          </thead>
          <tbody>
            <template x-for="(item, index) in productSizes" :key="item.uniqueKey">
              <tr class="border-b hover:bg-gray-50 transition">
                <td class="px-3 py-2 font-medium" x-text="item.product_name"></td>
                <td class="px-3 py-2 text-center" x-text="item.category_name ?? '-'"></td>
                <td class="px-3 py-2 text-center" x-text="item.size_name && item.size_name !== '' ? item.size_name : '-'"></td>
                <td class="px-3 py-2 text-center font-semibold text-indigo-700" x-text="item.qty"></td>

                {{-- Balance --}}
                <td class="px-3 py-2 text-center">
                  <input type="number" min="0" :max="item.qty"
                         x-model.number="item.balance_stock"
                         @input="updateSellQty(item)"
                         class="w-24 border-gray-300 rounded-md text-center">
                </td>

                {{-- Selling Price --}}
                <td class="px-3 py-2 text-center">
                  <input type="number" min="0" step="0.01"
                         x-model.number="item.selling_price"
                         @input="updateSellQty(item)"
                         class="w-24 border-gray-300 rounded-md text-center">
                </td>

                {{-- Sold Qty --}}
                <td class="px-3 py-2 text-center font-semibold text-indigo-700"
                    x-text="item.sell_qty"></td>

                {{-- Total --}}
                <td class="px-3 py-2 text-right text-green-600 font-semibold"
                    x-text="(item.sell_qty * item.selling_price).toFixed(2)"></td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>

      {{-- Total Summary --}}
      <div class="text-right mt-6">
        <p class="text-lg font-semibold text-gray-700">
          💰 Total Sale Value: Rs. <span x-text="totalValue.toFixed(2)"></span>
        </p>
      </div>

      {{-- Submit Button --}}
      <div class="mt-6 text-right">
        <button @click="submitForm"
                class="px-6 py-2 bg-green-600 text-white font-semibold rounded-md hover:bg-green-700 transition">
          <i class="fa fa-save mr-1"></i> Save Sale Entry
        </button>
      </div>
    </div>
  </div>
</div>

<script>
function sellForm() {
  return {
    saleDate: '',
    rawProducts: @json($products),
    productSizes: [], // 🔹 make it reactive

    init() {
      console.log("📦 Full backend product list:", this.rawProducts);

      // 🔹 Flatten backend data into editable array
      this.productSizes = this.rawProducts.flatMap(p => {
        if (!p.sizes || p.sizes.length === 0) {
          return [{
            uniqueKey: `${p.id}-none`,
            product_id: p.id,
            product_name: p.name,
            category_name: p.category?.name ?? '-',
            size_name: '-',
            qty: p.qty ?? 0,
            selling_price: parseFloat(p.selling_price ?? 0),
            balance_stock: p.qty ?? 0,
            sell_qty: 0,
          }];
        }

        return p.sizes.map(s => {
          const sizeQty = Number(s.pivot?.qty ?? 0);
          const sizePrice = parseFloat(s.pivot?.selling_price ?? 0);
          return {
            uniqueKey: `${p.id}-${s.id}`,
            product_id: p.id,
            size_id: s.id,
            product_name: p.name,
            category_name: p.category?.name ?? '-',
            size_name: s.label ?? '-',
            qty: sizeQty,
            selling_price: sizePrice > 0 ? sizePrice : parseFloat(p.selling_price ?? 0),
            balance_stock: sizeQty,
            sell_qty: 0,
          };
        });
      });

      console.table(this.productSizes);
    },

    updateSellQty(item) {
      if (item.balance_stock < 0) item.balance_stock = 0;
      if (item.balance_stock > item.qty) item.balance_stock = item.qty;
      item.sell_qty = item.qty - item.balance_stock;
    },

    get totalValue() {
      return this.productSizes.reduce((sum, i) => sum + (i.sell_qty * i.selling_price), 0);
    },

    submitForm() {
      const selectedItems = this.productSizes
        .filter(i => i.sell_qty > 0)
        .map(i => ({
          id: i.product_id,
          size_id: i.size_id,
          qty: i.sell_qty,
          price: i.selling_price
        }));

      console.log("🧾 Selected Sale Items:", selectedItems);

      if (!this.saleDate) {
        alert('Please select a sale date.');
        return;
      }
      if (selectedItems.length === 0) {
        alert('Please adjust stock values to calculate sold quantities.');
        return;
      }

      axios.post('{{ route('sells.store') }}', {
        date: this.saleDate,
        items: selectedItems
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
