<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Monthly Sales, Cost & Profit Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; margin: 20px; }
        h2, h3, p { text-align: center; margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #444; padding: 6px; }
        th { background-color: #2c3e50; color: #fff; }
        td { text-align: right; }
        td:first-child { text-align: left; }
        .month-header { background: #f2f2f2; font-weight: bold; text-align: left; }
        tfoot td { font-weight: bold; background-color: #f3f3f3; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .summary-row { background-color: #eef3fc; font-weight: bold; }
    </style>
</head>
<body>
    <h2>Monthly Sales, Cost & Profit Report</h2>
    <p>
        <strong>Period:</strong> {{ $startDate->format('F Y') }} – {{ $endDate->format('F Y') }}
    </p>

    @php
        $grandSales = 0;
        $grandCost = 0;
        $grandProfit = 0;
    @endphp

    @foreach ($monthly as $month)
        <h3>{{ \Carbon\Carbon::parse($month->month . '-01')->format('F Y') }}</h3>

        <table>
            <thead>
                <tr>
                    <th>Product (Size)</th>
                    <th>Qty</th>
                    <th>Selling Price (Rs)</th>
                    <th>Cost Price (Rs)</th>
                    <th>Total Sales (Rs)</th>
                    <th>Total Cost (Rs)</th>
                    <th>Profit (Rs)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($month->items as $item)
                    @php
                        $qty = $item->qty ?? 0;
                        $sell = $item->selling_price ?? 0;
                        $cost = $item->latest_cost_price ?? 0;
                        $totalSales = $qty * $sell;
                        $totalCost = $qty * $cost;
                        $profit = $totalSales - $totalCost;
                    @endphp
                    <tr>
                        <td class="text-left">
                            {{ $item->product->name ?? '-' }}
                            <small>({{ $item->size->label ?? '-' }})</small>
                        </td>
                        <td>{{ $qty }}</td>
                        <td>{{ number_format($sell, 2) }}</td>
                        <td>{{ number_format($cost, 2) }}</td>
                        <td>{{ number_format($totalSales, 2) }}</td>
                        <td>{{ number_format($totalCost, 2) }}</td>
                        <td>{{ number_format($profit, 2) }}</td>
                    </tr>
                @endforeach
                {{-- Monthly total --}}
                <tr class="summary-row">
                    <td class="text-right">Total for {{ \Carbon\Carbon::parse($month->month . '-01')->format('F Y') }}</td>
                    <td>—</td>
                    <td>—</td>
                    <td>—</td>
                    <td>{{ number_format($month->total_sales, 2) }}</td>
                    <td>{{ number_format($month->total_cost, 2) }}</td>
                    <td>{{ number_format($month->profit, 2) }}</td>
                </tr>
            </tbody>
        </table>

        @php
            $grandSales += $month->total_sales;
            $grandCost += $month->total_cost;
            $grandProfit += $month->profit;
        @endphp
    @endforeach

    {{-- Grand Total Section --}}
    <h3 style="margin-top: 20px;">Grand Total Summary</h3>
    <table>
        <thead>
            <tr>
                <th>Total Sales (Rs)</th>
                <th>Total Cost (Rs)</th>
                <th>Total Profit (Rs)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ number_format($grandSales, 2) }}</td>
                <td>{{ number_format($grandCost, 2) }}</td>
                <td>{{ number_format($grandProfit, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <p style="text-align:center; margin-top:20px; font-size:10px; color:#555;">
        Generated on {{ now()->format('Y-m-d H:i:s') }}
    </p>
</body>
</html>
