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
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
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
        <div class="report-info">
            <p><strong>Prepared By:</strong> {{ $preparedBy }}</p>
            <p><strong>Report Date & Time:</strong> {{ $currentDateTime }}</p>
        </div>
    </div>

    <h2 class="report-title">
        Annual Sales Report
    </h2>

    <table>
        <thead>
            <tr>
                <th class="px-4 py-2">Year</th>
                <th class="px-4 py-2">Total Sales</th>
                <th class="px-4 py-2">Appointment Count</th>
                <th class="px-4 py-2">Services Count</th>
                <th class="px-4 py-2">Services</th>
                <th class="px-4 py-2">Prices</th>
                <th class="px-4 py-2">Employees</th>
                <th class="px-4 py-2">Customers</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reports as $report)
                <tr>
                    <td class="px-4 py-2">{{ $report->year }}</td>
                    <td class="px-4 py-2">₱{{ number_format($report->total_sales, 2) }}</td>
                    <td class="px-4 py-2">{{ $report->appointment_count }}</td>
                    <td class="px-4 py-2">{{ $report->services_count }}</td>
                    <td class="px-4 py-2">
                        @foreach (explode(',', $report->services) as $service)
                            <p>{{ $service }}</p>
                        @endforeach
                    </td>
                    <td class="px-4 py-2">
                        @foreach (explode(',', $report->prices) as $price)
                            <p>{{ number_format($price, 2) }}</p>
                        @endforeach
                    </td>
                    <td class="px-4 py-2">
                        @foreach (explode(',', $report->employees) as $employee)
                            <p>{{ $employee }}</p>
                        @endforeach
                    </td>
                    <td class="px-4 py-2">
                        @foreach (explode(',', $report->customers) as $customer)
                            <p>{{ $customer }}</p>
                        @endforeach
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
