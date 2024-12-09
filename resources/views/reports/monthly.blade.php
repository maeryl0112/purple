<x-dashboard>
    <div class="p-4 sm:ml-64">
        <div class="flex justify-between mx-5">
            <h2 class="text-4xl font-bold text-salonPurple">Monthly Sales Report</h2>
            <p><strong>Grand Total Sales:</strong> ₱{{ number_format($grandTotal, 2) }}</p>
        </div>

        <!-- Month Filter -->
        <div class="py-4 ml-5">
            <form action="{{ route('monthly.report') }}" method="GET" id="month-filter-form">
                <label for="month" class="mr-2">Select Month:</label>
                <input
                    type="month"
                    id="month"
                    name="month"
                    value="{{ $selectedMonth }}"
                    class="border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500"
                    onchange="document.getElementById('month-filter-form').submit();"
                >
            </form>
        </div>

        <!-- Data Table -->
        <div class="overflow-auto rounded-lg border border-gray-200 shadow-md m-5">
            <table class="w-full border-collapse bg-white text-center text-lg text-gray-950 overflow-x-scroll min-w-screen">
                <thead class="bg-gray-100">
                    <tr>
                        <th>Month</th>
                        <th>Total Sales</th>
                        <th>Appointment Count</th>
                        <th>Services</th>
                        <th>Prices</th>
                        <th>Employees</th>
                        <th>Customers</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reports as $report)
                        <tr>
                            <td>{{ $report->month_name }} {{ $report->year }}</td>
                            <td>₱{{ number_format($report->total_sales, 2) }}</td>
                            <td>{{ $report->appointment_count }}</td>
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
                    @empty
                        <tr>
                            <td colspan="7">No sales data found for the selected month.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <a href="{{route('monthly.report.pdf')}}" type="button" class="focus:outline-none text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 mx-5 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900">Download This Month's Sales Report</a>

    </div>
</x-dashboard>
