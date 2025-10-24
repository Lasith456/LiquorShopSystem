<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Product;
use App\Models\Category;
use App\Models\Sell;
use App\Models\SellItem;
use App\Models\StockItem;
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

        // ✅ Preload latest cost prices to avoid multiple DB queries
        $latestCosts = \App\Models\StockItem::select('product_id', \DB::raw('MAX(id) as max_id'))
            ->groupBy('product_id')
            ->pluck('max_id', 'product_id');

        $costMap = \App\Models\StockItem::whereIn('id', $latestCosts)
            ->pluck('cost_price', 'product_id');

        // ✅ Fetch sell items with filters
        $query = \App\Models\SellItem::with(['product.category', 'sell'])
            ->whereHas('sell', fn($q) => $q->whereBetween('date', [$startDate, $endDate]));

        if ($categoryId) {
            $query->whereHas('product', fn($q) => $q->where('category_id', $categoryId));
        }

        if ($productId) {
            $query->where('product_id', $productId);
        }

        $sellItems = $query->get();

        // ✅ Group day-wise with totals
        $dayWise = $sellItems->groupBy(fn($i) => \Carbon\Carbon::parse($i->sell->date)->format('Y-m-d'))
            ->map(function ($group, $day) use ($costMap) {
                $totalSales = $group->sum(fn($i) => $i->qty * ($i->selling_price ?? 0));
                $totalCost = $group->sum(function ($i) use ($costMap) {
                    $cost = $costMap[$i->product_id] ?? 0;
                    return $i->qty * $cost;
                });
                $profit = $totalSales - $totalCost;

                // attach latest cost for display
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

        // ✅ MUST RETURN VIEW
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
    public function monthly(Request $request): View
    {
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfMonth()
            : Carbon::now()->startOfYear();

        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfMonth()
            : Carbon::now()->endOfYear();

        // 🧾 Fetch Sell Items within the range
        $sellItems = SellItem::with(['product', 'sell'])
            ->whereHas('sell', fn($q) => $q->whereBetween('date', [$startDate, $endDate]))
            ->get();

        // 🧮 Group data month-wise
        $monthly = $sellItems
            ->groupBy(fn($i) => Carbon::parse($i->sell->date)->format('Y-m'))
            ->map(function ($group) {
                $totalSales = 0;
                $totalCost = 0;

                foreach ($group as $item) {
                    $qty = $item->qty ?? 0;
                    $sellingPrice = $item->selling_price ?? 0;

                    // ✅ Get latest cost price from stock_items table
                    $latestCost = StockItem::where('product_id', $item->product_id)
                        ->orderByDesc('id')
                        ->value('cost_price') ?? 0;

                    $totalSales += $qty * $sellingPrice;
                    $totalCost += $qty * $latestCost;
                }

                return [
                    'month' => Carbon::parse($group->first()->sell->date)->format('Y-m'),
                    'total_sales' => $totalSales,
                    'total_cost' => $totalCost,
                    'profit' => $totalSales - $totalCost,
                ];
            })
            ->sortKeys();

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

        // 🧾 Fetch Sell Items within date range
        $sellItems = SellItem::with(['product', 'sell'])
            ->whereHas('sell', fn($q) => $q->whereBetween('date', [$startDate, $endDate]))
            ->get();

        // 🧮 Monthly group with cost, sales & profit
        $monthly = $sellItems
            ->groupBy(fn($i) => Carbon::parse($i->sell->date)->format('Y-m'))
            ->map(function ($group) {
                $totalSales = 0;
                $totalCost = 0;

                foreach ($group as $item) {
                    $qty = $item->qty ?? 0;
                    $sellingPrice = $item->selling_price ?? 0;

                    $latestCost = StockItem::where('product_id', $item->product_id)
                        ->orderByDesc('id')
                        ->value('cost_price') ?? 0;

                    $totalSales += $qty * $sellingPrice;
                    $totalCost += $qty * $latestCost;
                }

                return [
                    'month' => Carbon::parse($group->first()->sell->date)->format('Y-m'),
                    'total_sales' => $totalSales,
                    'total_cost' => $totalCost,
                    'profit' => $totalSales - $totalCost,
                ];
            })
            ->sortKeys();

        // 🧾 Load the PDF view
        $pdf = Pdf::loadView('reports.pdf.monthly', [
            'monthly' => $monthly,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ])->setPaper('A4', 'portrait');

        $filename = 'Monthly_Report_' . $startDate->format('Y_m') . '_to_' . $endDate->format('Y_m') . '.pdf';

        return $pdf->download($filename);
    }
    public function exportMonthlyExcel(Request $request)
    {
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfMonth()
            : Carbon::now()->startOfYear();

        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfMonth()
            : Carbon::now()->endOfYear();

        // 🧾 Fetch Sell Items within date range
        $sellItems = SellItem::with(['product', 'sell'])
            ->whereHas('sell', fn($q) => $q->whereBetween('date', [$startDate, $endDate]))
            ->get();

        // 🧮 Monthly group with cost, sales & profit
        $monthly = $sellItems
            ->groupBy(fn($i) => Carbon::parse($i->sell->date)->format('Y-m'))
            ->map(function ($group) {
                $totalSales = 0;
                $totalCost = 0;

                foreach ($group as $item) {
                    $qty = $item->qty ?? 0;
                    $sellingPrice = $item->selling_price ?? 0;

                    $latestCost = StockItem::where('product_id', $item->product_id)
                        ->orderByDesc('id')
                        ->value('cost_price') ?? 0;

                    $totalSales += $qty * $sellingPrice;
                    $totalCost += $qty * $latestCost;
                }

                return [
                    'month' => Carbon::parse($group->first()->sell->date)->format('F Y'),
                    'total_sales' => $totalSales,
                    'total_cost' => $totalCost,
                    'profit' => $totalSales - $totalCost,
                ];
            })
            ->sortKeys();

        // ✅ Convert to Collection for Excel export
        $exportData = new Collection([
            ['Month', 'Total Sales (Rs)', 'Total Cost (Rs)', 'Profit (Rs)'],
        ]);

        foreach ($monthly as $row) {
            $exportData->push([
                $row['month'],
                number_format($row['total_sales'], 2),
                number_format($row['total_cost'], 2),
                number_format($row['profit'], 2),
            ]);
        }

        // Add grand totals
        $exportData->push([
            'TOTAL',
            number_format($monthly->sum('total_sales'), 2),
            number_format($monthly->sum('total_cost'), 2),
            number_format($monthly->sum('profit'), 2),
        ]);

        // ✅ Return Excel download
        $filename = 'Monthly_Report_' . $startDate->format('Y_m') . '_to_' . $endDate->format('Y_m') . '.xlsx';
        return Excel::download(new class($exportData) implements \Maatwebsite\Excel\Concerns\FromCollection {
            private $data;
            public function __construct($data) { $this->data = $data; }
            public function collection() { return $this->data; }
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

        $query = SellItem::with(['product.category', 'sell'])
            ->whereHas('sell', fn($q) => $q->whereBetween('date', [$startDate, $endDate]));

        if ($categoryId) {
            $query->whereHas('product', fn($q) => $q->where('category_id', $categoryId));
        }

        if ($productId) {
            $query->where('product_id', $productId);
        }

        $sellItems = $query->get();

        $productWise = $sellItems
            ->groupBy('product_id')
            ->map(function ($group) {
                $product = $group->first()->product;
                $totalQty = $group->sum('qty');
                $totalSales = $group->sum(fn($i) => $i->qty * ($i->selling_price ?? 0));

                $latestCost = StockItem::where('product_id', $product->id)
                    ->orderByDesc('id')
                    ->value('cost_price') ?? 0;

                $totalCost = $totalQty * $latestCost;
                $profit = $totalSales - $totalCost;

                return [
                    'product_name' => $product->name,
                    'category' => $product->category->name ?? 'N/A',
                    'total_qty' => $totalQty,
                    'total_sales' => $totalSales,
                    'total_cost' => $totalCost,
                    'profit' => $profit,
                ];
            });

        return view('reports.productwise', [
            'productWise' => $productWise,
            'categories' => Category::all(),
            'products' => Product::all(), // ✅ FIX: Add this line
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

        $query = SellItem::with(['product.category', 'sell'])
            ->whereHas('sell', fn($q) => $q->whereBetween('date', [$startDate, $endDate]));

        if ($categoryId) {
            $query->whereHas('product', fn($q) => $q->where('category_id', $categoryId));
        }

        if ($productId) {
            $query->where('product_id', $productId);
        }

        $sellItems = $query->get();

        // 🧮 Group by product & calculate totals
        $productWise = $sellItems
            ->groupBy('product_id')
            ->map(function ($group) {
                $product = $group->first()->product;
                $totalQty = $group->sum('qty');
                $totalSales = $group->sum(fn($i) => $i->qty * ($i->selling_price ?? 0));

                $latestCost = StockItem::where('product_id', $product->id)
                    ->orderByDesc('id')
                    ->value('cost_price') ?? 0;

                $totalCost = $totalQty * $latestCost;
                $profit = $totalSales - $totalCost;

                return [
                    'product_name' => $product->name,
                    'category' => $product->category->name ?? 'N/A',
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

        // 🧾 Load view and generate PDF
        $pdf = PDF::loadView('reports.pdf.productwise', $data)
            ->setPaper('a4', 'portrait');

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

        $query = SellItem::with(['product.category', 'sell'])
            ->whereHas('sell', fn($q) => $q->whereBetween('date', [$startDate, $endDate]));

        if ($categoryId) {
            $query->whereHas('product', fn($q) => $q->where('category_id', $categoryId));
        }

        if ($productId) {
            $query->where('product_id', $productId);
        }

        $sellItems = $query->get();

        // 🧮 Aggregate by product
        $productWise = $sellItems
            ->groupBy('product_id')
            ->map(function ($group) {
                $product = $group->first()->product;
                $totalQty = $group->sum('qty');
                $totalSales = $group->sum(fn($i) => $i->qty * ($i->selling_price ?? 0));

                $latestCost = StockItem::where('product_id', $product->id)
                    ->orderByDesc('id')
                    ->value('cost_price') ?? 0;

                $totalCost = $totalQty * $latestCost;
                $profit = $totalSales - $totalCost;

                return [
                    'Product' => $product->name,
                    'Category' => $product->category->name ?? 'N/A',
                    'Quantity Sold' => $totalQty,
                    'Total Sales (Rs)' => $totalSales,
                    'Total Cost (Rs)' => $totalCost,
                    'Profit (Rs)' => $profit,
                ];
            })
            ->values();

        // 🧾 Export file
        $filename = 'Productwise_Report_' . $startDate->format('Y_m_d') . '_to_' . $endDate->format('Y_m_d') . '.xlsx';

        return Excel::download(new class($productWise) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
            protected $data;
            public function __construct($data) { $this->data = $data; }
            public function collection() { return new Collection($this->data); }
            public function headings(): array {
                return ['Product', 'Category', 'Quantity Sold', 'Total Sales (Rs)', 'Total Cost (Rs)', 'Profit (Rs)'];
            }
        }, $filename);
    }



    /* =======================================================
       📊 4️⃣ STOCK SUMMARY REPORT
    ======================================================= */
    public function stocksummary(Request $request)
    {
        $categories = Category::all();

        $query = Product::with('category');

        // 🧭 Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $stock = $query->orderBy('name')->get();

        return view('reports.stocksummary', compact('categories', 'stock'));
    }
    public function exportStockPDF(Request $request)
    {
        $categories = Category::all();

        $query = Product::with('category');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $stock = $query->orderBy('name')->get();

        $categoryName = $request->filled('category_id')
            ? Category::find($request->category_id)?->name
            : 'All';

        $pdf = PDF::loadView('reports.pdf.stocksummary', [
            'stock' => $stock,
            'categoryName' => $categoryName,
        ])->setPaper('a4', 'portrait');

        $filename = 'Stock_Summary_Report_' . now()->format('Y_m_d_His') . '.pdf';
        return $pdf->download($filename);
    }
    public function exportStockExcel(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $stock = $query->orderBy('name')->get();

        $data = $stock->map(function ($p) {
            return [
                'Product' => $p->name,
                'Category' => $p->category->name ?? '-',
                'Available Qty' => $p->qty,
                'Selling Price (Rs)' => $p->selling_price,
            ];
        });

        $filename = 'Stock_Summary_Report_' . now()->format('Y_m_d_His') . '.xlsx';

        return Excel::download(new class($data) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
            protected $data;
            public function __construct($data) { $this->data = $data; }
            public function collection() { return new Collection($this->data); }
            public function headings(): array {
                return ['Product', 'Category', 'Available Qty', 'Selling Price (Rs)'];
            }
        }, $filename);
    }




    /* =======================================================
       🧾 5️⃣ STOCK ADDED REPORT
    ======================================================= */
    public function stockadded(Request $request): View
    {
        [$startDate, $endDate, $categoryId, $productId] = $this->getFilters($request);

        $query = StockItem::with(['product.category', 'stock' => fn($q) => $q->whereBetween('created_at', [$startDate, $endDate])]);
        if ($categoryId) $query->whereHas('product', fn($q) => $q->where('category_id', $categoryId));
        if ($productId) $query->where('product_id', $productId);

        $stockItems = $query->get();

        return view('reports.stockadded', [
            'stockItems' => $stockItems,
            'categories' => Category::all(),
            'products' => Product::all(),
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }
    public function exportStockaddedPDF(Request $request)
    {
        [$startDate, $endDate, $categoryId, $productId] = $this->getFilters($request);

        $query = StockItem::with(['product.category', 'stock' => fn($q) => $q->whereBetween('created_at', [$startDate, $endDate])]);
        if ($categoryId) $query->whereHas('product', fn($q) => $q->where('category_id', $categoryId));
        if ($productId) $query->where('product_id', $productId);

        $stockItems = $query->get();
        $totalCost = $stockItems->sum('total');

        $pdf = Pdf::loadView('reports.pdf.stockadded', [
            'stockItems' => $stockItems,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalCost' => $totalCost,
        ]);

        return $pdf->download('stock_added_report.pdf');
    }

    public function exportStockaddedExcel(Request $request)
    {
        [$startDate, $endDate, $categoryId, $productId] = $this->getFilters($request);

        $query = StockItem::with(['product.category', 'stock' => fn($q) => $q->whereBetween('created_at', [$startDate, $endDate])]);
        if ($categoryId) $query->whereHas('product', fn($q) => $q->where('category_id', $categoryId));
        if ($productId) $query->where('product_id', $productId);

        $stockItems = $query->get()->map(function($item){
            return [
                'Date' => $item->stock->created_at->format('Y-m-d'),
                'Product' => $item->product->name ?? '-',
                'Category' => $item->product->category->name ?? '-',
                'Qty Added' => $item->qty,
                'Cost Price' => $item->cost_price,
                'Total Cost' => $item->total,
            ];
        });

        return Excel::download(new GenericExport($stockItems->toArray()), 'stock_added_report.xlsx');
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
