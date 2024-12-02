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
        <div class="report-info">
            <p><strong>Prepared By:</strong> {{ $preparedBy }}</p>
            <p><strong>Report Date & Time:</strong> {{ $currentDateTime }}</p>
        </div>
    </div>

    <h2 class="report-title">Daily Sales Report for {{ \Carbon\Carbon::today()->format('F d, Y') }}</h2>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Total Sales</th>
                <th>Appointments</th>
                <th>Service</th>
                <th>Price</th>
                <th>Employee</th>
                <th>Customer</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reports as $report)
                <tr>
                    <td rowspan="{{ count($report->services_with_employees_and_customers) + 1 }}">
                        {{ $report->date }}
                    </td>
                    <td rowspan="{{ count($report->services_with_employees_and_customers) + 1 }}">
                        ₱{{ number_format($report->total_sales, 2) }}
                    </td>
                    <td rowspan="{{ count($report->services_with_employees_and_customers) + 1 }}">
                        {{ $report->appointment_count }}
                    </td>
                </tr>
                @foreach ($report->services_with_employees_and_customers as $item)
                    <tr>
                        <td>{{ $item['service'] }}</td>
                        <td>₱{{ number_format($item['price'], 2) }}</td>
                        <td>{{ $item['employee'] }}</td>
                        <td>{{ $item['customer'] }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>
</html>
