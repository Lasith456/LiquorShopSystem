<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Stock Added Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; margin: 25px; }
        h2 { text-align: center; color: #4f46e5; margin-bottom: 5px; }
        p { text-align: center; font-size: 13px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #aaa; padding: 6px; }
        th { background-color: #4f46e5; color: white; }
        td { text-align: right; }
        td:first-child, th:first-child { text-align: left; }
        td:nth-child(2), td:nth-child(3), td:nth-child(4) { text-align: center; }
        .total-row { background-color: #eef2ff; font-weight: bold; }
        .footer { text-align: center; font-size: 11px; color: #666; margin-top: 15px; }
    </style>
</head>
<body>
    <h2>📦 Stock Added Report</h2>
    <p>
        <strong>Category:</strong> {{ $category }} | 
        <strong>Product:</strong> {{ $product }} <br>
        <strong>Period:</strong> {{ $startDate->toDateString() }} → {{ $endDate->toDateString() }}
    </p>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Product</th>
                <th>Category</th>
                <th>Size</th>
                <th>Qty Added</th>
                <th>Cost Price (Rs)</th>
                <th>Total Cost (Rs)</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @forelse($stockItems as $item)
                @php $grandTotal += $item->total ?? 0; @endphp
                <tr>
                    <td>{{ $item->stock->created_at->format('Y-m-d') }}</td>
                    <td>{{ $item->product->name ?? '-' }}</td>
                    <td>{{ $item->product->category->name ?? '-' }}</td>
                    <td>{{ $item->size->label ?? '-' }}</td>
                    <td style="text-align:center;">{{ $item->qty }}</td>
                    <td>{{ number_format($item->cost_price, 2) }}</td>
                    <td>{{ number_format($item->total, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding:8px;">No stock additions found.</td>
                </tr>
            @endforelse

            <tr class="total-row">
                <td colspan="6" style="text-align:right;">💰 Total Stock Added Value</td>
                <td>{{ number_format($grandTotal, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <p class="footer">
        Generated on {{ now()->format('Y-m-d H:i:s') }}
    </p>
</body>
</html>
