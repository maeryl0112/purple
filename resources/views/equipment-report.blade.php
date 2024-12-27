<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Equipment Report</title>
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
            margin: 20px 0;
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
        .low-stock, .maintenance-alert {
            color: red;
            font-weight: bold;
        }
        tfoot td {
            font-weight: bold;
            background-color: #f2f2f2;
        }
        .logo p {
            font-size: smaller;
            color: #62a;
        }
        @media print {
            body {
                margin: 0;
                font-size: 12px;
            }
            .header, .report-info {
                margin: 0;
                display: block;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="logo">
            <img src="{{ $image }}" width="300px" alt="Purple Look Salon and Spa Logo">
            <p>Stall 2 & 19, 678 Terminal Bayanan Bacoor Cavite <br> purplelookhairsalonandspa@gmail.com <br> 09********</p>
        </div>
    </div>
        <div class="report-info">
            <p><strong>Prepared By:</strong> {{ $preparedBy }}</p>
            <p><strong>Report Date & Time:</strong> {{ $currentDateTime }}</p>
        </div>
    <h1 class="report-title">Equipment Report</h1>
    <table>
        <thead>
            <tr>
                <th scope="col">Name</th>
                <th scope="col">Category</th>
                <th scope="col">Brand Name</th>
                <th scope="col">Quantity</th>
                <th scope="col">Staff Assigned</th>
                <th scope="col">Last Maintenance</th>
                <th scope="col">Next Maintenance</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($equipments as $equipment)
                @php
                    $lowQuantity = $equipment->quantity < 5; // Example threshold for low quantity
                    $maintenanceAlert = $equipment->next_maintenance && \Carbon\Carbon::parse($equipment->next_maintenance)->lessThanOrEqualTo(now()->addDays(7));
                @endphp
                <tr>
                    <td>{{ $equipment->name }}</td>
                    <td>{{ $equipment->category->name ?? 'N/A' }}</td>
                    <td>{{ $equipment->brand_name }}</td>
                    <td class="{{ $lowQuantity ? 'low-stock' : '' }}">{{ $equipment->quantity }}</td>
                    <td>{{ $equipment->employee->first_name ?? 'N/A' }}</td>
                    <td>{{ $equipment->last_maintenance ? \Carbon\Carbon::parse($equipment->last_maintenance)->format('Y-m-d') : 'N/A' }}</td>
                    <td class="{{ $maintenanceAlert ? 'maintenance-alert' : '' }}">
                        {{ $equipment->next_maintenance ? \Carbon\Carbon::parse($equipment->next_maintenance)->format('Y-m-d') : 'N/A' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7">Total Equipment: {{ $equipments->count() }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
