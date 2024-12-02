<x-dashboard>
    <div class="p-4 sm:ml-64">
        <div class="flex justify-between mx-5">
            <h2 class="text-4xl font-bold text-salonPurple">Daily Sales Report</h2>
            <div class="text-right">
                <h3 class="text-2xl font-semibold text-gray-950">Total Sales Amount:</h3>
                <p class="text-xl font-bold text-purple-700">
                    ₱ {{ number_format($grandTotal, 2) }}
                </p>
            </div>
        </div>

        <div class="py-4 ml-5">
            <a href="{{ route('daily.report.pdf') }}" class="focus:outline-none text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900">
                Download Today's Report PDF
            </a>
        </div>
        <div class="py-2 ml-5">
            <a href="{{ route('all.daily.report.pdf') }}" class="focus:outline-none text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900">
                Download All Daily Sales Report PDF
            </a>
        </div>

        <div class="overflow-auto rounded-lg border border-gray-200 shadow-md m-5">
            <table class="w-full border-collapse bg-white text-center text-lg text-gray-950">
                <thead class="bg-gray-100">
                    <tr>
                        <th>Date</th>
                        <th>Total Sales</th>
                        <th>Appointments</th>
                        <th>Service Name</th>
                        <th>Service Price</th>
                        <th>Employee Assigned</th>
                        <th>Customer</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reports as $report)
                        <!-- Date and total sales -->
                        <tr>
                            <td class="border-2" rowspan="{{ count($report->services_with_details) + 1 }}">
                                {{ $report->date }}
                            </td>
                            <td class="border-2" rowspan="{{ count($report->services_with_details) + 1 }}">
                                ₱{{ number_format($report->total_sales, 2) }}
                            </td>
                            <td class="border-2" rowspan="{{ count($report->services_with_details) + 1 }}">
                                {{ $report->appointment_count }}
                            </td>
                        </tr>
                        <!-- Service details -->
                        @foreach($report->services_with_details as $detail)
                            <tr>
                                <td class="border-2">{{ $detail['name'] }}</td>
                                <td class="border-2">₱{{ number_format($detail['price'], 2) }}</td>
                                <td class="border-2">{{ $detail['employee'] }}</td>
                                <td class="border-2">{{ $detail['customer'] }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>

</x-dashboard>
