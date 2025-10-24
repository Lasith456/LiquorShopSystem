<?php

namespace App\Http\Controllers;

use App\Models\Sell;
use App\Models\SellItem;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class SellController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:sell-list|sell-create|sell-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:sell-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:sell-delete', ['only' => ['destroy']]);
    }

    public function index(): View
    {
        $sells = Sell::with('user')->latest()->paginate(10);
        return view('sells.index', compact('sells'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function create(): View
    {
        $categories = Category::all();
        $products = Product::all();

        return view('sells.create', compact('categories', 'products'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        $reference = 'SEL-' . strtoupper(Str::random(6));
        $totalValue = collect($request->items)->sum(fn($item) => $item['qty'] * $item['price']);

        $sell = Sell::create([
            'reference_no' => $reference,
            'date' => $request->date,
            'total_value' => $totalValue,
            'user_id' => Auth::id(),
        ]);

        foreach ($request->items as $item) {
            SellItem::create([
                'sell_id' => $sell->id,
                'product_id' => $item['id'],
                'qty' => $item['qty'],
                'selling_price' => $item['price'],
                'total' => $item['qty'] * $item['price'],
            ]);

            // Decrease product quantity
            Product::where('id', $item['id'])->decrement('qty', $item['qty']);
        }

        return response()->json(['success' => true, 'message' => 'Sell entry saved successfully!']);
    }

    public function show(Sell $sell): View
    {
        $sell->load(['items.product', 'user']);
        return view('sells.show', compact('sell'));
    }

    public function destroy(Sell $sell): RedirectResponse
    {
        $sell->load('items.product');

        foreach ($sell->items as $item) {
            $product = $item->product;
            if ($product) {
                // Revert sold qty
                $product->increment('qty', $item->qty);
            }
            $item->delete();
        }

        $sell->delete();

        return redirect()->route('sells.index')
            ->with('success', 'Sell record deleted and quantities restored!');
    }
}
