@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 py-10 px-4">
    <div class="bg-white shadow-lg rounded-2xl w-full max-w-2xl p-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-indigo-700">📘 Size Details</h2>
            <a href="{{ route('sizes.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-medium rounded-md shadow transition">
                <i class="fa fa-arrow-left mr-2"></i> Back
            </a>
        </div>

        <div class="space-y-4">
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <p class="text-sm text-gray-500 font-semibold">Label</p>
                <p class="text-lg font-bold text-gray-800">{{ $size->label }}</p>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <p class="text-sm text-gray-500 font-semibold">Description</p>
                <p class="text-gray-700 leading-relaxed">{{ $size->description }}</p>
            </div>

            <div class="text-sm text-gray-500 mt-4 text-right">
                <p>Created at: <span class="text-gray-700 font-medium">{{ $size->created_at->format('d M Y, h:i A') }}</span></p>
                <p>Last updated: <span class="text-gray-700 font-medium">{{ $size->updated_at->format('d M Y, h:i A') }}</span></p>
            </div>
        </div>

        <p class="text-center text-gray-500 text-sm mt-8">
            <small>Powered by <span class="text-indigo-600 font-semibold">NsoftItSolutions</span></small>
        </p>
    </div>
</div>
@endsection
