<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplies and Consumables Report</title>
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
            color: #6a2695;
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
        tfoot td {
            font-weight: bold;
            background-color: #f2f2f2;
        }
        .low-stock {
            color: red;
            font-weight: bold;
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
    <p><strong>Assigned Branch:</strong> {{ $employeeBranchName }}</p>
</div>
    <h1>SUPPLIES REPORT</h1>

    <!-- Low Stock Supplies Section -->
    <h3>LOW STOCK SUPPLIES</h3>
    <table>
        <thead>
            <tr>
                <th class="low-stock">Name</th>
                <th class="low-stock">Category</th>
                <th class="low-stock">Description</th>
                <th class="low-stock">Quantity</th>
                <th class="low-stock">Color Code</th>
                <th class="low-stock">Color Shade</th>
                <th class="low-stock">Size</th>
                <th class="low-stock">Expiration Date</th>
                <th class="low-stock">Branch</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($supplies as $supply)
                @if ($supply->quantity < 10)
                    <tr>
                        <td class="low-stock">{{ $supply->name }}</td>
                        <td class="low-stock">{{ $supply->category->name ?? 'N/A' }}</td>
                        <td class="low-stock">{{ $supply->description ?? 'N/A' }}</td>
                        <td class="low-stock">{{ $supply->quantity }}</td>
                        <td class="low-stock">{{ $supply->color_code ?? 'N/A' }}</td>
                        <td class="low-stock">{{ $supply->color_shade ?? 'N/A' }}</td>
                        <td class="low-stock">{{ $supply->size ?? 'N/A' }}</td>
                        <td class="low-stock">{{ $supply->expiration_date ? \Carbon\Carbon::parse($supply->expiration_date)->format('Y-m-d') : 'N/A' }}</td>
                        <td class="low-stock">{{ $supply->branch->name ?? 'N/A' }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <!-- Expiring Supplies Section -->
    <h3>EXPIRING SUPPLIES (WITHIN 1 WEEK)</h3>
    <table>
        <thead>
            <tr>
                <th class="low-stock">Name</th>
                <th class="low-stock">Category</th>
                <th class="low-stock">Description</th>
                <th class="low-stock">Quantity</th>
                <th class="low-stock">Color Code</th>
                <th class="low-stock">Color Shade</th>
                <th class="low-stock">Size</th>
                <th class="low-stock">Expiration Date</th>
                <th class="low-stock">Branch</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($supplies as $supply)
                @if ($supply->expiration_date && \Carbon\Carbon::parse($supply->expiration_date)->diffInDays(now()) <= 7 
                    && !\Carbon\Carbon::parse($supply->expiration_date)->isPast())
                    <tr>
                        <td class="low-stock">{{ $supply->name }}</td>
                        <td class="low-stock">{{ $supply->category->name ?? 'N/A' }}</td>
                        <td class="low-stock">{{ $supply->description ?? 'N/A' }}</td>
                        <td class="low-stock">{{ $supply->quantity }}</td>
                        <td class="low-stock">{{ $supply->color_code ?? 'N/A' }}</td>
                        <td class="low-stock">{{ $supply->color_shade ?? 'N/A' }}</td>
                        <td class="low-stock">{{ $supply->size ?? 'N/A' }}</td>
                        <td class="low-stock">{{ \Carbon\Carbon::parse($supply->expiration_date)->format('Y-m-d') }}</td>
                        <td class="low-stock">{{ $supply->branch->name ?? 'N/A' }}</td>
                    </tr>
                @endif  
            @endforeach
        </tbody>
    </table>

    <!-- Non-Expiring Supplies Section -->
    <h3>NON-EXPIRING SUPPLIES (MORE THAN 1 WEEK REMAINING)</h3>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Category</th>
                <th>Description</th>
                <th>Quantity</th>
                <th>Color Code</th>
                <th>Color Shade</th>
                <th>Size</th>
                <th>Expiration Date</th>
                <th>Branch</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($supplies as $supply)
                @if (!$supply->expiration_date || \Carbon\Carbon::parse($supply->expiration_date)->diffInDays(now()) > 7)
                    <tr>
                        <td>{{ $supply->name }}</td>
                        <td>{{ $supply->category->name ?? 'N/A' }}</td>
                        <td>{{ $supply->description ?? 'N/A' }}</td>
                        <td>{{ $supply->quantity }}</td>
                        <td>{{ $supply->color_code ?? 'N/A' }}</td>
                        <td>{{ $supply->color_shade ?? 'N/A' }}</td>
                        <td>{{ $supply->size ?? 'N/A' }}</td>
                        <td>{{ $supply->expiration_date ? \Carbon\Carbon::parse($supply->expiration_date)->format('Y-m-d') : 'N/A' }}</td>
                        <td>{{ $supply->branch->name ?? 'N/A' }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</body>
</html>
