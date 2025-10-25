<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Daywise Sales Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #000;
        }
        h2, h4 {
            text-align: center;
            margin: 4px 0;
            padding: 2px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            page-break-inside: avoid;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f3f3f3;
            font-weight: bold;
        }
        .day-header {
            background-color: #e0e7ff;
            color: #1e3a8a;
            font-weight: bold;
            text-align: left;
            padding: 8px;
            border: 1px solid #000;
        }
        .summary-row {
            background-color: #f9fafb;
            font-weight: bold;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <h2>📊 Daywise Sales Report</h2>
    <h4>Period: {{ $startDate }} — {{ $endDate }}</h4>

    @foreach ($dayWise as $day)
        <div style="margin-top: 25px;">
            <div class="day-header">🗓️ Date: {{ $day->day }}</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 25%;">Product (Size)</th>
                        <th class="text-center" style="width: 8%;">Qty</th>
                        <th class="text-right" style="width: 12%;">Selling Price (Rs)</th>
                        <th class="text-right" style="width: 12%;">Cost Price (Rs)</th>
                        <th class="text-right" style="width: 13%;">Total Sales (Rs)</th>
                        <th class="text-right" style="width: 13%;">Total Cost (Rs)</th>
                        <th class="text-right" style="width: 13%;">Profit (Rs)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($day->items as $item)
                        <tr>
                            <td>
                                {{ $item->product->name ?? '-' }}
                                <small style="color:#555;">({{ $item->size->label ?? '-' }})</small>
                            </td>
                            <td class="text-center">{{ $item->qty }}</td>
                            <td class="text-right">{{ number_format($item->selling_price, 2) }}</td>
                            <td class="text-right">{{ number_format($item->latest_cost_price ?? 0, 2) }}</td>
                            <td class="text-right">{{ number_format($item->qty * $item->selling_price, 2) }}</td>
                            <td class="text-right">{{ number_format($item->qty * ($item->latest_cost_price ?? 0), 2) }}</td>
                            @php 
                                $profit = ($item->qty * $item->selling_price) - ($item->qty * ($item->latest_cost_price ?? 0));
                            @endphp
                            <td class="text-right" style="color: {{ $profit >= 0 ? '#15803d' : '#b91c1c' }};">
                                {{ number_format($profit, 2) }}
                            </td>
                        </tr>
                    @endforeach

                    {{-- ✅ Totals for the Day --}}
                    <tr class="summary-row">
                        <td colspan="4" class="text-right">Total</td>
                        <td class="text-right">{{ number_format($day->total_sales, 2) }}</td>
                        <td class="text-right">{{ number_format($day->total_cost, 2) }}</td>
                        <td class="text-right" style="color: {{ $day->profit >= 0 ? '#15803d' : '#b91c1c' }};">
                            {{ number_format($day->profit, 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endforeach

    {{-- Footer --}}
    <div style="margin-top:40px; text-align:center; font-size:11px; color:#555;">
        Generated on {{ now()->format('Y-m-d H:i') }} |
        Powered by <strong style="color:#1e3a8a;">NsoftItSolutions</strong>
    </div>
</body>
</html>
