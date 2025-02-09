<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly Sales Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 4px;
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
           <p>Stall 2 & 19, 678 Terminal Bayanan Bacoor Cavite </br> 
           purplelookhairsalonandspa@gmail.com<br>
               0916-504-8592 (Globe) <br>
               0968-322-8344 (Smart) <br>
               (046) 450-1531 (Molino Branch) <br>
               (046) 471-3897 (Main Branch) <br> 
    </p>
        </div>
    </div>
    <h2 class="report-title">Weekly Sales Report</h2>

    <table>
        <thead>
            <tr>
                <th>Week</th>
                <th>Week Start</th>
                <th>Week End</th>
                <th>Service Name</th>
                <th>Branch</th>
                <th>Sales</th>
             
            </tr>
        </thead>
        <tbody>
            @foreach ($reports as $report)
                <tr>
                    <td>{{ $report->week }}</td>
                    <td>{{ \Carbon\Carbon::parse($report->week_start)->format('F d, Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($report->week_end)->format('F d, Y') }}</td>
                    <td>{{ number_format($report->total_sales, 2) }}</td>
                    <td>{{ $report->appointment_count }}</td>
                    <td>
                        @foreach (explode(',', $report->services) as $service)
                            <p>{{ $service }}</p>
                        @endforeach
                    </td>
                    <td>{{ $report->branch_name ?? 'N/A' }}</td>
                    <td>
                        @foreach (explode(',', $report->prices) as $price)
                            <p>{{ number_format($price, 2) }}</p>
                        @endforeach
                    </td>
               
                    
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
