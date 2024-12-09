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
        <div class="report-info">
            <p><strong>Prepared By:</strong> {{ $preparedBy }}</p>
            <p><strong>Report Date & Time:</strong> {{ $currentDateTime }}</p>
        </div>
    </div>
    <h1>Supplies Report</h1>
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
                <th>Supplier</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($supplies as $supply)
                <tr>
                    <td>{{ $supply->name }}</td>
                    <td>{{ $supply->category->name ?? 'N/A' }}</td>
                    <td>{{ $supply->description ?? 'N/A' }}</td>
                    <td>{{ $supply->quantity }}</td>
                    <td>{{ $supply->color_code ?? 'N/A' }}</td>
                    <td>{{ $supply->color_shade ?? 'N/A' }}</td>
                    <td>{{ $supply->size ?? 'N/A' }}</td>
                    <td>{{ $supply->expiration_date ? \Carbon\Carbon::parse($supply->expiration_date)->format('Y-m-d') : 'N/A' }}</td>
                    <td>{{ $supply->online_supplier->name ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
