<?php

namespace App\Http\Controllers;

use App\Models\Sell;
use App\Models\SellItem;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SellController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:sell-list|sell-create|sell-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:sell-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:sell-delete', ['only' => ['destroy']]);
    }

    /**
     * Display all sales
     */
    public function index(Request $request)
    {
        $query = Sell::with('user')->latest();

        // ✅ Apply date filter if provided
        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('date', [$request->from, $request->to]);
        } elseif ($request->filled('from')) {
            $query->whereDate('date', '>=', $request->from);
        } elseif ($request->filled('to')) {
            $query->whereDate('date', '<=', $request->to);
        }

        $sells = $query->paginate(10);

        return view('sells.index', compact('sells'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }


    /**
     * Show the sale creation form
     */
    public function create(): View
    {
        $categories = Category::all();

        // Load all products with their sizes and pivot fields
        $products = Product::with([
            'category:id,name',
            'sizes' => function ($q) {
                $q->select('sizes.id', 'sizes.label');
            }
        ])->get(['id', 'name', 'category_id', 'qty', 'selling_price']);
        return view('sells.create', compact('categories', 'products'));
    }

    /**
     * Store a new sale entry
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.size_id' => 'nullable|exists:sizes,id',
        ]);

        DB::beginTransaction();

        try {
            $reference = 'SEL-' . strtoupper(Str::random(6));

            $totalValue = collect($validated['items'])->sum(fn($item) => $item['qty'] * $item['price']);

            $sell = Sell::create([
                'reference_no' => $reference,
                'date' => $validated['date'],
                'total_value' => $totalValue,
                'user_id' => Auth::id(),
            ]);

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['id']);
                $sizeId = $item['size_id'] ?? null;

                // Handle size-based stock decrement
                if ($sizeId) {
                    $pivot = DB::table('product_size')
                        ->where('product_id', $product->id)
                        ->where('size_id', $sizeId)
                        ->first();

                    if (!$pivot || $pivot->qty < $item['qty']) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => "Insufficient stock for {$product->name} ({$sizeId})."
                        ], 400);
                    }

                    // Decrease pivot stock
                    DB::table('product_size')
                        ->where('product_id', $product->id)
                        ->where('size_id', $sizeId)
                        ->update([
                            'qty' => $pivot->qty - $item['qty'],
                            'selling_price' => $item['price'], // update latest selling price
                            'updated_at' => now(),
                        ]);
                } else {
                    // No size: reduce product qty directly
                    if ($product->qty < $item['qty']) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => "Insufficient stock for {$product->name}."
                        ], 400);
                    }
                    $product->decrement('qty', $item['qty']);
                    $product->update(['selling_price' => $item['price']]);
                }

                // Record sale item
                SellItem::create([
                    'sell_id' => $sell->id,
                    'product_id' => $product->id,
                    'size_id' => $sizeId,
                    'qty' => $item['qty'],
                    'selling_price' => $item['price'],
                    'total' => $item['qty'] * $item['price'],
                ]);
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

    /**
     * Show details of a single sale
     */
    public function show(Sell $sell): View
    {
        $sell->load(['items.product', 'user']);
        return view('sells.show', compact('sell'));
    }

    /**
     * Delete a sale and restore stock
     */
    public function destroy(Sell $sell): RedirectResponse
    {
        $sell->load('items.product');

        foreach ($sell->items as $item) {
            $product = $item->product;
            if ($product) {
                if ($item->size_id) {
                    $pivot = DB::table('product_size')
                        ->where('product_id', $product->id)
                        ->where('size_id', $item->size_id)
                        ->first();

                    if ($pivot) {
                        DB::table('product_size')
                            ->where('product_id', $product->id)
                            ->where('size_id', $item->size_id)
                            ->update(['qty' => $pivot->qty + $item->qty]);
                    }
                } else {
                    $product->increment('qty', $item->qty);
                }
            }

            $item->delete();
        }

        $sell->delete();

        return redirect()->route('sells.index')
            ->with('success', 'Sale record deleted and stock restored successfully!');
    }
}
