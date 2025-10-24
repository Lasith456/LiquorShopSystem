<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Stock Summary Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; margin: 20px; }
        h2 { text-align: center; color: #4f46e5; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: right; }
        th { background-color: #4f46e5; color: white; }
        td:first-child, th:first-child { text-align: left; }
        td:nth-child(2), th:nth-child(2) { text-align: left; }
        .summary { margin-top: 10px; font-size: 13px; }
    </style>
</head>
<body>

    <h2>📦 Stock Summary Report</h2>
    <p class="summary"><strong>Category:</strong> {{ $categoryName }}</p>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Category</th>
                <th>Available Qty</th>
                <th>Selling Price (Rs)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($stock as $p)
                <tr>
                    <td>{{ $p->name }}</td>
                    <td>{{ $p->category->name ?? '-' }}</td>
                    <td>{{ $p->qty }}</td>
                    <td>{{ number_format($p->selling_price, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="4" style="text-align:center;">No records found.</td></tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
