<x-dashboard>
    <div class="p-4 sm:ml-64">
        <div class="flex justify-between mx-5">
            <h2 class=" text-4xl font-bold text-salonPurple">Annual Sales Report</h2>
        </div>
        <div class="py-4 ml-5">
            <a href="{{ route('annual.report.pdf') }}" class="focus:outline-none text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900">
                Download Annual Sales Report PDF
            </a>
        </div>
        <div class="py-2 ml-5">
            <a href="{{ route('all.annual.report.pdf') }}" class="focus:outline-none text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900">
                Download All Annual Sales Report PDF
            </a>
        </div>
        <div class="overflow-auto rounded-lg border border-gray-200 shadow-md m-5">
            <table class="w-full border-collapse bg-white text-center text-lg text-gray-950">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2">Year</th>
                        <th class="px-4 py-2">Total Sales</th>
                        <th class="px-4 py-2">Appointment Count</th>
                        <th class="px-4 py-2">Services Count</th>
                        <th class="px-4 py-2">Services</th>
                        <th class="px-4 py-2">Prices</th>
                        <th class="px-4 py-2">Employees</th>
                        <th class="px-4 py-2">Customers</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reports as $report)
                        <tr>
                            <td class="px-4 py-2">{{ $report->year }}</td>
                            <td class="px-4 py-2">₱{{ number_format($report->total_sales, 2) }}</td>
                            <td class="px-4 py-2">{{ $report->appointment_count }}</td>
                            <td class="px-4 py-2">{{ $report->services_count }}</td>
                            <td class="px-4 py-2">
                                @foreach (explode(',', $report->services) as $service)
                                    <p>{{ $service }}</p>
                                @endforeach
                            </td>
                            <td class="px-4 py-2">
                                @foreach (explode(',', $report->prices) as $price)
                                    <p>₱{{ number_format($price, 2) }}</p>
                                @endforeach
                            </td>
                            <td class="px-4 py-2">
                                @foreach (explode(',', $report->employees) as $employee)
                                    <p>{{ $employee }}</p>
                                @endforeach
                            </td>
                            <td class="px-4 py-2">
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

