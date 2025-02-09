<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Daily Sales Report</title>
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

    <div class="report-info">
    <p><strong>Prepared By:</strong> {{ $preparedBy }}</p>
    <p><strong>Report Date & Time:</strong> {{ $currentDateTime }}</p>
    <p><strong>Report for Date:</strong> {{ \Carbon\Carbon::parse($selectedDate)->format('F d, Y') }}</p>
    <p><strong>Branch:</strong> {{ $branchAssigned }}</p>
    
</div>


    <h2 class="report-title">All Daily Sales Report</h2>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Branches</th>
                <th>Total Sales</th>
                <th>Service Name</th>
                <th>Sales</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports as $report)
                @php $rowspan = count($report->grouped_services); @endphp
                @if($rowspan > 0)
                    <!-- First row with date and total sales -->
                    <tr>
                        <td rowspan="{{ $rowspan }}">{{ \Carbon\Carbon::parse($report->date)->format('F d, Y') }}</td>
                        <td rowspan="{{$rowspan}}">{{ $report->branch_name ?? 'N/A' }}</td> <!-- Add branch name -->
                        <td rowspan="{{ $rowspan }}">{{ number_format($report->total_sales, 2) }}</td>
                        @php $firstService = true; @endphp
                        @foreach($report->grouped_services as $serviceName => $serviceData)
                            @if(!$firstService) <tr> @endif
                            <td>{{ $serviceName }}</td>
                            <td>{{ number_format($serviceData['total_price'], 2) }}</td>
                            </tr>
                            @php $firstService = false; @endphp
                        @endforeach
                    </tr>
                @endif
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2">Grand Total:</th>
                <td colspan="2"><strong>{{ number_format($grandTotal, 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
