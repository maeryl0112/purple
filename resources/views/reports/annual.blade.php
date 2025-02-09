<x-dashboard>
    <div class="p-4 sm:ml-64">
        <div class="flex justify-between mx-5">
            <h2 class="text-4xl font-bold text-salonPurple">Annual Sales Report</h2>
        </div>

        <!-- Year Filter -->
        <div class="py-4 ml-5">
            <form method="GET" action="{{ route('annual.report') }}">
                <label for="year">Select Year:</label>
                <select name="year" class="border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500">
                    @foreach (range(date('Y'), date('Y') - 10) as $year) {{-- Show last 10 years --}}
                        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>

                <label for="branch">Select Branch:</label>
                <select
                    name="branch_id"
                    id="branch_id"
                    class="mt-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                    @if (!empty($branches) && $branches->count())
                        <option value="" {{ $selectedBranch === null ? 'selected' : '' }}>All Branches</option>
                        @foreach ($branches as $branch)
                            <option
                                value="{{ $branch->id }}"
                                {{ $selectedBranch == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    @else
                        <option value="" disabled>No branches available</option>
                    @endif
                </select>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2">Filter</button>
            </form>
        </div>

        <!-- Data Table -->
        <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-md m-5">
            <table class="w-full border-collapse bg-white text-lg text-gray-950 border border-gray-300">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border border-gray-300 text-left">Year</th>
                        <th class="px-4 py-2 border border-gray-300 text-left">Branch</th>
                        <th class="px-4 py-2 border border-gray-300 text-left">Service Name</th>
                        <th class="px-4 py-2 border border-gray-300 text-right">Sales</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reports as $report)
                        @php
                            $annualTotal = 0;
                        @endphp

                        <tr>
                            <td class="px-4 py-2 border border-gray-300 font-bold text-left align-top" rowspan="{{ $report->grouped_services->count() + 1 }}">
                                {{ $report->year }}
                            </td>
                            <td class="px-4 py-2 border border-gray-300 font-bold text-left align-top" rowspan="{{ $report->grouped_services->count() + 1 }}">
                            {{ $selectedBranch ? $branches->firstWhere('id', $selectedBranch)->name : 'All Branches' }}
                        </td>
                        </tr>

                        @foreach ($report->grouped_services as $serviceName => $group)
                            @php
                                $annualTotal += $group['total_price'];
                            @endphp
                            <tr>
                                <td class="px-4 py-2 border border-gray-300">{{ $serviceName }}</td>
                                <td class="px-4 py-2 border border-gray-300 text-right">₱{{ number_format($group['total_price'], 2) }}</td>
                            </tr>
                        @endforeach

                        <tr>
                            <td class="px-4 py-2 border border-gray-300 font-bold text-right" colspan="3">Total Sales:</td>
                            <td class="px-4 py-2 border border-gray-300 font-bold text-right">₱{{ number_format($annualTotal, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-2 border border-gray-300 text-center">No sales data found for the selected year.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Download Buttons -->
        <div class="flex flex-col items-start space-y-4 mx-5">
            <a href="{{ route('annual.report.pdf') }}" 
                class="focus:outline-none text-white bg-salonPurple hover:bg-darkPurple focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5">
                Download This Year's Sales Report
            </a>
            <a href="{{ route('all.annual.report.pdf') }}" 
                class="focus:outline-none text-white bg-salonPurple hover:bg-darkPurple focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5">
                Download All Annual Reports
            </a>
        </div>
    </div>
</x-dashboard>
