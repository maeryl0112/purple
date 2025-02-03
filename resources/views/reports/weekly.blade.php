<x-dashboard>
    <div class="p-4 sm:ml-64">
        <div class="flex justify-between mx-5">
            <h2 class="text-4xl font-bold text-salonPurple">Weekly Sales Report</h2>
           
        </div>

        <!-- Week & Branch Filter -->
        <div class="py-4 ml-5 flex space-x-4">
            <form action="{{ route('weekly.report') }}" method="GET" id="week-filter-form">
                <label for="week" class="mr-2">Select Week:</label>
                <input
                    type="week"
                    id="week"
                    name="week"
                    value="{{ $selectedWeek }}"
                    class="border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500"
                >
            </form>

            <form action="{{ route('weekly.report') }}" method="GET" id="branch-filter-form">
                <label for="branch" class="mr-2">Select Branch:</label>
                <select name="branch_id" id="branch" class="border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                    <option value="">All Branches</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}" {{ $selectedBranch == $branch->id ? 'selected' : '' }}>
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        <!-- Data Table -->
        <div class="overflow-auto rounded-lg border border-gray-200 shadow-md m-5">
            <table class="w-full border-collapse bg-white text-center text-lg text-gray-950 overflow-x-scroll min-w-screen">
                <thead class="bg-gray-100">
                    <tr>
                        <th>Week</th>
                        <th>Total Sales</th>
                        <th>Service Name</th>
                        <th>Service Sales</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reports as $report)
                        <tr>
                            <td>
                                {{ \Carbon\Carbon::parse($selectedWeek)->startOfWeek()->format('M d, Y') }} - 
                                {{ \Carbon\Carbon::parse($selectedWeek)->endOfWeek()->format('M d, Y') }}
                            </td>
                            <td>₱{{ number_format($report->total_sales, 2) }}</td>
                            <td>
                                @foreach ($report->grouped_services as $service => $details)
                                    <p>{{ $service }}</p>
                                @endforeach
                            </td>
                            <td>
                                @foreach ($report->grouped_services as $details)
                                    <p>₱{{ number_format($details['total_price'], 2) }}</p>
                                @endforeach
                            </td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 border border-gray-300 font-bold text-right" colspan="3">Total Sales:</td>
                            <td class="px-4 py-2 border border-gray-300 font-bold text-right">₱{{ number_format($grandTotal, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">No sales data found for the selected week.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Download Buttons -->
        <div class="flex flex-col items-start space-y-4 mx-5">
            <a href="{{ route('weekly.report.pdf') }}" 
                class="focus:outline-none text-white bg-salonPurple hover:bg-darkPurple focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900">
                Download This Weekly Sales Report
            </a>
            <a href="{{ route('all.weekly.report.pdf') }}" 
                class="focus:outline-none text-white bg-salonPurple hover:bg-darkPurple focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900">
                Download All
            </a>
        </div>
    </div>
</x-dashboard>

<!-- JavaScript -->
<script>
    document.getElementById('week').addEventListener('change', function() {
        document.getElementById('week-filter-form').submit();
    });

    document.getElementById('branch').addEventListener('change', function() {
        document.getElementById('branch-filter-form').submit();
    });
</script>
