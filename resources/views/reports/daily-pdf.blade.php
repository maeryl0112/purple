<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Sales Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 20px;
        }
        .header {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            margin-bottom: 20px;
            text-align: center;
        }
        .logo h1 {
            font-family: cursive;
            font-weight: 900;
            color: #62a;
            font-style: italic;
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
            vertical-align: top;
        }
        table th {
            background-color: #f2f2f2;
        }
        tfoot td {
            font-weight: bold;
            background-color: #f2f2f2;
        }
        .logo p {
            font-size: smaller;
            color: #62a;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
           <img src="{{ $image }}" width="300px" alt="Salon Logo">
           <p>Stall 2 & 19, 678 Terminal Bayanan Bacoor Cavite </br> purplelookhairsalonandspa@gmail.com </br> 09********</p>
        </div>
    </div>

    <div class="report-info">
        <p><strong>Prepared By:</strong> {{ $preparedBy }}</p>
        <p><strong>Report Date & Time:</strong> {{ $currentDateTime }}</p>
        <p><strong>Report for Date:</strong> {{ \Carbon\Carbon::parse($selectedDate)->format('F d, Y') }}</p>
        @if(isset($selectedBranch) && $selectedBranch)
            <p><strong>Branch:</strong> {{ $selectedBranch }}</p>
        @endif
    </div>

    <h2 class="report-title">Daily Sales Report</h2>

    <table class="table-container">
        <thead>
            <tr>
                <th>Service Name</th>
                <th>Sales</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reports as $report)
                @foreach ($report->grouped_services as $serviceName => $serviceData)
                    <tr>
                        <td>{{ $serviceName }}</td>
                        <td>{{ number_format($serviceData['total_price'], 2) }}</td>
                    </tr>
                @endforeach
            @endforeach
            <tr class="total-row">
                <td><strong>Total Sales:</strong></td>
                <td><strong>{{ number_format($grandTotal, 2) }}</strong></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
