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
           <p>Stall 2 & 19, 678 Terminal Bayanan Bacoor Cavite </br> purplelookhairsalonandspa@gmail.com </br> 09********</p>
        </div>
    </div>
        <div class="report-info">
            <p><strong>Prepared By:</strong> {{ $preparedBy }}</p>
            <p><strong>Report Date & Time:</strong> {{ $currentDateTime }}</p>
        </div>

    <h2 class="report-title">
        Annual Sales Report
    </h2>

    <table>
        <thead>
            <tr>
                <th>Year</th>
                <th>Total Sales</th>
                <th>Appointment Count</th>
                <th>Services Count</th>
                <th>Services</th>
                <th>Prices</th>
                <th>Employees</th>
                <th>Customers</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reports as $report)
                <tr>
                    <td>{{ $report->year }}</td>
                    <td>{{ number_format($report->total_sales, 2) }}</td>
                    <td>{{ $report->appointment_count }}</td>
                    <td>{{ $report->services_count }}</td>
                    <td>
                        @foreach (explode(',', $report->services) as $service)
                            {{ $service }}<br>
                        @endforeach
                    </td>
                    <td>
                        @foreach (explode(',', $report->prices) as $price)
                            {{ number_format($price, 2) }}<br>
                        @endforeach
                    </td>
                    <td>
                        @foreach (explode(',', $report->employees) as $employee)
                            {{ $employee }}<br>
                        @endforeach
                    </td>
                    <td>
                        @foreach (explode(',', $report->customers) as $customer)
                            {{ $customer }}<br>
                        @endforeach
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
