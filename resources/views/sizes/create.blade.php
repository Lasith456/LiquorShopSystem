@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 py-10 px-4">
    <div class="bg-white shadow-lg rounded-2xl w-full max-w-2xl p-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-indigo-700">➕ Add New Size</h2>
            <a href="{{ route('sizes.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-medium rounded-md shadow transition">
                <i class="fa fa-arrow-left mr-2"></i> Back
            </a>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                <strong>Whoops!</strong> Please correct the following errors:
                <ul class="mt-2 ml-4 list-disc">
                    @foreach ($errors->all() as $error)
                        <li class="text-sm">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('sizes.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Size Label</label>
                <input type="text" name="label" placeholder="Enter size label (e.g. Small, Medium, Large)"
                       class="w-full border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 rounded-lg shadow-sm text-sm px-4 py-2">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="4" placeholder="Enter description (optional)"
                          class="w-full border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 rounded-lg shadow-sm text-sm px-4 py-2"></textarea>
            </div>

            <div class="text-center">
                <button type="submit" 
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-6 rounded-lg shadow-md transition">
                    <i class="fa-solid fa-floppy-disk mr-2"></i> Save Size
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
