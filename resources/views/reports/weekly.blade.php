<x-dashboard>
    <div class="p-4 sm:ml-64">
        <div class="flex justify-between mx-5">
            <h2 class=" text-4xl font-bold text-salonPurple">Weekly Sales Report</h2>
            <div class="text-right">
                <h3 class="text-2xl font-semibold text-gray-950">Total Sales:</h3>
                <p class="text-xl font-bold text-purple-700">
                    ₱ {{ number_format($grandTotal, 2) }}
                </p>
            </div>
        </div>
        <div class="py-4 ml-5">
            <a href="{{ route('weekly.report.pdf') }}" class="focus:outline-none text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900">
                Download This Week Sales Report PDF
            </a>
        </div>
        <div class="py-2 ml-5">
            <a href="{{ route('all.weekly.report.pdf') }}" class="focus:outline-none text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900">
                Download All Weekly Sales Report PDF
            </a>
        </div>

        <div class="overflow-auto rounded-lg border border-gray-200 shadow-md m-5">
        <table class="w-full border-collapse bg-white text-center text-m text-gray-950 overflow-x-scroll min-w-screen">
        <thead class="bg-gray-100">
            <tr>
                <th>Week</th>
                <th>Week Start</th>
                <th>Week End</th>
                <th>Total Sales</th>
                <th>Appointment Count</th>
                <th>Services</th>
                <th>Prices</th>
                <th>Employees</th>
                <th>Customers</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reports as $report)
                <tr>
                    <td class="border-2">{{ $report->week }}</td>
                    <td class="border-2">{{ \Carbon\Carbon::parse($report->week_start)->format('F d, Y') }}</td>
                    <td class="border-2">{{ \Carbon\Carbon::parse($report->week_end)->format('F d, Y') }}</td>
                    <td class="border-2">₱{{ number_format($report->total_sales, 2) }}</td>
                    <td class="border-2">{{ $report->appointment_count }}</td>
                    <td class="border-2">
                        @foreach (explode(',', $report->services) as $service)
                            <p>{{ $service }}</p>
                        @endforeach
                    </td>
                    <td class="border-2">
                        @foreach (explode(',', $report->prices) as $price)
                            <p>₱{{ number_format($price, 2) }}</p>
                        @endforeach
                    </td>
                    <td class="border-2">
                        @foreach (explode(',', $report->employees) as $employee)
                            <p>{{ $employee }}</p>
                        @endforeach
                    </td>
                    <td class="border-2">
                        @foreach (explode(',', $report->customers) as $customer)
                            <p>{{ $customer }}</p>
                        @endforeach
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
        </div>
    </div>
</x-dashboard>
