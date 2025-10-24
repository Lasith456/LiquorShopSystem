@extends('layouts.app')

@section('content')
<div class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen p-6">
    <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-md p-6">
        <h2 class="text-2xl font-semibold text-indigo-700 mb-6">➕ Add Empty Bottle Entry</h2>

        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('bottles.store') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm text-gray-600 mb-1">Date</label>
                <input type="date" name="date" value="{{ old('date') }}" class="w-full border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-400">
            </div>

            <div>
                <label class="block text-sm text-gray-600 mb-1">Quantity</label>
                <input type="number" name="quantity" min="1" value="{{ old('quantity') }}" class="w-full border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-400">
            </div>

            <div>
                <label class="block text-sm text-gray-600 mb-1">Price per Bottle (LKR)</label>
                <input type="number" name="price_per_bottle" step="0.01" min="0" value="{{ old('price_per_bottle') }}" class="w-full border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-400">
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <a href="{{ route('bottles.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                    Cancel
                </a>
                <button class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Save Record
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
