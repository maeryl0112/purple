<x-dashboard>
    <div class="p-4 sm:ml-64">
        <div class="flex justify-between mx-5">
            <h2 class="text-4xl font-bold text-salonPurple">Weekly Sales Report</h2>
            <p><strong>Grand Total Sales:</strong> ₱{{ number_format($grandTotal, 2) }}</p>
        </div>

        <!-- Week Filter -->
        <div class="py-4 ml-5">
            <form action="{{ route('weekly.report') }}" method="GET" id="week-filter-form">
                <label for="week" class="mr-2">Select Week:</label>
                <input
                    type="week"
                    id="week"
                    name="week"
                    value="{{ $selectedWeek }}"
                    class="border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500"
                    onchange="document.getElementById('week-filter-form').submit();"
                >
            </form>
        </div>

        <!-- Data Table -->
        <div class="overflow-auto rounded-lg border border-gray-200 shadow-md m-5">
            <table class="w-full border-collapse bg-white text-center text-lg text-gray-950 overflow-x-scroll min-w-screen">
                <thead class="bg-gray-100">
                    <tr>
                        <th>Week</th>
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
                            <td>{{ \Carbon\Carbon::parse($report->week_start)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($report->week_end)->format('M d, Y') }}</td>
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
                            <td colspan="7">No sales data found for the selected week.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <a href="{{route('weekly.report.pdf')}}" type="button" class="focus:outline-none text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 mx-5 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900">Download This Week's Sales Report</a>

    </div>
</x-dashboard>
