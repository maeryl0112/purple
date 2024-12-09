<x-dashboard>
    <div class="p-4 sm:ml-64">
        <div class="flex justify-between mx-5">
            <h2 class="text-4xl font-bold text-salonPurple">Daily Sales Report</h2>
            <p><strong>Grand Total Sales:</strong> ₱{{ number_format($grandTotal, 2) }}</p>
        </div>

        <!-- Date Filter -->
        <div class="py-4 ml-5">
            <form action="{{ route('daily.report') }}" method="GET" id="date-filter-form">
                <label for="date" class="mr-2">Select Date:</label>
                <input
                    type="date"
                    id="date"
                    name="date"
                    value="{{ $selectedDate }}"
                    class="border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500"
                    onchange="document.getElementById('date-filter-form').submit();"
                >
            </form>
        </div>

        <!-- Data Table -->
        <div class="overflow-auto rounded-lg border border-gray-200 shadow-md m-5">
            <table class="w-full border-collapse bg-white text-center text-lg text-gray-950 overflow-x-scroll min-w-screen">
                <thead class="bg-gray-100">
                    <tr>
                        <th>Date</th>
                        <th>Total Sales</th>
                        <th>Appointment Count</th>
                        <th>Services with Details</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reports as $report)
                        <tr>
                            <td>{{ $report->date }}</td>
                            <td>₱{{ number_format($report->total_sales, 2) }}</td>
                            <td>{{ $report->appointment_count }}</td>
                            <td>
                                @foreach ($report->services_with_details as $service)
                                    <p>
                                        <strong>Service:</strong> {{ $service['name'] }} <br>
                                        <strong>Price:</strong> ₱{{ number_format($service['price'], 2) }} <br>
                                        <strong>Employee:</strong> {{ $service['employee'] }} <br>
                                        <strong>Customer:</strong> {{ $service['customer'] }}
                                    </p>
                                    <hr>
                                @endforeach
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">No sales data found for the selected date.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <a href="{{route('daily.report.pdf')}}" type="button" class="focus:outline-none text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 mx-5 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900">Download Today's Sales Report</a>

    </div>
</x-dashboard>
