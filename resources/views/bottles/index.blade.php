@extends('layouts.app')

@section('content')
<div class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen p-6">
    <div class="max-w-5xl mx-auto bg-white rounded-2xl shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-indigo-700">🍾 Empty Bottle Records</h2>
            <a href="{{ route('bottles.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                + Add New
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 text-sm">
                <thead class="bg-indigo-600 text-white">
                    <tr>
                        <th class="px-3 py-2 text-left">Date</th>
                        <th class="px-3 py-2 text-center">Quantity</th>
                        <th class="px-3 py-2 text-right">Price per Bottle</th>
                        <th class="px-3 py-2 text-right">Total</th>
                        <th class="px-3 py-2 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bottles as $bottle)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-3 py-2">{{ $bottle->date }}</td>
                            <td class="px-3 py-2 text-center">{{ $bottle->quantity }}</td>
                            <td class="px-3 py-2 text-right">Rs. {{ number_format($bottle->price_per_bottle, 2) }}</td>
                            <td class="px-3 py-2 text-right">Rs. {{ number_format($bottle->total_price, 2) }}</td>
                            <td class="px-3 py-2 text-center">
                                <form action="{{ route('bottles.destroy', $bottle) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this record?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-gray-500 py-3">No records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $bottles->links() }}
        </div>
    </div>
</div>
@endsection
