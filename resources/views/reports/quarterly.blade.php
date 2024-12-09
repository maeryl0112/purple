<x-dashboard>
    <div class="p-4 sm:ml-64">
        <div class="flex justify-between mx-5">
            <h2 class="text-4xl font-bold text-salonPurple">Quarterly Sales Report</h2>
        </div>

        <!-- Filters -->
        <div class="py-4 ml-5">
            <form action="{{ route('quarterly.report') }}" method="GET" class="flex items-center space-x-4">
                <div>
                    <label for="year" class="block text-gray-700 font-medium">Select Year:</label>
                    <select id="year" name="year" class="border-gray-300 rounded-md shadow-sm" onchange="this.form.submit()">
                        @foreach (range(date('Y'), date('Y') - 5) as $year)
                            <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="quarter" class="block text-gray-700 font-medium">Select Quarter:</label>
                    <select id="quarter" name="quarter" class="border-gray-300 rounded-md shadow-sm" onchange="this.form.submit()">
                        @foreach ([1, 2, 3, 4] as $quarter)
                            <option value="{{ $quarter }}" {{ $selectedQuarter == $quarter ? 'selected' : '' }}>Q{{ $quarter }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        <!-- Data Table -->
        <div class="overflow-auto rounded-lg border border-gray-200 shadow-md m-5">
            <table class="w-full border-collapse bg-white text-center text-sm text-gray-950">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2">Quarter</th>
                        <th class="px-4 py-2">Total Sales</th>
                        <th class="px-4 py-2">Appointment Count</th>
                        <th class="px-4 py-2">Service Count</th>
                        <th class="px-4 py-2">Services</th>
                        <th class="px-4 py-2">Prices</th>
                        <th class="px-4 py-2">Employees</th>
                        <th class="px-4 py-2">Customers</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse ($reports as $report)
                        <tr class="even:bg-gray-100">
                            <td rowspan="{{ $report->maxRows }}" class="align-top px-4 py-2">
                                {{ $report->quarter_label }}
                            </td>
                            <td rowspan="{{ $report->maxRows }}" class="align-top px-4 py-2 {{ $report->total_sales > 100000 ? 'text-green-500 font-bold' : '' }}">
                                ₱ {{ number_format($report->total_sales, 2) }}
                            </td>
                            <td rowspan="{{ $report->maxRows }}" class="align-top px-4 py-2">
                                {{ $report->appointment_count }}
                            </td>
                            <td rowspan="{{ $report->maxRows }}" class="align-top px-4 py-2">
                                {{ $report->service_count }}
                            </td>
                            @php
                                $services = explode(',', $report->services);
                                $prices = explode(',', $report->prices);
                                $employees = explode(',', $report->employees);
                                $customers = explode(',', $report->customers);
                            @endphp
                            <td class="px-4 py-2">{{ $services[0] ?? 'N/A' }}</td>
                            <td class="px-4 py-2">₱ {{ $prices[0] ?? 'N/A' }}</td>
                            <td class="px-4 py-2">{{ $employees[0] ?? 'N/A' }}</td>
                            <td class="px-4 py-2">{{ $customers[0] ?? 'N/A' }}</td>
                        </tr>
                        @for ($i = 1; $i < $report->maxRows; $i++)
                            <tr class="even:bg-gray-100">
                                <td class="px-4 py-2">{{ $services[$i] ?? 'N/A' }}</td>
                                <td class="px-4 py-2">₱ {{ $prices[$i] ?? 'N/A' }}</td>
                                <td class="px-4 py-2">{{ $employees[$i] ?? 'N/A' }}</td>
                                <td class="px-4 py-2">{{ $customers[$i] ?? 'N/A' }}</td>
                            </tr>
                        @endfor
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-2 text-center">
                                No data found for the year {{ $selectedYear }} and Q{{ $selectedQuarter }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <a href="{{route('quarterly.report.pdf')}}" type="button" class="focus:outline-none text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 mx-5 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900">Download This Quarter Sales Report</a>

    </div>
</x-dashboard>
