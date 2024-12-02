<x-dashboard>
    <div class="p-4 sm:ml-64">
        <div class="flex justify-between mx-5">
            <h2 class=" text-4xl font-bold text-salonPurple">Quarterly Sales Report</h2>


        </div>
        <div class="py-4 ml-5">
        <a href="{{ route('quarterly.report.pdf') }}" class="focus:outline-none text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900">
            Download This Quarter Sales Report PDF
        </a>
    </div>
    <div class="py-2 ml-5">
        <a href="{{ route('all.quarterly.report.pdf') }}" class="focus:outline-none text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900">
            Download All Quarter Sales Report PDF
        </a>
    </div>

        <div class="overflow-auto rounded-lg border border-gray-200 shadow-md m-5">
        <table class="w-full border-collapse bg-white text-center text-lg text-gray-950 overflow-x-scroll min-w-screen">
        <thead class="bg-gray-100">
            <tr>
                <th>Quarter</th>
                <th>Total Sales</th>
                <th>Appointment Count</th>
                <th>Service Count</th>
                <th>Services</th>
                <th>Prices</th>
                <th>Employees</th>
                <th>Customers</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reports as $report)
                <tr>
                    <td>{{ $report->quarter_label }}</td>
                    <td>₱{{ number_format($report->total_sales, 2) }}</td>
                    <td>{{ $report->appointment_count }}</td>
                    <td>{{ $report->service_count }}</td>
                    <td>
                        @foreach (explode(',', $report->services) as $service)
                            <p>{{ $service }}</p>
                        @endforeach
                    </td>
                    <td>
                        @foreach (explode(',', $report->prices) as $price)
                            <p>₱{{ number_format($price, 2) }}</p>
                        @endforeach
                    </td>
                    <td>
                        @foreach (explode(',', $report->employees) as $employee)
                            <p>{{ $employee }}</p>
                        @endforeach
                    </td>
                    <td>
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
