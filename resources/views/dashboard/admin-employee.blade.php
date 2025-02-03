@php
    use App\Enums\UserRolesEnum;
    $role = UserRolesEnum::from(Auth::user()->role_id)->name;
@endphp
    <x-dashboard>



        <!--         <div class="fixed top-5 right-4 z-50 space-y-4">
            @foreach ($nearExpirationSupplies as $supply)
                <div x-data="{ show: true }" x-show="show" x-transition
                    id="alert-expiration-{{ $supply->id }}"
                    class="p-4 mb-4 text-red-800 border border-red-300 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 dark:border-red-800 shadow-lg"
                    role="alert">
                <div class="flex items-center">
                    <svg class="flex-shrink-0 w-4 h-4 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                    </svg>
                    <span class="sr-only">Info</span>
                    <h3 class="text-lg font-medium">Warning: Expiring Soon</h3>
                </div>
                <div class="mt-2 mb-4 text-sm">
                    Supply item <strong>{{ $supply->name }}</strong> from supplier is nearing its expiration date on <strong>{{ $supply->expiration_date }}</strong>. Please review and reorder if necessary.
                </div>
                <div class="flex">
                    <a href="{{ route('managesupplies') }}?search={{ $supply->name }}" class="text-white bg-red-800 hover:bg-red-900 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-xs px-3 py-1.5 me-2 text-center inline-flex items-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
                    <svg class="me-2 h-3 w-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 14">
                        <path d="M10 0C4.612 0 0 5.336 0 7c0 1.742 3.546 7 10 7 6.454 0 10-5.258 10-7 0-1.664-4.612-7-10-7Zm0 10a3 3 0 1 1 0-6 3 3 0 0 1 0 6Z"/>
                    </svg>
                    View Supply
                    </a>
                    <button @click="show = false"
                            type="button"
                            class="text-red-800 bg-transparent border border-red-800 hover:bg-red-900 hover:text-white focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-xs px-3 py-1.5 text-center dark:hover:bg-red-600 dark:border-red-600 dark:text-red-500 dark:hover:text-white dark:focus:ring-red-800"
                            aria-label="Close">
                    Dismiss
                    </button>
                </div>
                </div>
            @endforeach
            @foreach ($lowQuantitySupplies as $supply)
            <div x-data="{ show: true }" x-show="show" x-transition
                id="alert-low-quantity-{{ $supply->id }}"
                class="p-4 mb-4 text-yellow-800 border border-yellow-300 rounded-lg bg-yellow-50 dark:bg-gray-800 dark:text-yellow-400 dark:border-yellow-800 shadow-lg"
                role="alert">
                <div class="flex items-center">
                    <svg class="flex-shrink-0 w-4 h-4 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 0C4.612 0 0 5.336 0 7c0 1.742 3.546 7 10 7 6.454 0 10-5.258 10-7 0-1.664-4.612-7-10-7Zm0 10a3 3 0 1 1 0-6 3 3 0 0 1 0 6Z"/>
                    </svg>
                    <span class="sr-only">Info</span>
                    <h3 class="text-lg font-medium">Warning: Low Quantity</h3>
                </div>
                <div class="mt-2 mb-4 text-sm">
                    Supply item <strong>{{ $supply->name }}</strong> has low quantity: <strong>{{ $supply->quantity }}</strong>. Please review and reorder if necessary.
                </div>
                <div class="flex">
                    <a href="{{ route('managesupplies') }}?search={{ $supply->name }}" class="text-white bg-yellow-800 hover:bg-yellow-900 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-xs px-3 py-1.5 me-2 text-center inline-flex items-center dark:bg-yellow-600 dark:hover:bg-yellow-700 dark:focus:ring-yellow-800">
                        <svg class="me-2 h-3 w-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 14">
                            <path d="M10 0C4.612 0 0 5.336 0 7c0 1.742 3.546 7 10 7 6.454 0 10-5.258 10-7 0-1.664-4.612-7-10-7Zm0 10a3 3 0 1 1 0-6 3 3 0 0 1 0 6Z"/>
                        </svg>
                        View Supply
                    </a>
                    <button @click="show = false"
                            type="button"
                            class="text-yellow-800 bg-transparent border border-yellow-800 hover:bg-yellow-900 hover:text-white focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-xs px-3 py-1.5 text-center dark:hover:bg-yellow-600 dark:border-yellow-600 dark:text-yellow-500 dark:hover:text-white dark:focus:ring-yellow-800"
                            aria-label="Close">
                        Dismiss
                    </button>
                </div>
            </div>
        @endforeach
        </div> -->

        <script>
            function updateDateTime() {
                const now = new Date();
                const options = {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                };
                document.getElementById('dateTime').innerText = now.toLocaleString('en-US', options);
            }

            setInterval(updateDateTime, 1000); // Update every second
            updateDateTime(); // Initialize immediately
        </script>

    <div class="p-2 sm:ml-64">
        <div class="flex justify-between mx-7">
            <h2 class="text-2xl font-bold text-salonPurple">DASHBOARD</h2>

            <div id="dateTime" class="px-5 py-2 text-salonPurple text-m"></div>
        </div>
        <div class="p-4 rounded-lg dark:border-gray-700">

        <div class="grid grid-cols-3 gap-4 mb-4">
            <div class="bg-salonPurple  shadow-lg rounded-md flex items-center justify-between p-3 border-b-4 border-purple-400  text-white font-medium group">
                <div class="flex justify-center items-center w-14 h-14 bg-white rounded-full transition-all duration-300 transform group-hover:rotate-12">
                <svg width="30" height="30" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="stroke-current text-purple-800  transform transition-transform duration-500 ease-in-out"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <div class="text-right">
                <p class="text-2xl">{{ $totalCustomers }}</p>
                <p>Customers</p>
                </div>
            </div>
            <div class="bg-salonPurple  shadow-lg rounded-md flex items-center justify-between p-3 border-b-4 border-purple-400  text-white font-medium group">
                <div class="flex justify-center items-center w-14 h-14 bg-white rounded-full transition-all duration-300 transform group-hover:rotate-12">
                <svg width="30" height="30" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="stroke-current text-purple-800  transform transition-transform duration-500 ease-in-out"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <div class="text-right">
                <p class="text-2xl">{{ $totalEmployees }}</p>
                <p>Employees</p>
                </div>
            </div>
            <div class="bg-salonPurple  shadow-lg rounded-md flex items-center justify-between p-3 border-b-4 border-purple-400  text-white font-medium group">
                <div class="flex justify-center items-center w-14 h-14 bg-white rounded-full transition-all duration-300 transform group-hover:rotate-12">
                <svg width="30" height="30" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="stroke-current text-purple-800  transform transition-transform duration-500 ease-in-out"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                </div>
                <div class="text-right">
                <p class="text-2xl">{{ $totalServices }}</p>
                <p>Services</p>
                </div>
            </div>

        </div>

        <div class="grid grid-cols-3 gap-4 mb-4">
                <div class="bg-salonPurple  shadow-lg rounded-md flex items-center justify-between p-3 border-b-4 border-purple-400  text-white font-medium group">
                    <div class="flex justify-center items-center w-14 h-14 bg-white rounded-full transition-all duration-300 transform group-hover:rotate-12">
                    <svg width="30" height="30" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="stroke-current text-purple-800  transform transition-transform duration-500 ease-in-out"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    </div>
                    <div class="text-right">
                    <p class="text-2xl">{{ $totalServicesActive }}</p>
                    <p>Active Services</p>
                    </div>
                </div>
                <div class="bg-salonPurple  shadow-lg rounded-md flex items-center justify-between p-3 border-b-4 border-purple-400  text-white font-medium group">
                    <div class="flex justify-center items-center w-14 h-14 bg-white rounded-full transition-all duration-300 transform group-hover:rotate-12">
                        <svg width="30" height="30" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="stroke-current text-purple-800  transform transition-transform duration-500 ease-in-out"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl">{{ $totalUpcomingAppointments }}</p>
                        <p>Upcoming Appointments</p>
                    </div>
                </div>
            <div class="bg-salonPurple  shadow-lg rounded-md flex items-center justify-between p-3 border-b-4 border-purple-400  text-white font-medium group">
                <div class="flex justify-center items-center w-14 h-14 bg-white rounded-full transition-all duration-300 transform group-hover:rotate-12">
                    <svg width="30" height="30" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="stroke-current text-purple-800  transform transition-transform duration-500 ease-in-out"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </div>
                <div class="text-right">
                    <p class="text-2xl">{{ $totalCompletedAppointments }}</p>
                    <p>Completed Appointments</p>
                </div>
                </div>
        </div>


        </div>

            
       
    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm 2xl:col-span-2 dark:border-gray-700 sm:p-6 dark:bg-gray-800">
        <div id="serviceCategoryRevenueChart"></div>
        <a href="{{ route('category.pdf') }}" class="focus:outline-none text-white bg-salonPurple hover:bg-secondary focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-salonPurple dark:hover:bg-purple-700 dark:focus:ring-purple-900">Download PDF</a>


        <script>
            // Data from Laravel
            const serviceCategoryRevenue = @json($serviceCategoryRevenue);

            // Prepare data for the chart
            const categories = serviceCategoryRevenue.map(item => item.category_name);
            const revenues = serviceCategoryRevenue.map(item => parseFloat(item.total_revenue));

            // Configure and render the chart
            const options = {
                chart: { type: 'bar', height: 350 },
                series: [{ name: 'Sales', data: revenues }],
                xaxis: { categories },
                yaxis: { title: { text: 'Sales (₱)' } },
                title: { text: 'Sales by Service Category', align: 'center' },
                colors: ['#6a2695'], // Customize bar color
                tooltip: { y: { formatter: value => `₱${value.toFixed(2)}` } }
            };

            const chart = new ApexCharts(document.querySelector("#serviceCategoryRevenueChart"), options);
            chart.render();
        </script>


    </div>
       <div class="pt-4 grid grid-cols-2 gap-4">
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="flex-shrink-0">
                    <span class="text-xl font-bold leading-none text-gray-900 sm:text-2xl dark:text-white">
                        ₱{{ number_format($totals->sum(), 2) }}
                    </span>
                    <h3 class="text-base font-light text-gray-500 dark:text-gray-400">Sales Every Month</h3>
                </div>
                <a href="{{route('monthly.report')}}" class="px-4 py-2 text-sm font-medium text-white bg-salonPurple rounded-lg hover:bg-secondary">
                    View Details
                </a>
            </div>
            <div id="line-chart"></div>
        </div>

    <script>
      document.addEventListener("DOMContentLoaded", function () {
        const months = @json($months); // Your months data (e.g., ['January', 'February', ...])
        const totals = @json($totals); // Your totals data (e.g., [500, 1000, ...])

        const options = {
            chart: {
                type: "area", // For the gradient-filled line
                height: 350,
                toolbar: {
                    show: false,
                },
            },
            dataLabels: {
                enabled: false, // Disable point labels for a clean look
            },
            stroke: {
                curve: "smooth", // Smooth curves
                width: 3,
            },
            series: [
                {
                    name: "Sales",
                    data: totals,
                },
            ],
            xaxis: {
                categories: months,
                labels: {
                    rotate: -45,
                },
            },
            yaxis: {
                labels: {
                    formatter: function (value) {
                        return "₱" + value.toFixed(2);
                    },
                },
            },
           fill: {
                type: "gradient",
                gradient: {
                    shadeIntensity: 1,
                    gradientToColors: ["#6a2695"], // Light purple at the bottom
                    opacityFrom: 0.7,
                    opacityTo: 0,
                    stops: [0, 90, 100],
                },
            },
            colors: ["#6a2695"], // Custom color for the line
            tooltip: {
                theme: "dark",
                y: {
                    formatter: function (value) {
                        return "₱" + value.toFixed(2);
                    },
                },
            },
        };

        const chart = new ApexCharts(document.querySelector("#line-chart"), options);
        chart.render();
    });

        </script>

<div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
    <h3 class="flex items-center mb-4 text-lg font-semibold text-gray-900 dark:text-white">Top Customers
    <button data-popover-target="popover-description" data-popover-placement="bottom-end" type="button"><svg class="w-4 h-4 ml-2 text-gray-400 hover:text-gray-500" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg><span class="sr-only">Show information</span></button>
</h3>
<a href="{{ route('top.customer.pdf') }}" class="px-4 py-2 text-sm font-medium text-white bg-salonPurple rounded-lg hover:bg-secondary">Download Pdf</a>

    <div data-popover id="popover-description" role="tooltip" class="absolute z-10 invisible inline-block text-sm font-light text-gray-500 transition-opacity duration-300 bg-white border border-gray-200 rounded-lg shadow-sm opacity-0 w-72 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400">
      <div class="p-3 space-y-2">
          <h3 class="font-semibold text-gray-900 dark:text-white">Top Customer</h3>
          <p>These are the loyal customers</p>
      </div>
      <div data-popper-arrow></div>
    </div>


    <div id="fullWidthTabContent" class="border-t border-gray-200 dark:border-gray-600">
        <div id="faq" role="tabpanel" aria-labelledby="faq-tab">
            <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach ($topCustomers as $customer)
                <li class="py-3 sm:py-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <img class="w-8 h-8 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($customer->name) }}" alt="{{ $customer->name }} image">
                        </div>
                        <div class="flex-1 min-w-0 ms-4">
                            <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                                {{ $customer->name }}
                            </p>
                            <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                                {{ $customer->email }}
                            </p>
                        </div>
                        <div class="inline-flex items-center text-base font-semibold text-gray-900 dark:text-white">
                            ₱{{ number_format($customer->total_revenue, 2) }}
                        </div>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
    </div>
    </div>
 </div>

 <script>
        
    // Select all tabs and content containers
    const tabs = document.querySelectorAll('.tab');
    const tabContents = document.querySelectorAll('.tab-content');

    // Add click event listeners to each tab
    tabs.forEach((tab, index) => {
        tab.addEventListener('click', () => {
            // Remove the 'active' class from all tabs
            tabs.forEach(t => t.classList.remove('font-bold', 'border-b-darkPurple', 'border-b-2'));
            
            // Hide all content sections
            tabContents.forEach(content => content.classList.add('hidden'));
            
            // Activate the clicked tab and show its content
            tab.classList.add('font-bold', 'border-b-darkPurple', 'border-b-2');
            tabContents[index].classList.remove('hidden');
        });
    });
</script>

</x-dashboard>
