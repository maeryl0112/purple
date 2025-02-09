s<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Customers Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }
        th { background-color: #f4f4f4; }
        .logo p {
            font-size: smaller;
            color: #62a;
            text-align: center;
        }
        .header {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            margin-bottom: 20px;
            text-align: center;
        }
        .customer { margin-bottom: 20px; page-break-after: always; }
        .report-info {
            margin-top: 10px;
        }
        .logo img {
            display: block;
            margin: 0 auto;
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
            <p><strong>Branch Assigned:</strong> {{ $assignedBranch }}</p>

        </div>
    @foreach($customers as $customerId => $services)
        <div class="customer">
            <h2>Customer: {{ $services->first()->name }}</h2>
            <p>Email: {{ $services->first()->email }}</p>
            <p><strong>Total Revenue:</strong> {{ number_format($services->first()->total_revenue, 2) }}</p>

            <table>
                <thead>
                    <tr>
                        <th>Service Name</th>
                        <th>Branch</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($services as $service)
                        <tr>
                            <td>{{ $service->service_name }}</td>
                            <td>{{ $service->branch_name }}</td>
                            <td>{{ number_format($service->service_price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach
</body>
</html>
