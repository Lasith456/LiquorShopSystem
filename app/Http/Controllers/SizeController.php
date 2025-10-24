<?php

namespace App\Http\Controllers;

use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SizeController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:size-list|size-create|size-edit|size-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:size-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:size-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:size-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of sizes.
     */
    public function index(): View
    {
        $sizes = Size::latest()->paginate(10);

        return view('sizes.index', compact('sizes'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }

    /**
     * Show the form for creating a new size.
     */
    public function create(): View
    {
        return view('sizes.create');
    }

    /**
     * Store a newly created size in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255|unique:sizes,label',
            'code'  => 'nullable|string|max:50|unique:sizes,code',
        ]);

        Size::create($validated);

        return redirect()->route('sizes.index')
            ->with('success', '✅ Size created successfully.');
    }

    /**
     * Display the specified size.
     */
    public function show(Size $size): View
    {
        return view('sizes.show', compact('size'));
    }

    /**
     * Show the form for editing the specified size.
     */
    public function edit(Size $size): View
    {
        return view('sizes.edit', compact('size'));
    }

    /**
     * Update the specified size in storage.
     */
    public function update(Request $request, Size $size): RedirectResponse
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255|unique:sizes,label,' . $size->id,
            'code'  => 'nullable|string|max:50|unique:sizes,code,' . $size->id,
        ]);

        $size->update($validated);

        return redirect()->route('sizes.index')
            ->with('success', '✅ Size updated successfully.');
    }

    /**
     * Remove the specified size from storage.
     */
    public function destroy(Size $size): RedirectResponse
    {
        $size->delete();

        return redirect()->route('sizes.index')
            ->with('success', '🗑️ Size deleted successfully.');
    }
}
