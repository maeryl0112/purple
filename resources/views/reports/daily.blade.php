        <x-dashboard>
        <div class="p-4 sm:ml-64">
            <div class="flex justify-between mx-5">
                <h2 class="text-4xl font-bold text-salonPurple">Daily Sales Report</h2>
            </div>

            <!-- Date Filter -->
            <div class="py-4 ml-5">
        
     
        <form method="GET" action="{{ route('daily.report') }}">
    <label for="date">Select Date:</label>
    <input type="date" name="date" value="{{ $selectedDate }}" class="border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500">

    <label for="branch">Select Branch:</label>
    <select name="branch_id" class="border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500" >
        <option value="">All Branches</option>
        @foreach ($branches as $branch)
            <option value="{{ $branch->id }}" {{ $selectedBranch == $branch->id ? 'selected' : '' }}>
                {{ $branch->name }}
            </option>
        @endforeach
    </select>

    <button type="submit" class="bg-blue-500 text-white px-4 py-2">Filter</button>
</form>
</div>  


            <!-- Data Table -->
            <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-md m-5">
                <table class="w-full border-collapse bg-white text-lg text-gray-950 border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 border border-gray-300 text-left">Date</th>
                            <th class="px-4 py-2 border border-gray-300 text-left">Service Name</th>
                            <th class="px-4 py-2 border border-gray-300 text-right">Sales</th>
                        </tr>
                    </thead>
                    <tbody>
    @forelse ($reports as $report)
        @php
            $dailyTotal = 0;
        @endphp

        <tr>
            <td class="px-4 py-2 border border-gray-300 font-bold text-left align-top" rowspan="{{ $report->grouped_services->count() + 1 }}">
                {{ $report->date }}
            </td>
        </tr>

        @foreach ($report->grouped_services as $serviceName => $group)
            @php
                $dailyTotal += $group['total_price'];
            @endphp
            <tr>
                <td class="px-4 py-2 border border-gray-300">{{ $serviceName }}</td>
                <td class="px-4 py-2 border border-gray-300 text-right">₱{{ number_format($group['total_price'], 2) }}</td>
            </tr>
        @endforeach

        <tr>
            <td class="px-4 py-2 border border-gray-300 font-bold text-right" colspan="2">Total Sales:</td>
            <td class="px-4 py-2 border border-gray-300 font-bold text-right">₱{{ number_format($dailyTotal, 2) }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="3" class="px-4 py-2 border border-gray-300 text-center">No sales data found for the selected date.</td>
        </tr>
    @endforelse
</tbody>

                </table>
            </div>

            <!-- Download Buttons -->
            <div class="flex flex-col items-start space-y-4 mx-5">
                <a href="{{ route('daily.report.pdf') }}" 
                    class="focus:outline-none text-white bg-salonPurple hover:bg-darkPurple focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5">
                    Download This Day Sales Report
                </a>
                <a href="{{ route('all.daily.report.pdf') }}" 
                    class="focus:outline-none text-white bg-salonPurple hover:bg-darkPurple focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5">
                    Download All
                </a>
            </div>
        </div>
    </x-dashboard>
