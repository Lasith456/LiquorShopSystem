@extends('layouts.app')

@section('content')
<div class="bg-gray-50 min-h-screen p-6">
    <div class="max-w-7xl mx-auto space-y-6">
        {{-- Welcome Header --}}
        <div class="bg-white shadow rounded-2xl p-6 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-indigo-700">🏠 Dashboard</h2>
                <p class="text-gray-600">Welcome back, {{ Auth::user()->name ?? 'User' }}!</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">Today: {{ now()->format('l, d M Y') }}</p>
            </div>
        </div>

        {{-- Quick Shortcuts --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <a href="{{ route('reports.daywise') }}" class="bg-gradient-to-r from-indigo-500 to-blue-600 text-white p-5 rounded-xl shadow hover:scale-105 transform transition">
                <h3 class="text-lg font-semibold mb-2">📅 Daywise Report</h3>
                <p class="text-sm text-indigo-100">View daily sales and costs</p>
            </a>
            <a href="{{ route('reports.monthly') }}" class="bg-gradient-to-r from-green-500 to-emerald-600 text-white p-5 rounded-xl shadow hover:scale-105 transform transition">
                <h3 class="text-lg font-semibold mb-2">📆 Monthly Report</h3>
                <p class="text-sm text-green-100">Track monthly performance</p>
            </a>
            <a href="{{ route('reports.productwise') }}" class="bg-gradient-to-r from-yellow-400 to-orange-500 text-white p-5 rounded-xl shadow hover:scale-105 transform transition">
                <h3 class="text-lg font-semibold mb-2">📦 Productwise Report</h3>
                <p class="text-sm text-yellow-100">Check product-level stats</p>
            </a>
            <a href="{{ route('reports.bottles') }}" class="bg-gradient-to-r from-pink-500 to-rose-600 text-white p-5 rounded-xl shadow hover:scale-105 transform transition">
                <h3 class="text-lg font-semibold mb-2">🍾 Empty Bottle Report</h3>
                <p class="text-sm text-pink-100">Monitor bottle returns</p>
            </a>
        </div>

        {{-- Analytics Panels --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Low Stock Products --}}
            <div class="bg-white rounded-2xl shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-red-600">⚠️ Low Stock Products</h3>
                    <a href="{{ route('reports.stocksummary') }}" class="text-sm text-indigo-600 hover:underline">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200 text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-3 py-2 text-left">Product</th>
                                <th class="px-3 py-2 text-center">Qty</th>
                                <th class="px-3 py-2 text-right">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $lowStockProducts = \App\Models\Product::orderBy('qty', 'asc')->take(10)->get();
                            @endphp
                            @forelse($lowStockProducts as $product)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-3 py-2">{{ $product->name }}</td>
                                    <td class="px-3 py-2 text-center text-red-600 font-semibold">{{ $product->qty }}</td>
                                    <td class="px-3 py-2 text-right">Rs. {{ number_format($product->selling_price, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-gray-500 py-3">No products found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- High Demand Products --}}
            <div class="bg-white rounded-2xl shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-green-600">🔥 High Demand Products</h3>
                    <a href="{{ route('reports.productwise') }}" class="text-sm text-indigo-600 hover:underline">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200 text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-3 py-2 text-left">Product</th>
                                <th class="px-3 py-2 text-center">Total Sold</th>
                                <th class="px-3 py-2 text-right">Revenue (LKR)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $highDemand = \App\Models\SellItem::selectRaw('product_id, SUM(qty) as total_sold, SUM(total) as revenue')
                                    ->groupBy('product_id')
                                    ->orderByDesc('total_sold')
                                    ->take(10)
                                    ->get();
                            @endphp
                            @forelse($highDemand as $item)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-3 py-2">{{ $item->product->name ?? 'Unknown' }}</td>
                                    <td class="px-3 py-2 text-center text-green-600 font-semibold">{{ $item->total_sold }}</td>
                                    <td class="px-3 py-2 text-right">Rs. {{ number_format($item->revenue, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-gray-500 py-3">No sales data available.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="text-center text-gray-500 text-sm mt-6">
            © {{ date('Y') }} NSOFT IT Solutions — All Rights Reserved
        </div>
    </div>
</div>
@endsection
