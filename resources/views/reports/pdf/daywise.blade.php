<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Daywise Sales Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2, h4 { text-align: center; margin: 0; padding: 4px; }
    </style>
</head>
<body>
    <h2>Daywise Sales Report</h2>
    <h4>{{ $startDate }} — {{ $endDate }}</h4>

    @foreach ($dayWise as $day)
        <h4>{{ $day->day }}</h4>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Selling Price</th>
                    <th>Cost Price</th>
                    <th>Total Sales</th>
                    <th>Total Cost</th>
                    <th>Profit</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($day->items as $item)
                <tr>
                    <td>{{ $item->product->name ?? '-' }}</td>
                    <td>{{ $item->qty }}</td>
                    <td style="text-align:right;">{{ number_format($item->selling_price, 2) }}</td>
                    <td style="text-align:right;">{{ number_format($item->latest_cost_price, 2) }}</td>
                    <td style="text-align:right;">{{ number_format($item->qty * $item->selling_price, 2) }}</td>
                    <td style="text-align:right;">{{ number_format($item->qty * $item->latest_cost_price, 2) }}</td>
                    <td style="text-align:right;">{{ number_format(($item->selling_price - $item->latest_cost_price) * $item->qty, 2) }}</td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="4" style="text-align:right;font-weight:bold;">Total</td>
                    <td style="text-align:right;font-weight:bold;">{{ number_format($day->total_sales, 2) }}</td>
                    <td style="text-align:right;font-weight:bold;">{{ number_format($day->total_cost, 2) }}</td>
                    <td style="text-align:right;font-weight:bold;">{{ number_format($day->profit, 2) }}</td>
                </tr>
            </tbody>
        </table>
    @endforeach
</body>
</html>
