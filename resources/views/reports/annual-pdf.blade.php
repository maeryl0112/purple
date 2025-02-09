<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Annual Sales Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo img {
            max-width: 300px;
        }
        .logo p {
            font-size: smaller;
            color: #62a;
            margin: 0;
        }
        .report-info p {
            margin: 5px 0;
        }
        .report-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
        }
        table th {
            background-color: #f2f2f2;
        }
        tfoot td {
            font-weight: bold;
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <img src="{{ $image }}" alt="Salon Logo">
            <p>
                Stall 2 & 19, 678 Terminal Bayanan Bacoor Cavite <br>
                purplelookhairsalonandspa@gmail.com <br>
                0916-504-8592 (Globe) | 0968-322-8344 (Smart) <br>
                (046) 450-1531 (Molino Branch) | (046) 471-3897 (Main Branch)
            </p>
        </div>
    </div>
    <div class="report-info">
        <p><strong>Prepared By:</strong> {{ $preparedBy }}</p>
        <p><strong>Report Date & Time:</strong> {{ $currentDateTime }}</p>
    </div>
    <h2 class="report-title">Annual Sales Report</h2>
    <table>
        <thead>
            <tr>
                <th>Year</th>
                <th>Branch</th>
                <th>Service Name</th>
                <th>Sales</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reports as $report)
                <tr>
                    <td>{{ $report->year }}</td>
                    <td>{{ $report->branch_name }}</td>

                    <td>
                        @foreach ($report->grouped_services as $serviceName => $serviceData)
                            {{ $serviceName }}<br>
                        @endforeach
                    </td>
                    <td>
                        @foreach ($report->grouped_services as $serviceData)
                            {{ number_format($serviceData['total_price'], 2) }}<br>
                        @endforeach
                    </td>
                    <td>{{ number_format($report->total_sales, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4">Grand Total</td>
                <td>{{ number_format($grandTotal, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
