<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\StockItem;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class StockController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:stock-list|stock-create|stock-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:stock-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:stock-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the stocks.
     */
    public function index(): View
    {
        $stocks = Stock::with('user')
            ->latest()
            ->paginate(10);

        return view('stocks.index', compact('stocks'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }

    /**
     * Show the form for creating a new stock entry.
     */
    public function create(): View
    {
        $categories = Category::all();
        $products = Product::all();

        return view('stocks.create', compact('categories', 'products'));
    }

    /**
     * Store a new stock entry via Axios (AJAX).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.cost' => 'required|numeric|min:0',
        ]);

        $reference = 'STK-' . strtoupper(Str::random(6));
        $totalValue = collect($request->items)->sum(fn($item) => $item['qty'] * $item['cost']);

        $stock = Stock::create([
            'reference_no' => $reference,
            'date' => now(),
            'total_value' => $totalValue,
            'user_id' => Auth::id(),
        ]);

        foreach ($request->items as $item) {
            StockItem::create([
                'stock_id' => $stock->id,
                'product_id' => $item['id'],
                'qty' => $item['qty'],
                'cost_price' => $item['cost'],
                'total' => $item['qty'] * $item['cost'],
            ]);

            Product::where('id', $item['id'])->increment('qty', $item['qty']);
        }

        return response()->json(['success' => true, 'message' => 'Stock entry saved successfully!']);
    }

    /**
     * Display a single stock entry with products.
     */
    public function show(Stock $stock): View
    {
        $stock->load(['items.product', 'user']);
        return view('stocks.show', compact('stock'));
    }

    /**
     * Remove a stock record.
     */
    public function destroy(Stock $stock): RedirectResponse
    {
        // Load related items
        $stock->load('items');

        // Loop through each stock item and reduce product quantity
        foreach ($stock->items as $item) {
            $product = $item->product;
            if ($product) {
                // Decrease product quantity but ensure it doesn't go below zero
                $newQty = max(0, $product->qty - $item->qty);
                $product->update(['qty' => $newQty]);
            }

            // Delete the stock item
            $item->delete();
        }

        // Delete the stock record itself
        $stock->delete();

        return redirect()->route('stocks.index')
            ->with('success', '🗑️ Stock deleted and product quantities adjusted successfully!');
    }

}
