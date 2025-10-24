<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProductController extends Controller
{ 
    public function __construct()
    {
        $this->middleware('permission:product-list|product-create|product-edit|product-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:product-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:product-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:product-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the products.
     */
    public function index(): View
    {
        // Fetch products with category & sizes eager loaded
        $products = Product::with(['category', 'sizes'])
            ->latest()
            ->paginate(10);

        // Calculate index offset for pagination
        $i = (request()->input('page', 1) - 1) * 10;

        return view('products.index', compact('products', 'i'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(): View
    {
        $sizes = Size::all();
        $categories = Category::all();

        return view('products.create', compact('sizes', 'categories'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'detail' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'qty' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'sizes' => 'array',
        ]);

        $product = Product::create($request->only('name', 'detail', 'category_id', 'qty', 'selling_price'));

        // Attach selected sizes
        if ($request->has('sizes')) {
            $product->sizes()->attach($request->sizes);
        }

        return redirect()->route('products.index')->with('success', '✅ Product created successfully.');
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product): View
    {
        $product->load(['category', 'sizes']);
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product): View
    {
        $sizes = Size::all();
        $categories = Category::all();
        $selectedSizes = $product->sizes->pluck('id')->toArray();

        return view('products.edit', compact('product', 'sizes', 'categories', 'selectedSizes'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'detail' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'qty' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'sizes' => 'array',
        ]);

        $product->update($request->only('name', 'detail', 'category_id', 'qty', 'selling_price'));

        // Sync sizes
        $product->sizes()->sync($request->sizes ?? []);

        return redirect()->route('products.index')->with('success', '✅ Product updated successfully.');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', '🗑️ Product deleted successfully.');
    }
}
