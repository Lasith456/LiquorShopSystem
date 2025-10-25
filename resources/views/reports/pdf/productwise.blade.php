<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Product-wise Sales, Cost & Profit Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; margin: 25px; color: #333; }
        h2 { text-align: center; color: #4f46e5; margin-bottom: 5px; }
        p.summary { text-align: center; margin: 0 0 15px 0; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #999; padding: 6px; }
        th { background-color: #4f46e5; color: white; font-weight: bold; }
        td { text-align: right; }
        td:first-child, th:first-child { text-align: left; }
        td:nth-child(2), th:nth-child(2) { text-align: left; }
        td:nth-child(3), th:nth-child(3) { text-align: center; }
        .total-row { background-color: #eef2ff; font-weight: bold; }
        .footer { text-align: center; font-size: 11px; margin-top: 15px; color: #555; }
        .nowrap { white-space: nowrap; }
    </style>
</head>
<body>

    <h2>🧾 Product-wise Sales, Cost & Profit Report</h2>

    <p class="summary">
        <strong>Category:</strong> {{ $category }} |
        <strong>Product:</strong> {{ $product }} <br>
        <strong>From:</strong> {{ $startDate->toDateString() }} 
        <strong>To:</strong> {{ $endDate->toDateString() }}
    </p>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Category</th>
                <th>Size</th>
                <th>Qty Sold</th>
                <th>Total Sales (Rs)</th>
                <th>Total Cost (Rs)</th>
                <th>Profit (Rs)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grandSales = 0;
                $grandCost = 0;
                $grandProfit = 0;
            @endphp

            @forelse($productWise as $row)
                @php
                    $grandSales += $row['total_sales'];
                    $grandCost += $row['total_cost'];
                    $grandProfit += $row['profit'];
                @endphp
                <tr>
                    <td>{{ $row['product_name'] }}</td>
                    <td>{{ $row['category'] }}</td>
                    <td class="nowrap">{{ $row['size'] }}</td>
                    <td style="text-align:center;">{{ $row['total_qty'] }}</td>
                    <td>{{ number_format($row['total_sales'], 2) }}</td>
                    <td>{{ number_format($row['total_cost'], 2) }}</td>
                    <td style="color:{{ $row['profit'] < 0 ? 'red' : 'green' }};">
                        {{ number_format($row['profit'], 2) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding:10px;">No records found for the selected filters.</td>
                </tr>
            @endforelse

            {{-- Grand Total --}}
            <tr class="total-row">
                <td colspan="3" style="text-align:right;">TOTAL</td>
                <td style="text-align:center;">—</td>
                <td>{{ number_format($grandSales, 2) }}</td>
                <td>{{ number_format($grandCost, 2) }}</td>
                <td>{{ number_format($grandProfit, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <p class="footer">
        Generated on {{ now()->format('Y-m-d H:i:s') }}
    </p>

</body>
</html>
