<!DOCTYPE html>
<html>
<head>
    <title>Monthly Sales, Cost & Profit Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h2 { text-align: center; color: #2c3e50; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: right; }
        th { background-color: #2c3e50; color: white; }
        td:first-child, th:first-child { text-align: left; }
        tfoot td { font-weight: bold; background-color: #f3f3f3; }
    </style>
</head>
<body>
    <h2>Monthly Sales, Cost & Profit Report</h2>
    <p>
        <strong>Period:</strong> {{ $startDate->format('F Y') }} - {{ $endDate->format('F Y') }}
    </p>

    <table>
        <thead>
            <tr>
                <th>Month</th>
                <th>Total Sales (Rs)</th>
                <th>Total Cost (Rs)</th>
                <th>Profit (Rs)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grandSales = $monthly->sum('total_sales');
                $grandCost = $monthly->sum('total_cost');
                $grandProfit = $monthly->sum('profit');
            @endphp

            @foreach ($monthly as $data)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($data['month'].'-01')->format('F Y') }}</td>
                    <td>{{ number_format($data['total_sales'], 2) }}</td>
                    <td>{{ number_format($data['total_cost'], 2) }}</td>
                    <td>{{ number_format($data['profit'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td>Total</td>
                <td>{{ number_format($grandSales, 2) }}</td>
                <td>{{ number_format($grandCost, 2) }}</td>
                <td>{{ number_format($grandProfit, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
