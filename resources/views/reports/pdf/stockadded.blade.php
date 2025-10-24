<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Stock Added Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f3f3f3; }
        h2 { text-align: center; color: #333; }
        .total { text-align: right; font-weight: bold; margin-top: 20px; }
    </style>
</head>
<body>
    <h2>📦 Stock Added Report</h2>
    <p><strong>Period:</strong> {{ $startDate }} to {{ $endDate }}</p>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Product</th>
                <th>Category</th>
                <th>Qty Added</th>
                <th>Cost Price (LKR)</th>
                <th>Total Cost (LKR)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($stockItems as $item)
                <tr>
                    <td>{{ $item->stock->created_at->format('Y-m-d') }}</td>
                    <td>{{ $item->product->name ?? '-' }}</td>
                    <td>{{ $item->product->category->name ?? '-' }}</td>
                    <td>{{ $item->qty }}</td>
                    <td style="text-align:right;">{{ number_format($item->cost_price, 2) }}</td>
                    <td style="text-align:right;">{{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p class="total">💰 <strong>Total Stock Added Value:</strong> Rs. {{ number_format($totalCost, 2) }}</p>
</body>
</html>
