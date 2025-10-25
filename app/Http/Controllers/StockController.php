<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\StockItem;
use App\Models\Product;
use App\Models\Category;
use App\Models\Size;
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

    /** 🧾 View all stock entries */
    public function index(Request $request): View
    {
        $query = Stock::with('user');

        if ($request->filled('search')) {
            $query->where('reference_no', 'like', "%{$request->search}%");
        }

        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('date', [$request->from, $request->to]);
        } elseif ($request->filled('from')) {
            $query->whereDate('date', '>=', $request->from);
        } elseif ($request->filled('to')) {
            $query->whereDate('date', '<=', $request->to);
        }

        $stocks = $query->latest()->paginate(10);
        $i = (request()->input('page', 1) - 1) * 10;

        return view('stocks.index', compact('stocks', 'i'));
    }


    /** 🏗️ Create form */
    public function create(): View
    {
        $products = Product::with('sizes')->get();
        $categories = Category::all();
        return view('stocks.create', compact('categories', 'products'));
    }

    /** 💾 Store new stock entry */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.size_id' => 'nullable|exists:sizes,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.cost_price' => 'required|numeric|min:0',
        ]);

        $reference = 'STK-' . strtoupper(Str::random(6));
        $totalValue = collect($request->items)->sum(fn($item) => $item['qty'] * $item['cost_price']);

        $stock = Stock::create([
            'reference_no' => $reference,
            'date' => now(),
            'total_value' => $totalValue,
            'user_id' => Auth::id(),
        ]);

        foreach ($request->items as $item) {
            // Save in stock_items table
            StockItem::create([
                'stock_id' => $stock->id,
                'product_id' => $item['product_id'],
                'size_id' => $item['size_id'] ?? null,
                'qty' => $item['qty'],
                'cost_price' => $item['cost_price'],
                'total' => $item['qty'] * $item['cost_price'],
            ]);

            // 🧮 Update product total quantity
            $product = Product::find($item['product_id']);
            $product->increment('qty', $item['qty']);

            // 📏 If size specified, update pivot table qty
            if (!empty($item['size_id'])) {
                $existing = $product->sizes()
                    ->where('size_id', $item['size_id'])
                    ->first();

                if ($existing) {
                    $newQty = $existing->pivot->qty + $item['qty'];
                    $product->sizes()->updateExistingPivot($item['size_id'], [
                        'qty' => $newQty,
                        'selling_price' => $existing->pivot->selling_price ?? 0,
                    ]);
                } else {
                    $product->sizes()->attach($item['size_id'], [
                        'qty' => $item['qty'],
                        'selling_price' => 0,
                    ]);
                }
            }
        }

        return response()->json(['success' => true, 'message' => '✅ Stock added successfully!']);
    }

    /** 🔍 View stock entry */
    public function show(Stock $stock): View
    {
        $stock->load(['items.product', 'items.size', 'user']);
        return view('stocks.show', compact('stock'));
    }

    /** 🗑️ Delete stock entry */
    public function destroy(Stock $stock): RedirectResponse
    {
        $stock->load('items');

        foreach ($stock->items as $item) {
            $product = $item->product;
            if ($product) {
                $newQty = max(0, $product->qty - $item->qty);
                $product->update(['qty' => $newQty]);

                if ($item->size_id) {
                    $pivot = $product->sizes()->where('size_id', $item->size_id)->first();
                    if ($pivot) {
                        $newSizeQty = max(0, $pivot->pivot->qty - $item->qty);
                        $product->sizes()->updateExistingPivot($item->size_id, ['qty' => $newSizeQty]);
                    }
                }
            }
            $item->delete();
        }

        $stock->delete();

        return redirect()->route('stocks.index')
            ->with('success', '🗑️ Stock deleted and size quantities updated successfully!');
    }
}
