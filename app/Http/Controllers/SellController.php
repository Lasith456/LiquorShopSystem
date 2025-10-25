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
use Illuminate\Support\Facades\DB;

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
    // Load each product with its category and all attached sizes
    $products = Product::with(['category', 'sizes'])->get();

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

        DB::beginTransaction();

        try {
            // Generate unique sale reference
            $reference = 'SEL-' . strtoupper(Str::random(6));

            // Calculate total sale value
            $totalValue = collect($validated['items'])->sum(function ($item) {
                return $item['qty'] * $item['price'];
            });

            // Create main Sell record
            $sell = Sell::create([
                'reference_no' => $reference,
                'date' => $validated['date'],
                'total_value' => $totalValue,
                'user_id' => Auth::id(),
            ]);

            // Process each sold item
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['id']);

                // Check stock availability
                if ($item['qty'] > $product->qty) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Insufficient stock for {$product->name}. Available: {$product->qty}"
                    ], 400);
                }

                // Create sale item
                SellItem::create([
                    'sell_id' => $sell->id,
                    'product_id' => $product->id,
                    'qty' => $item['qty'],
                    'selling_price' => $item['price'],
                    'total' => $item['qty'] * $item['price'],
                ]);

                // Update product stock and price
                $product->decrement('qty', $item['qty']);
                $product->update(['selling_price' => $item['price']]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sale recorded successfully!',
                'reference_no' => $sell->reference_no,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error saving sale: ' . $e->getMessage(),
            ], 500);
        }
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
