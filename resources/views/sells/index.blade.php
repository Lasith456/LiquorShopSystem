@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-10 px-4">
    <div class="max-w-6xl mx-auto bg-white shadow-lg rounded-2xl p-6">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6 border-b pb-3">
            <h2 class="text-2xl font-bold text-indigo-700 mb-4 sm:mb-0 flex items-center">
                💰 <span class="ml-2">Sell Records</span>
            </h2>

            @can('sell-create')
                <a href="{{ route('sells.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-md shadow transition-all duration-150">
                    <i class="fa-solid fa-plus mr-2"></i> Record New Sale
                </a>
            @endcan
        </div>

        {{-- Filter Form --}}
        <form method="GET" action="{{ route('sells.index') }}" class="mb-6 bg-gray-50 rounded-lg p-4 shadow-sm">
            <div class="flex flex-col sm:flex-row gap-4 sm:items-end justify-between">
                <div class="flex gap-4 w-full sm:w-auto">
                    {{-- From Date --}}
                    <div class="flex flex-col">
                        <label for="from" class="text-sm font-medium text-gray-700 mb-1">From Date</label>
                        <input type="date" id="from" name="from" value="{{ request('from') }}"
                               class="border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 w-full sm:w-48">
                    </div>

                    {{-- To Date --}}
                    <div class="flex flex-col">
                        <label for="to" class="text-sm font-medium text-gray-700 mb-1">To Date</label>
                        <input type="date" id="to" name="to" value="{{ request('to') }}"
                               class="border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 w-full sm:w-48">
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="flex gap-3">
                    <button type="submit"
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md shadow font-medium text-sm">
                        <i class="fa-solid fa-filter mr-1"></i> Apply Filter
                    </button>
                    <a href="{{ route('sells.index') }}"
                       class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-md shadow font-medium text-sm">
                        <i class="fa-solid fa-rotate-left mr-1"></i> Reset
                    </a>
                </div>
            </div>
        </form>

        {{-- Success Message --}}
        @if (session('success'))
            <div class="mb-5 px-4 py-3 border border-green-400 text-green-700 bg-green-100 rounded-lg flex items-center">
                <i class="fa-solid fa-circle-check mr-2 text-green-600"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Sell Records Table --}}
        @if ($sells->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
                    <thead class="bg-indigo-600 text-white">
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-semibold">#</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold">Reference No</th>
                            <th class="px-4 py-2 text-center text-sm font-semibold">Date</th>
                            <th class="px-4 py-2 text-center text-sm font-semibold">Total Value (LKR)</th>
                            <th class="px-4 py-2 text-center text-sm font-semibold">Recorded By</th>
                            <th class="px-4 py-2 text-center text-sm font-semibold">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach ($sells as $index => $sell)
                            <tr class="hover:bg-indigo-50 transition duration-150 ease-in-out">
                                <td class="px-4 py-3 text-sm text-gray-700 font-medium">{{ $loop->iteration + ($sells->currentPage() - 1) * $sells->perPage() }}</td>
                                <td class="px-4 py-3 text-sm text-gray-800 font-semibold">{{ $sell->reference_no }}</td>
                                <td class="px-4 py-3 text-sm text-center text-gray-600">
                                    {{ $sell->date ? $sell->date->format('Y-m-d') : '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-center text-gray-700 font-medium">
                                    <span class="text-green-700 font-semibold">
                                        Rs. {{ number_format($sell->total_value, 2) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-center text-gray-600">
                                    {{ $sell->user->name ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3 text-center space-x-2">
                                    <a href="{{ route('sells.show', $sell->id) }}"
                                       class="inline-flex items-center px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold rounded-md shadow transition">
                                        <i class="fa-solid fa-eye mr-1"></i> View
                                    </a>

                                    @can('sell-delete')
                                        <form action="{{ route('sells.destroy', $sell->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                onclick="return confirm('Are you sure you want to delete this sale?')"
                                                class="inline-flex items-center px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-semibold rounded-md shadow transition">
                                                <i class="fa-solid fa-trash mr-1"></i> Delete
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-6 flex justify-between items-center">
                <p class="text-sm text-gray-500">
                    Showing <span class="font-semibold">{{ $sells->firstItem() }}</span> to
                    <span class="font-semibold">{{ $sells->lastItem() }}</span> of
                    <span class="font-semibold">{{ $sells->total() }}</span> entries
                </p>
                <div>
                    {!! $sells->appends(request()->query())->links() !!}
                </div>
            </div>
        @else
            {{-- Empty State --}}
            <div class="text-center py-16 text-gray-500">
                <i class="fa-solid fa-box-open text-5xl mb-3 text-gray-400"></i>
                <p class="text-lg font-medium">No sale records found.</p>
                <p class="text-sm text-gray-400 mt-1">Try adjusting your date range or reset the filter.</p>
            </div>
        @endif

        {{-- Footer --}}
        <p class="text-center text-gray-500 text-sm mt-10">
            <small>Powered by <span class="text-indigo-600 font-semibold">NsoftItSolutions</span></small>
        </p>
    </div>
</div>
@endsection
