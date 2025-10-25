<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Stock Summary Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; margin: 20px; }
        h2 { text-align: center; color: #4f46e5; }
        p { text-align: center; margin-bottom: 10px; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #999; padding: 6px; text-align: right; }
        th { background-color: #4f46e5; color: white; }
        td:first-child, th:first-child { text-align: left; }
        td:nth-child(2), th:nth-child(2),
        td:nth-child(3), th:nth-child(3) { text-align: center; }
        .footer { text-align: center; margin-top: 10px; font-size: 10px; color: #555; }
    </style>
</head>
<body>

<h2>📦 Current Stock Summary</h2>
<p><strong>Category:</strong> {{ $categoryName }}</p>

<table>
    <thead>
        <tr>
            <th>Product</th>
            <th>Category</th>
            <th>Size</th>
            <th>Available Qty</th>
            <th>Selling Price (Rs)</th>
        </tr>
    </thead>
    <tbody>
        @forelse($stock as $item)
            <tr>
                <td>{{ $item['product_name'] }}</td>
                <td>{{ $item['category'] }}</td>
                <td>{{ $item['size'] }}</td>
                <td style="text-align:center;">{{ $item['qty'] }}</td>
                <td>{{ number_format($item['selling_price'], 2) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" style="text-align:center; padding:10px;">No data available.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<p class="footer">Generated on {{ now()->format('Y-m-d H:i:s') }}</p>
</body>
</html>
