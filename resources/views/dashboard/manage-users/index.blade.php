<x-dashboard>
    <div class="p-4 sm:ml-64">
        <div class="flex justify-between items-center mx-7 mb-4">
            <button>
                <a href="{{ route('manageusers.create') }}" class="px-5 py-2 text-white text-sm bg-salonPurple rounded-md hover:bg-darkPurple">Add User</a>
            </button>
        </div>
        <div x-data="{showModal:false}">
            <div class="overflow-hidden rounded-lg border border-gray-200 shadow-md m-5 bg-white">

                <!-- Filters and Search -->
                <div class="flex flex-wrap items-center justify-between gap-4 p-4 bg-gray-50 border-b border-gray-200">
                    <!-- Search Form -->
                    <form class="flex-1" action="{{ route('manageusers') }}">
                        <div class="relative">
                            <input
                                type="search"
                                name="search"
                                value="{{ $search }}"
                                placeholder="Search Users..."
                                class="block w-full p-4 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-purple-500 focus:border-purple-500"
                            />
                            <button type="submit" class="text-white absolute right-2.5 bottom-2.5 bg-purple-600 hover:bg-purple-700 focus:ring-4 focus:outline-none focus:ring-purple-300 font-medium rounded-lg text-sm px-4 py-2">
                                Search
                            </button>
                        </div>
                    </form>

                    <!-- Role Filter -->
                    <form method="GET" action="{{ route('manageusers') }}" class="flex items-center gap-4">
                        <select name="role" class="border rounded px-4 py-2 text-sm">
                            <option value="">All Roles</option>
                            <option value="employee" {{ request('role') == 'employee' ? 'selected' : '' }}>Employee</option>
                            <option value="customer" {{ request('role') == 'customer' ? 'selected' : '' }}>Customer</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                            Filter
                        </button>
                    </form>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm text-gray-700">
                        <thead class="bg-gray-100 text-gray-900">
                            <tr>
                                <th class="px-6 py-3 text-left font-medium">Id</th>
                                <th class="px-6 py-3 text-left font-medium">User</th>
                                <th class="px-6 py-3 text-left font-medium">Status</th>
                                <th class="px-6 py-3 text-left font-medium">Role</th>
                                <th class="px-6 py-3 text-left font-medium">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($users as $user)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">{{ $user->id }}</td>
                                    <td class="px-6 py-4 flex items-center gap-3">
                                        <img
                                            src="{{ $user->profile_photo_url }}"
                                            alt="{{ $user->name }}"
                                            class="h-10 w-10 rounded-full border"
                                        />
                                        <div>
                                            <div class="font-medium text-gray-700">{{ $user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full
                                                {{ $user->status ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' }}"
                                        >
                                            <span
                                                class="h-2 w-2 rounded-full {{ $user->status ? 'bg-green-600' : 'bg-red-600' }} mr-1"
                                            ></span>
                                            {{ $user->status ? 'Active' : 'Suspended' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">{{ $user->role->name }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex gap-2">
                                            @if ($user->role->name != 'Admin')
                                                <form action="{{ $user->status ? route('manageusers.suspend', $user->id) : route('manageusers.activate', $user->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit"
                                                        class="{{ $user->status ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900' }} text-sm">
                                                        {{ $user->status ? 'Suspend' : 'Activate' }}
                                                    </button>
                                                </form>
                                            @endif
                                            @if ($user->role->name == 'Customer')
                                                <a href="{{ route('users.show', $user->id) }}" class="text-blue-600 hover:text-blue-900 text-sm">
                                                    View
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="p-5">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-dashboard>
