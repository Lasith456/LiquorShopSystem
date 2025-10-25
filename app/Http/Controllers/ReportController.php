<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Product;
use App\Models\Category;
use App\Models\Sell;
use App\Models\SellItem;
use App\Models\StockItem;
use App\Models\Bottle;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\ArrayExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Exports\GenericExport;
class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:report-view');
    }

    /* =======================================================
    📅 1️⃣ DAY-WISE SALES REPORT
    ======================================================= */
    public function daywise(Request $request): \Illuminate\View\View
    {
        [$startDate, $endDate, $categoryId, $productId] = $this->getFilters($request);

        // ✅ Get latest cost per (product_id, size_id) if tracked by size
        $latestCosts = \App\Models\StockItem::select('product_id', 'size_id', \DB::raw('MAX(id) as max_id'))
            ->groupBy('product_id', 'size_id')
            ->pluck('max_id', 'product_id');

        $costMap = \App\Models\StockItem::whereIn('id', $latestCosts)
            ->get()
            ->mapWithKeys(fn($i) => [($i->product_id . '-' . ($i->size_id ?? 0)) => $i->cost_price]);

        // ✅ Load sells with products, sizes, categories
        $query = \App\Models\SellItem::with(['product.category', 'size', 'sell'])
            ->whereHas('sell', fn($q) => $q->whereBetween('date', [$startDate, $endDate]));

        if ($categoryId) {
            $query->whereHas('product', fn($q) => $q->where('category_id', $categoryId));
        }
        if ($productId) {
            $query->where('product_id', $productId);
        }

        $sellItems = $query->get();

        // ✅ Group by day
        $dayWise = $sellItems->groupBy(fn($i) => \Carbon\Carbon::parse($i->sell->date)->format('Y-m-d'))
            ->map(function ($group, $day) use ($costMap) {
                $totalSales = 0;
                $totalCost  = 0;

                $group->each(function ($i) use (&$totalSales, &$totalCost, $costMap) {
                    $costKey = $i->product_id . '-' . ($i->size_id ?? 0);
                    $i->latest_cost_price = $costMap[$costKey] ?? 0;
                    $i->profit = ($i->qty * $i->selling_price) - ($i->qty * $i->latest_cost_price);
                    $totalSales += $i->qty * $i->selling_price;
                    $totalCost  += $i->qty * $i->latest_cost_price;
                });

                return (object)[
                    'day'         => $day,
                    'total_sales' => $totalSales,
                    'total_cost'  => $totalCost,
                    'profit'      => $totalSales - $totalCost,
                    'items'       => $group,
                ];
            })
            ->values();

        return view('reports.daywise', [
            'dayWise'   => $dayWise,
            'categories'=> \App\Models\Category::all(),
            'products'  => \App\Models\Product::all(),
            'startDate' => $startDate,
            'endDate'   => $endDate,
        ]);
    }

    public function exportDaywisePDF(Request $request)
    {
        [$startDate, $endDate, $categoryId, $productId] = $this->getFilters($request);

        // ✅ Preload latest cost prices
        $latestCosts = StockItem::select('product_id', \DB::raw('MAX(id) as max_id'))
            ->groupBy('product_id')
            ->pluck('max_id', 'product_id');

        $costMap = StockItem::whereIn('id', $latestCosts)
            ->pluck('cost_price', 'product_id');

        // ✅ Fetch filtered data
        $query = SellItem::with(['product.category', 'sell'])
            ->whereHas('sell', fn($q) => $q->whereBetween('date', [$startDate, $endDate]));

        if ($categoryId) {
            $query->whereHas('product', fn($q) => $q->where('category_id', $categoryId));
        }

        if ($productId) {
            $query->where('product_id', $productId);
        }

        $sellItems = $query->get();

        // ✅ Group and calculate totals
        $dayWise = $sellItems->groupBy(fn($i) => Carbon::parse($i->sell->date)->format('Y-m-d'))
            ->map(function ($group, $day) use ($costMap) {
                $totalSales = $group->sum(fn($i) => $i->qty * ($i->selling_price ?? 0));
                $totalCost = $group->sum(function ($i) use ($costMap) {
                    $cost = $costMap[$i->product_id] ?? 0;
                    return $i->qty * $cost;
                });
                $profit = $totalSales - $totalCost;

                $group->each(function ($i) use ($costMap) {
                    $i->latest_cost_price = $costMap[$i->product_id] ?? 0;
                });

                return (object) [
                    'day'          => $day,
                    'total_sales'  => $totalSales,
                    'total_cost'   => $totalCost,
                    'profit'       => $profit,
                    'items'        => $group,
                ];
            })
            ->values();

        // ✅ Generate PDF
        $pdf = Pdf::loadView('reports.pdf.daywise', [
            'dayWise'   => $dayWise,
            'categories'=> Category::all(),
            'products'  => Product::all(),
            'startDate' => $startDate,
            'endDate'   => $endDate,
        ])->setPaper('A4', 'portrait');

        $fileName = 'Daywise_Report_' . Carbon::now()->format('Y_m_d_His') . '.pdf';
        return $pdf->download($fileName);
    }
    public function exportDaywiseExcel(Request $request)
    {
        [$startDate, $endDate, $categoryId, $productId] = $this->getFilters($request);

        // ✅ Load latest cost prices once for performance
        $latestCosts = StockItem::select('product_id', DB::raw('MAX(id) as max_id'))
            ->groupBy('product_id')
            ->pluck('max_id', 'product_id');

        $costMap = StockItem::whereIn('id', $latestCosts)->pluck('cost_price', 'product_id');

        // ✅ Filter sell items
        $query = SellItem::with(['product.category', 'sell'])
            ->whereHas('sell', fn($q) => $q->whereBetween('date', [$startDate, $endDate]));

        if ($categoryId) {
            $query->whereHas('product', fn($q) => $q->where('category_id', $categoryId));
        }
        if ($productId) {
            $query->where('product_id', $productId);
        }

        $sellItems = $query->get();

        // ✅ Transform to exportable array
        $exportData = [];
        foreach ($sellItems->groupBy(fn($i) => Carbon::parse($i->sell->date)->format('Y-m-d')) as $day => $group) {
            foreach ($group as $i) {
                $cost = $costMap[$i->product_id] ?? 0;
                $totalSale = $i->qty * ($i->selling_price ?? 0);
                $totalCost = $i->qty * $cost;

                $exportData[] = [
                    'Date'           => $day,
                    'Category'       => $i->product->category->name ?? '-',
                    'Product'        => $i->product->name ?? '-',
                    'Quantity Sold'  => $i->qty,
                    'Selling Price'  => number_format($i->selling_price ?? 0, 2),
                    'Cost Price'     => number_format($cost, 2),
                    'Total Sales'    => number_format($totalSale, 2),
                    'Total Cost'     => number_format($totalCost, 2),
                    'Profit'         => number_format($totalSale - $totalCost, 2),
                ];
            }
        }

        if (empty($exportData)) {
            return back()->with('error', 'No records found for the selected filters.');
        }

        // ✅ Generate Excel download
        return Excel::download(new class($exportData) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
            protected $data;
            public function __construct($data) { $this->data = $data; }
            public function array(): array { return $this->data; }
            public function headings(): array { return array_keys($this->data[0]); }
        }, 'Daywise_Report_' . now()->format('Y_m_d_His') . '.xlsx');
    }



    /* =======================================================
       📆 2️⃣ MONTHLY SALES REPORT
    ======================================================= */
   public function monthly(Request $request): \Illuminate\View\View
    {
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfMonth()
            : Carbon::now()->startOfYear();

        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfMonth()
            : Carbon::now()->endOfYear();

        // 🧾 Fetch sell items (with product + size)
        $sellItems = SellItem::with(['product.category', 'size', 'sell'])
            ->whereHas('sell', fn($q) => $q->whereBetween('date', [$startDate, $endDate]))
            ->get();

        // 🧮 Load latest cost per product-size pair
        $latestCosts = StockItem::select('product_id', 'size_id', DB::raw('MAX(id) as max_id'))
            ->groupBy('product_id', 'size_id')
            ->pluck('max_id', 'product_id');

        $costMap = StockItem::whereIn('id', $latestCosts)
            ->get()
            ->mapWithKeys(fn($i) => [($i->product_id . '-' . ($i->size_id ?? 0)) => $i->cost_price]);

        // 📆 Group month-wise
        $monthly = $sellItems
            ->groupBy(fn($i) => Carbon::parse($i->sell->date)->format('Y-m'))
            ->map(function ($group, $month) use ($costMap) {
                $totalSales = 0;
                $totalCost = 0;

                // Compute cost and profit for each record
                $group->each(function ($item) use (&$totalSales, &$totalCost, $costMap) {
                    $key = $item->product_id . '-' . ($item->size_id ?? 0);
                    $item->latest_cost_price = $costMap[$key] ?? 0;

                    $qty = $item->qty ?? 0;
                    $sellPrice = $item->selling_price ?? 0;
                    $cost = $item->latest_cost_price ?? 0;

                    $totalSales += $qty * $sellPrice;
                    $totalCost += $qty * $cost;
                    $item->profit = ($qty * $sellPrice) - ($qty * $cost);
                });

                return (object) [
                    'month' => $month,
                    'total_sales' => $totalSales,
                    'total_cost' => $totalCost,
                    'profit' => $totalSales - $totalCost,
                    'items' => $group,
                ];
            })
            ->sortKeys()
            ->values();

        return view('reports.monthly', [
            'monthly' => $monthly,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    public function exportMonthlyPDF(Request $request)
    {
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfMonth()
            : Carbon::now()->startOfYear();

        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfMonth()
            : Carbon::now()->endOfYear();

        // 🧾 Fetch Sell Items within date range (with size)
        $sellItems = SellItem::with(['product.category', 'size', 'sell'])
            ->whereHas('sell', fn($q) => $q->whereBetween('date', [$startDate, $endDate]))
            ->get();

        // 🧮 Load latest cost prices per product-size pair once
        $latestCosts = StockItem::select('product_id', 'size_id', DB::raw('MAX(id) as max_id'))
            ->groupBy('product_id', 'size_id')
            ->pluck('max_id', 'product_id');

        $costMap = StockItem::whereIn('id', $latestCosts)
            ->get()
            ->mapWithKeys(fn($i) => [($i->product_id . '-' . ($i->size_id ?? 0)) => $i->cost_price]);

        // 🧮 Monthly grouping
        $monthly = $sellItems
            ->groupBy(fn($i) => Carbon::parse($i->sell->date)->format('Y-m'))
            ->map(function ($group, $month) use ($costMap) {
                $totalSales = 0;
                $totalCost = 0;

                $group->each(function ($item) use (&$totalSales, &$totalCost, $costMap) {
                    $key = $item->product_id . '-' . ($item->size_id ?? 0);
                    $item->latest_cost_price = $costMap[$key] ?? 0;

                    $qty = $item->qty ?? 0;
                    $sell = $item->selling_price ?? 0;
                    $cost = $item->latest_cost_price ?? 0;

                    $totalSales += $qty * $sell;
                    $totalCost += $qty * $cost;
                    $item->profit = ($qty * $sell) - ($qty * $cost);
                });

                return (object)[
                    'month' => $month,
                    'total_sales' => $totalSales,
                    'total_cost' => $totalCost,
                    'profit' => $totalSales - $totalCost,
                    'items' => $group,
                ];
            })
            ->sortKeys()
            ->values();

        // 🧾 Generate PDF (size-wise layout)
        $pdf = Pdf::loadView('reports.pdf.monthly', [
            'monthly' => $monthly,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ])->setPaper('A4', 'portrait');

        $fileName = 'Monthly_Report_' . $startDate->format('Y_m') . '_to_' . $endDate->format('Y_m') . '.pdf';
        return $pdf->download($fileName);
    }

    public function exportMonthlyExcel(Request $request)
    {
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfMonth()
            : Carbon::now()->startOfYear();

        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfMonth()
            : Carbon::now()->endOfYear();

        // 🧾 Fetch Sell Items within range
        $sellItems = SellItem::with(['product.category', 'size', 'sell'])
            ->whereHas('sell', fn($q) => $q->whereBetween('date', [$startDate, $endDate]))
            ->get();

        // 🧮 Load cost map once
        $latestCosts = StockItem::select('product_id', 'size_id', DB::raw('MAX(id) as max_id'))
            ->groupBy('product_id', 'size_id')
            ->pluck('max_id', 'product_id');

        $costMap = StockItem::whereIn('id', $latestCosts)
            ->get()
            ->mapWithKeys(fn($i) => [($i->product_id . '-' . ($i->size_id ?? 0)) => $i->cost_price]);

        // 🧩 Group month-wise
        $monthly = $sellItems
            ->groupBy(fn($i) => Carbon::parse($i->sell->date)->format('Y-m'))
            ->map(function ($group, $month) use ($costMap) {
                $rows = [];
                $totalSales = 0;
                $totalCost = 0;

                foreach ($group as $item) {
                    $key = $item->product_id . '-' . ($item->size_id ?? 0);
                    $latestCost = $costMap[$key] ?? 0;

                    $qty = $item->qty ?? 0;
                    $selling = $item->selling_price ?? 0;

                    $totalSale = $qty * $selling;
                    $totalCostItem = $qty * $latestCost;
                    $profit = $totalSale - $totalCostItem;

                    $rows[] = [
                        'Month'         => Carbon::parse($item->sell->date)->format('F Y'),
                        'Category'      => $item->product->category->name ?? '-',
                        'Product'       => $item->product->name ?? '-',
                        'Size'          => $item->size->label ?? '-',
                        'Quantity Sold' => $qty,
                        'Selling Price' => number_format($selling, 2),
                        'Cost Price'    => number_format($latestCost, 2),
                        'Total Sales'   => number_format($totalSale, 2),
                        'Total Cost'    => number_format($totalCostItem, 2),
                        'Profit'        => number_format($profit, 2),
                    ];

                    $totalSales += $totalSale;
                    $totalCost += $totalCostItem;
                }

                // Add subtotal row
                $rows[] = [
                    'Month' => 'Subtotal - ' . Carbon::parse($month . '-01')->format('F Y'),
                    'Category' => '',
                    'Product' => '',
                    'Size' => '',
                    'Quantity Sold' => '',
                    'Selling Price' => '',
                    'Cost Price' => '',
                    'Total Sales' => number_format($totalSales, 2),
                    'Total Cost' => number_format($totalCost, 2),
                    'Profit' => number_format($totalSales - $totalCost, 2),
                ];

                return $rows;
            })
            ->flatten(1);

        if ($monthly->isEmpty()) {
            return back()->with('error', 'No records found for the selected filters.');
        }

        // ✅ Export to Excel
        $filename = 'Monthly_Report_' . $startDate->format('Y_m') . '_to_' . $endDate->format('Y_m') . '.xlsx';

        return Excel::download(new class($monthly) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
            private $data;
            public function __construct($data) { $this->data = $data; }
            public function collection() { return collect($this->data); }
            public function headings(): array {
                return ['Month', 'Category', 'Product', 'Size', 'Quantity Sold', 'Selling Price', 'Cost Price', 'Total Sales', 'Total Cost', 'Profit'];
            }
        }, $filename);
    }



    /* =======================================================
       📦 3️⃣ PRODUCT-WISE SALES REPORT
    ======================================================= */
   public function productwise(Request $request)
    {
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : Carbon::now()->startOfMonth();

        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : Carbon::now()->endOfMonth();

        $categoryId = $request->input('category_id');
        $productId = $request->input('product_id');

        // 🔍 Base query
        $query = SellItem::with(['product.category', 'size', 'sell'])
            ->whereHas('sell', fn($q) => $q->whereBetween('date', [$startDate, $endDate]));

        if ($categoryId) {
            $query->whereHas('product', fn($q) => $q->where('category_id', $categoryId));
        }

        if ($productId) {
            $query->where('product_id', $productId);
        }

        $sellItems = $query->get();

        // ⚙️ Preload latest cost prices (avoid per-row queries)
        $latestCosts = StockItem::select('product_id', 'size_id', DB::raw('MAX(id) as max_id'))
            ->groupBy('product_id', 'size_id')
            ->pluck('max_id', 'product_id');

        $costMap = StockItem::whereIn('id', $latestCosts)
            ->get()
            ->mapWithKeys(fn($i) => [($i->product_id . '-' . ($i->size_id ?? 0)) => $i->cost_price]);

        // 🧮 Group by product + size
        $productWise = $sellItems
            ->groupBy(fn($i) => $i->product_id . '-' . ($i->size_id ?? 0))
            ->map(function ($group) use ($costMap) {
                $item = $group->first();
                $key = $item->product_id . '-' . ($item->size_id ?? 0);

                $totalQty = $group->sum('qty');
                $totalSales = $group->sum(fn($i) => $i->qty * ($i->selling_price ?? 0));

                $latestCost = $costMap[$key] ?? 0;
                $totalCost = $totalQty * $latestCost;
                $profit = $totalSales - $totalCost;

                return [
                    'product_name' => $item->product->name ?? '-',
                    'category' => $item->product->category->name ?? '-',
                    'size' => $item->size->label ?? '-',
                    'total_qty' => $totalQty,
                    'total_sales' => $totalSales,
                    'total_cost' => $totalCost,
                    'profit' => $profit,
                ];
            });

        return view('reports.productwise', [
            'productWise' => $productWise,
            'categories' => Category::all(),
            'products' => Product::all(),
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }
    public function exportProductwisePDF(Request $request)
    {
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : Carbon::now()->startOfMonth();

        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : Carbon::now()->endOfMonth();

        $categoryId = $request->input('category_id');
        $productId = $request->input('product_id');

        $query = SellItem::with(['product.category', 'size', 'sell'])
            ->whereHas('sell', fn($q) => $q->whereBetween('date', [$startDate, $endDate]));

        if ($categoryId) {
            $query->whereHas('product', fn($q) => $q->where('category_id', $categoryId));
        }

        if ($productId) {
            $query->where('product_id', $productId);
        }

        $sellItems = $query->get();

        // Preload latest costs once
        $latestCosts = StockItem::select('product_id', 'size_id', DB::raw('MAX(id) as max_id'))
            ->groupBy('product_id', 'size_id')
            ->pluck('max_id', 'product_id');

        $costMap = StockItem::whereIn('id', $latestCosts)
            ->get()
            ->mapWithKeys(fn($i) => [($i->product_id . '-' . ($i->size_id ?? 0)) => $i->cost_price]);

        $productWise = $sellItems
            ->groupBy(fn($i) => $i->product_id . '-' . ($i->size_id ?? 0))
            ->map(function ($group) use ($costMap) {
                $item = $group->first();
                $key = $item->product_id . '-' . ($item->size_id ?? 0);

                $totalQty = $group->sum('qty');
                $totalSales = $group->sum(fn($i) => $i->qty * ($i->selling_price ?? 0));

                $latestCost = $costMap[$key] ?? 0;
                $totalCost = $totalQty * $latestCost;
                $profit = $totalSales - $totalCost;

                return [
                    'product_name' => $item->product->name ?? '-',
                    'category' => $item->product->category->name ?? '-',
                    'size' => $item->size->label ?? '-',
                    'total_qty' => $totalQty,
                    'total_sales' => $totalSales,
                    'total_cost' => $totalCost,
                    'profit' => $profit,
                ];
            });

        $data = [
            'productWise' => $productWise,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'category' => $categoryId ? Category::find($categoryId)?->name : 'All',
            'product' => $productId ? Product::find($productId)?->name : 'All',
        ];

        $pdf = Pdf::loadView('reports.pdf.productwise', $data)
            ->setPaper('A4', 'portrait');

        $filename = 'Productwise_Report_' . $startDate->format('Y_m_d') . '_to_' . $endDate->format('Y_m_d') . '.pdf';
        return $pdf->download($filename);
    }
    public function exportProductwiseExcel(Request $request)
    {
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : Carbon::now()->startOfMonth();

        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : Carbon::now()->endOfMonth();

        $categoryId = $request->input('category_id');
        $productId = $request->input('product_id');

        $query = SellItem::with(['product.category', 'size', 'sell'])
            ->whereHas('sell', fn($q) => $q->whereBetween('date', [$startDate, $endDate]));

        if ($categoryId) {
            $query->whereHas('product', fn($q) => $q->where('category_id', $categoryId));
        }

        if ($productId) {
            $query->where('product_id', $productId);
        }

        $sellItems = $query->get();

        // Preload cost map
        $latestCosts = StockItem::select('product_id', 'size_id', DB::raw('MAX(id) as max_id'))
            ->groupBy('product_id', 'size_id')
            ->pluck('max_id', 'product_id');

        $costMap = StockItem::whereIn('id', $latestCosts)
            ->get()
            ->mapWithKeys(fn($i) => [($i->product_id . '-' . ($i->size_id ?? 0)) => $i->cost_price]);

        $productWise = $sellItems
            ->groupBy(fn($i) => $i->product_id . '-' . ($i->size_id ?? 0))
            ->map(function ($group) use ($costMap) {
                $item = $group->first();
                $key = $item->product_id . '-' . ($item->size_id ?? 0);

                $totalQty = $group->sum('qty');
                $totalSales = $group->sum(fn($i) => $i->qty * ($i->selling_price ?? 0));

                $latestCost = $costMap[$key] ?? 0;
                $totalCost = $totalQty * $latestCost;
                $profit = $totalSales - $totalCost;

                return [
                    'Product' => $item->product->name ?? '-',
                    'Category' => $item->product->category->name ?? '-',
                    'Size' => $item->size->label ?? '-',
                    'Quantity Sold' => $totalQty,
                    'Total Sales (Rs)' => $totalSales,
                    'Total Cost (Rs)' => $totalCost,
                    'Profit (Rs)' => $profit,
                ];
            })
            ->values();

        $filename = 'Productwise_Report_' . $startDate->format('Y_m_d') . '_to_' . $endDate->format('Y_m_d') . '.xlsx';

        return Excel::download(new class($productWise) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
            protected $data;
            public function __construct($data) { $this->data = $data; }
            public function collection() { return new Collection($this->data); }
            public function headings(): array {
                return ['Product', 'Category', 'Size', 'Quantity Sold', 'Total Sales (Rs)', 'Total Cost (Rs)', 'Profit (Rs)'];
            }
        }, $filename);
    }



    /* =======================================================
       📊 4️⃣ STOCK SUMMARY REPORT
    ======================================================= */
   public function stocksummary(Request $request)
    {
        $categories = Category::all();

        // 🧭 Base query with category and size
        $query = Product::with(['category', 'sizes']);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->orderBy('name')->get();

        // 🧮 Flatten product-size data
        $stock = $products->flatMap(function ($product) {
            if ($product->sizes->isEmpty()) {
                return [[
                    'product_name' => $product->name,
                    'category' => $product->category->name ?? '-',
                    'size' => '-',
                    'qty' => $product->qty ?? 0,
                    'selling_price' => $product->selling_price ?? 0,
                ]];
            }

            return $product->sizes->map(function ($size) use ($product) {
                return [
                    'product_name' => $product->name,
                    'category' => $product->category->name ?? '-',
                    'size' => $size->label ?? '-',
                    'qty' => $size->pivot->qty ?? 0,
                    'selling_price' => $size->pivot->selling_price ?? $product->selling_price ?? 0,
                ];
            });
        });

        return view('reports.stocksummary', compact('categories', 'stock'));
    }

    public function exportStockPDF(Request $request)
    {
        $query = Product::with(['category', 'sizes']);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->orderBy('name')->get();

        $categoryName = $request->filled('category_id')
            ? Category::find($request->category_id)?->name
            : 'All';

        $stock = $products->flatMap(function ($product) {
            if ($product->sizes->isEmpty()) {
                return [[
                    'product_name' => $product->name,
                    'category' => $product->category->name ?? '-',
                    'size' => '-',
                    'qty' => $product->qty ?? 0,
                    'selling_price' => $product->selling_price ?? 0,
                ]];
            }

            return $product->sizes->map(function ($size) use ($product) {
                return [
                    'product_name' => $product->name,
                    'category' => $product->category->name ?? '-',
                    'size' => $size->label ?? '-',
                    'qty' => $size->pivot->qty ?? 0,
                    'selling_price' => $size->pivot->selling_price ?? $product->selling_price ?? 0,
                ];
            });
        });

        $pdf = Pdf::loadView('reports.pdf.stocksummary', [
            'stock' => $stock,
            'categoryName' => $categoryName,
        ])->setPaper('A4', 'portrait');

        $filename = 'Stock_Summary_Report_' . now()->format('Y_m_d_His') . '.pdf';
        return $pdf->download($filename);
    }

    public function exportStockExcel(Request $request)
    {
        $query = Product::with(['category', 'sizes']);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->orderBy('name')->get();

        $data = $products->flatMap(function ($product) {
            if ($product->sizes->isEmpty()) {
                return [[
                    'Product' => $product->name,
                    'Category' => $product->category->name ?? '-',
                    'Size' => '-',
                    'Available Qty' => $product->qty ?? 0,
                    'Selling Price (Rs)' => $product->selling_price ?? 0,
                ]];
            }

            return $product->sizes->map(function ($size) use ($product) {
                return [
                    'Product' => $product->name,
                    'Category' => $product->category->name ?? '-',
                    'Size' => $size->label ?? '-',
                    'Available Qty' => $size->pivot->qty ?? 0,
                    'Selling Price (Rs)' => $size->pivot->selling_price ?? $product->selling_price ?? 0,
                ];
            });
        });

        $filename = 'Stock_Summary_Report_' . now()->format('Y_m_d_His') . '.xlsx';

        return Excel::download(new class($data) implements 
            \Maatwebsite\Excel\Concerns\FromCollection, 
            \Maatwebsite\Excel\Concerns\WithHeadings 
        {
            protected $data;
            public function __construct($data) { $this->data = $data; }
            public function collection() { return new Collection($this->data); }
            public function headings(): array {
                return ['Product', 'Category', 'Size', 'Available Qty', 'Selling Price (Rs)'];
            }
        }, $filename);
    }




    /* =======================================================
       🧾 5️⃣ STOCK ADDED REPORT
    ======================================================= */
    public function stockadded(Request $request): View
    {
        [$startDate, $endDate, $categoryId, $productId] = $this->getFilters($request);

        // 🔍 Fetch stock items with relations
        $query = StockItem::with(['product.category', 'size', 'stock'])
            ->whereHas('stock', fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]));

        if ($categoryId) {
            $query->whereHas('product', fn($q) => $q->where('category_id', $categoryId));
        }

        if ($productId) {
            $query->where('product_id', $productId);
        }

        $stockItems = $query->orderByDesc('id')->get();

        return view('reports.stockadded', [
            'stockItems' => $stockItems,
            'categories' => Category::all(),
            'products' => Product::all(),
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    public function exportStockaddedPDF(Request $request)
    {
        [$startDate, $endDate, $categoryId, $productId] = $this->getFilters($request);

        $query = StockItem::with(['product.category', 'size', 'stock'])
            ->whereHas('stock', fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]));

        if ($categoryId) $query->whereHas('product', fn($q) => $q->where('category_id', $categoryId));
        if ($productId) $query->where('product_id', $productId);

        $stockItems = $query->get();
        $totalCost = $stockItems->sum('total');

        $pdf = Pdf::loadView('reports.pdf.stockadded', [
            'stockItems' => $stockItems,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalCost' => $totalCost,
            'category' => $categoryId ? Category::find($categoryId)?->name : 'All',
            'product' => $productId ? Product::find($productId)?->name : 'All',
        ])->setPaper('A4', 'portrait');

        $filename = 'Stock_Added_Report_' . $startDate->format('Y_m_d') . '_to_' . $endDate->format('Y_m_d') . '.pdf';
        return $pdf->download($filename);
    }

    public function exportStockaddedExcel(Request $request)
    {
        [$startDate, $endDate, $categoryId, $productId] = $this->getFilters($request);

        $query = StockItem::with(['product.category', 'size', 'stock'])
            ->whereHas('stock', fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]));

        if ($categoryId) $query->whereHas('product', fn($q) => $q->where('category_id', $categoryId));
        if ($productId) $query->where('product_id', $productId);

        $stockItems = $query->get()->map(function ($item) {
            return [
                'Date' => $item->stock->created_at->format('Y-m-d'),
                'Product' => $item->product->name ?? '-',
                'Category' => $item->product->category->name ?? '-',
                'Size' => $item->size->label ?? '-',
                'Qty Added' => $item->qty,
                'Cost Price (Rs)' => $item->cost_price,
                'Total Cost (Rs)' => $item->total,
            ];
        });

        $filename = 'Stock_Added_Report_' . now()->format('Y_m_d_His') . '.xlsx';

        return Excel::download(new class($stockItems) implements 
            \Maatwebsite\Excel\Concerns\FromCollection, 
            \Maatwebsite\Excel\Concerns\WithHeadings 
        {
            protected $data;
            public function __construct($data) { $this->data = $data; }
            public function collection() { return new Collection($this->data); }
            public function headings(): array {
                return ['Date', 'Product', 'Category', 'Size', 'Qty Added', 'Cost Price (Rs)', 'Total Cost (Rs)'];
            }
        }, $filename);
    }



    /* =======================================================
       🍾 6️⃣ EMPTY BOTTLE REPORT
    ======================================================= */
    public function bottleReport(Request $request): View
    {
        $startDate = $request->get('start_date') ?? now()->startOfMonth()->toDateString();
        $endDate = $request->get('end_date') ?? now()->endOfMonth()->toDateString();

        $bottles = Bottle::whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get();

        $totalValue = $bottles->sum('total_price');

        return view('reports.bottles', compact('bottles', 'startDate', 'endDate', 'totalValue'));
    }

    public function exportBottlePDF(Request $request)
    {
        $startDate = $request->get('start_date') ?? now()->startOfMonth()->toDateString();
        $endDate = $request->get('end_date') ?? now()->endOfMonth()->toDateString();

        $bottles = Bottle::whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get();

        $totalValue = $bottles->sum('total_price');

        $pdf = Pdf::loadView('reports.pdf.bottles', compact('bottles', 'startDate', 'endDate', 'totalValue'));
        return $pdf->download('empty_bottle_report.pdf');
    }

    public function exportBottleExcel(Request $request)
    {
        $startDate = $request->get('start_date') ?? now()->startOfMonth()->toDateString();
        $endDate = $request->get('end_date') ?? now()->endOfMonth()->toDateString();

        $bottles = Bottle::whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get()
            ->map(function($b) {
                return [
                    'Date' => $b->date,
                    'Quantity' => $b->quantity,
                    'Price per Bottle' => $b->price_per_bottle,
                    'Total Price' => $b->total_price,
                ];
            });

        return Excel::download(new GenericExport($bottles->toArray()), 'empty_bottle_report.xlsx');
    }
    /* =======================================================
       📤 EXPORT HELPERS (for PDF / Excel)
    ======================================================= */
    private function getFilters(Request $request)
    {
        $filter = $request->input('quick_filter');
        if ($filter === 'today') {
            $startDate = Carbon::today();
            $endDate = Carbon::today()->endOfDay();
        } elseif ($filter === 'week') {
            $startDate = Carbon::now()->startOfWeek();
            $endDate = Carbon::now()->endOfWeek();
        } else {
            $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
            $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfMonth();
        }
        $categoryId = $request->input('category_id');
        $productId = $request->input('product_id');

        return [$startDate, $endDate, $categoryId, $productId];
    }
}
