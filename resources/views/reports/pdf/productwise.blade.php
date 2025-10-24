<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Product-wise Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; margin: 20px; }
        h2 { text-align: center; color: #4f46e5; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: right; }
        th { background-color: #4f46e5; color: white; }
        td:first-child, th:first-child { text-align: left; }
        td:nth-child(2), th:nth-child(2) { text-align: left; }
        .summary { margin-top: 15px; font-size: 13px; }
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

            @foreach($productWise as $row)
                @php
                    $grandSales += $row['total_sales'];
                    $grandCost += $row['total_cost'];
                    $grandProfit += $row['profit'];
                @endphp
                <tr>
                    <td>{{ $row['product_name'] }}</td>
                    <td>{{ $row['category'] }}</td>
                    <td>{{ $row['total_qty'] }}</td>
                    <td>{{ number_format($row['total_sales'], 2) }}</td>
                    <td>{{ number_format($row['total_cost'], 2) }}</td>
                    <td>{{ number_format($row['profit'], 2) }}</td>
                </tr>
            @endforeach

            <tr style="background-color:#eef2ff; font-weight:bold;">
                <td colspan="3" style="text-align:right;">TOTAL</td>
                <td>{{ number_format($grandSales, 2) }}</td>
                <td>{{ number_format($grandCost, 2) }}</td>
                <td>{{ number_format($grandProfit, 2) }}</td>
            </tr>
        </tbody>
    </table>

</body>
</html>
