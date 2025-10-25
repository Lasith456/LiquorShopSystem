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
    public function index(Request $request): View
    {
        $query = Product::with(['category', 'sizes']);

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $products = $query->latest()->paginate(10);
        $categories = Category::all();
        $i = (request()->input('page', 1) - 1) * 10;

        return view('products.index', compact('products', 'i', 'categories'));
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
     * Store a newly created product in storage (size-wise qty + price).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'detail' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'sizes' => 'required|array',
        ]);

        // Step 1: Create product
        $product = Product::create([
            'name' => $request->name,
            'detail' => $request->detail,
            'category_id' => $request->category_id,
            'qty' => 0, // total qty across all sizes
            'selling_price' => 0, // optional main price reference
        ]);

        // Step 2: Attach sizes with qty + selling_price
        $syncData = [];
        $totalQty = 0;

        foreach ($request->sizes as $sizeId => $data) {
            if (isset($data['checked']) && $data['checked'] == 1) {
                $qty = isset($data['qty']) ? (int) $data['qty'] : 0;
                $price = isset($data['selling_price']) ? (float) $data['selling_price'] : 0;
                $totalQty += $qty;

                $syncData[$sizeId] = [
                    'qty' => $qty,
                    'selling_price' => $price,
                ];
            }
        }

        if (!empty($syncData)) {
            $product->sizes()->sync($syncData);
            $product->update([
                'qty' => $totalQty,
                'selling_price' => collect($syncData)->avg('selling_price') ?? 0, // optional average price
            ]);
        }

        return redirect()->route('products.index')
            ->with('success', '✅ Product created successfully with size-wise quantities and prices.');
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

        // Load existing pivot data
        $product->load('sizes');
        $sizeQuantities = $product->sizes->pluck('pivot.qty', 'id')->toArray();
        $sizePrices = $product->sizes->pluck('pivot.selling_price', 'id')->toArray();

        return view('products.edit', compact('product', 'sizes', 'categories', 'sizeQuantities', 'sizePrices'));
    }

    /**
     * Update the specified product in storage (size-wise qty + price).
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'detail' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'sizes' => 'required|array',
        ]);

        // Step 1: Update main product info
        $product->update([
            'name' => $request->name,
            'detail' => $request->detail,
            'category_id' => $request->category_id,
        ]);

        // Step 2: Sync size-wise qty and price
        $syncData = [];
        $totalQty = 0;

        foreach ($request->sizes as $sizeId => $data) {
            if (isset($data['checked']) && $data['checked'] == 1) {
                $qty = isset($data['qty']) ? (int) $data['qty'] : 0;
                $price = isset($data['selling_price']) ? (float) $data['selling_price'] : 0;
                $totalQty += $qty;

                $syncData[$sizeId] = [
                    'qty' => $qty,
                    'selling_price' => $price,
                ];
            }
        }

        $product->sizes()->sync($syncData);

        $product->update([
            'qty' => $totalQty,
            'selling_price' => collect($syncData)->avg('selling_price') ?? 0,
        ]);

        return redirect()->route('products.index')
            ->with('success', '✅ Product updated successfully with size-wise quantities and prices.');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $product->sizes()->detach();
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', '🗑️ Product deleted successfully.');
    }
}
