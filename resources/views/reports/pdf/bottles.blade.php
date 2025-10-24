<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Empty Bottle Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 6px; }
        th { background: #f2f2f2; }
        h2 { text-align: center; color: #333; }
    </style>
</head>
<body>
    <h2>🍾 Empty Bottle Return Report</h2>
    <p><strong>Period:</strong> {{ $startDate }} to {{ $endDate }}</p>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Quantity</th>
                <th>Price per Bottle (LKR)</th>
                <th>Total (LKR)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bottles as $b)
                <tr>
                    <td>{{ $b->date }}</td>
                    <td>{{ $b->quantity }}</td>
                    <td style="text-align:right;">{{ number_format($b->price_per_bottle, 2) }}</td>
                    <td style="text-align:right;">{{ number_format($b->total_price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p style="text-align:right; font-weight:bold; margin-top:20px;">
        💰 Total Bottle Value: Rs. {{ number_format($totalValue, 2) }}
    </p>
</body>
</html>
