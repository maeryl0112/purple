    <x-dashboard>
    <div class="p-4 sm:ml-64">

    <div class="p-4">
        <ul class="flex">
            <li id="homeTab"
                class="tab text-blue-600 font-bold text-base text-center bg-gray-50 py-3 px-6 border-b-2 border-blue-600 cursor-pointer transition-all">
                Equipment Notifications
            </li>
            <li id="contentTab"
                class="tab text-gray-600 font-semibold text-base text-center hover:bg-gray-50 py-3 px-6 border-b-2 cursor-pointer transition-all">
                Consumables Notifications
            </li>
        </ul>

        <!-- Equipment Notifications -->
        <div id="homeContent" class="tab-content max-w-2xl block mt-8">
            <h4 class="text-lg font-bold text-gray-600">Equipment Notifications</h4>
            <!-- Dropdown for Filtering -->
            <div class="mb-4">
                <label for="equipmentFilter" class="block text-sm font-medium text-gray-700">Filter By</label>
                <select id="equipmentFilter" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="all">All</option>
                    <option value="low_quantity">Low Quantity</option>
                    <option value="expiration_date">Maintenance Due</option>
                </select>
            </div>

            <!-- Dropdown for Branch Filtering -->
            <div class="mb-4">
    <label for="equipmentBranchFilter" class="block text-sm font-medium text-gray-700">Filter By Branch</label>
    <select id="equipmentBranchFilter" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        <option value="all">All Branches</option>
        @foreach ($branches as $branch)
            <option value="{{ $branch->name }}">{{ $branch->name }}</option>
        @endforeach
    </select>
</div>


            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table id="equipmentTable" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Type</th>
                            <th scope="col" class="px-6 py-3">Branch</th>
                            <th scope="col" class="px-6 py-3">Inventory Name</th>
                            <th scope="col" class="px-6 py-3">Message</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($equipmentNotifications as $notification)
                        <tr class="equipment-row bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                            data-type="{{ $notification->data['type'] }}"  data-branch="{{ $notification->data['equipment_branch'] }}">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $notification->data["type"] }}
                            </th>
                                <td class="px-6 py-4">
                                {{ $notification->data['equipment_branch'] }}   
                                </th>
                            <td class="px-6 py-4">
                            {{ $notification->data["equipment_name"] }}
                            </td>
                            <td class="px-6 py-4">
                            {{ $notification->data["message"] }}
                            </td>        
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Consumables Notifications -->
        <div id="contentContent" class="tab-content max-w-2xl hidden mt-8">
            <h4 class="text-lg font-bold text-gray-600">Consumables Notifications</h4>
            <!-- Dropdown for Filtering -->
            <div class="mb-4">
                <label for="supplyFilter" class="block text-sm font-medium text-gray-700">Filter By</label>
                <select id="supplyFilter" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="all">All</option>
                    <option value="low_quantity">Low Quantity</option>
                    <option value="expiration_date">Expiration DUe</option>
                </select>
            </div>

            <!-- Dropdown for Branch Filtering -->
            <div class="mb-4">
    <label for="supplyBranchFilter" class="block text-sm font-medium text-gray-700">Filter By Branch</label>
    <select id="supplyBranchFilter" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        <option value="all">All Branches</option>
        @foreach ($branches as $branch)
            <option value="{{ $branch->name }}">{{ $branch->name }}</option>
        @endforeach
    </select>
</div>


            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table id="supplyTable" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Type</th>
                            <th scope="col" class="px-6 py-3">Branch</th>
                            <th scope="col" class="px-6 py-3">Inventory Name</th>
                            <th scope="col" class="px-6 py-3">Message</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($supplyNotifications as $notification)
                        <tr class="supply-row bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                            data-type="{{ $notification->data['type'] }}" data-branch="{{ $notification->data['supply_branch'] }}">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $notification->data["type"] }}
                            </th>
                            <td class="px-6 py-4">
                            {{ $notification->data["supply_branch"] }}
                            </td>
                            <td class="px-6 py-4">
                            {{ $notification->data["supply_name"] }}
                            </td>
                            <td class="px-6 py-4">
                            {{ $notification->data["message"] }}
                            </td>        
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Tab functionality
            let tabs = document.querySelectorAll('.tab');
            let contents = document.querySelectorAll('.tab-content');

            tabs.forEach(function (tab) {
                tab.addEventListener('click', function () {
                    let targetId = tab.id.replace('Tab', 'Content');

                    // Hide all content divs
                    contents.forEach(function (content) {
                        content.classList.add('hidden');
                    });

                    // Remove active class from all tabs
                    tabs.forEach(function (tab) {
                        tab.classList.remove('text-blue-600', 'font-bold', 'bg-gray-50', 'border-blue-600');
                        tab.classList.add('text-gray-600', 'font-semibold');
                    });

                    // Show the target content
                    document.getElementById(targetId).classList.remove('hidden');

                    // Add active class to the clicked tab
                    tab.classList.add('text-blue-600', 'font-bold', 'bg-gray-50', 'border-blue-600');
                    tab.classList.remove('text-gray-600', 'font-semibold');
                });
            });

  
       
            document.getElementById('equipmentFilter').addEventListener('change', function () {
                let filter = this.value;
                let rows = document.querySelectorAll('.equipment-row');

                rows.forEach(function (row) {
                    if (filter === 'all' || row.dataset.type === filter) {
                        row.classList.remove('hidden');
                    } else {
                        row.classList.add('hidden');
                    }
                });
            });

            // Supply filter functionality
            document.getElementById('supplyFilter').addEventListener('change', function () {
                let filter = this.value;
                let rows = document.querySelectorAll('.supply-row');

                rows.forEach(function (row) {
                    if (filter === 'all' || row.dataset.type === filter) {
                        row.classList.remove('hidden');
                    } else {
                        row.classList.add('hidden');
                    }
                });
            });

     
                // Equipment Branch filter functionality
            document.getElementById('equipmentBranchFilter').addEventListener('change', function () {
                let branchFilter = this.value;
                let rows = document.querySelectorAll('.equipment-row');

                rows.forEach(function (row) {
                    let rowBranch = row.getAttribute('data-branch');
                    if (branchFilter === 'all' || rowBranch === branchFilter) {
                        row.classList.remove('hidden');
                    } else {
                        row.classList.add('hidden');
                    }
                });
            });
            // Supply Branch filter functionality
            document.getElementById('supplyBranchFilter').addEventListener('change', function () {
            let branchFilter = this.value;
            let rows = document.querySelectorAll('.supply-row');

            rows.forEach(function (row) {
                let rowBranch = row.getAttribute('data-branch'); // FIXED: Use data-branch
                if (branchFilter === 'all' || rowBranch === branchFilter) {
                    row.classList.remove('hidden');
                } else {
                    row.classList.add('hidden');
                }
            });
        });


        });
    </script>




    </div>
    </x-dashboard>
