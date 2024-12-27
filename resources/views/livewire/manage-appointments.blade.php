<div class="p-4 sm:ml-64">
        <div class="flex justify-between mx-7">
            <h2 class="text-2xl font-bold text-salonPurple">

                @if ($selectFilter == 'upcoming')
                        UPCOMING
                @elseif ($selectFilter == 'previous')
                        PREVIOUS
                @elseif ($selectFilter == 'cancelled')
                        CANCELLED
                @elseif ($selectFilter == 'completed')
                        COMPLETED
                @endif


                APPOINTMENTS</h2>


        </div>
        <div class="mt-4">
            @if (session()->has('message'))
                <div class="px-4 py-2 text-white bg-green-500 rounded-md">
                    {{ session('message') }}
                </div>
            @endif
        </div>

        <div class="overflow-auto rounded-lg border border-gray-200 shadow-md m-5">
    <div class="w-full m-4 flex flex-wrap items-center space-y-4 md:space-y-0 md:space-x-4">
        <!-- Search Input -->
        <div class="w-full md:w-1/3">
            <label for="default-search" class="sr-only">Search</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20" aria-hidden="true">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                    </svg>
                </div>
                <input 
                    type="search" 
                    wire:model="search" 
                    id="default-search" 
                    name="search" 
                    class="block w-full p-4 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-purple-500 focus:border-purple-500" 
                    placeholder="Search Appointments...">
            </div>
        </div>

        <!-- Date Filter -->
        <div class="w-full md:w-1/3">
            <label for="filterDate" class="sr-only">Filter by Date</label>
            <input 
                type="date" 
                wire:model="filterDate" 
                id="filterDate" 
                class="block w-full p-2.5 text-sm bg-gray-50 border border-gray-300 rounded-lg text-gray-900 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                placeholder="Select date">
        </div>

        <!-- Status Filter -->
        <div class="w-full md:w-1/4">
            <label for="selectFilter" class="sr-only">Filter by Status</label>
            <select 
                id="selectFilter" 
                wire:model="selectFilter" 
                class="block w-52 p-2.5 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                <option value="completed">Completed</option>
                <option value="upcoming">Upcoming</option>
                <option value="previous">Previous</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>

        <!-- Payment Filter (Conditional) -->
        @if ($selectFilter == 'completed')
        <div class="w-full md:w-1/4">
            <label for="paymentFilter" class="sr-only">Filter by Payment</label>
            <select 
                id="paymentFilter" 
                wire:model="paymentFilter" 
                class="block w-50 p-2.5 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                <option value="">All</option>
                <option value="cash">Cash</option>
                <option value="online">Online</option>
            </select>
        </div>
        @endif
    </div>


            <table class="w-full border-collapse bg-white text-left text-sm text-gray-500 overflow-x-scroll min-w-screen">
                <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="pl-6 py-4 font-bold text-gray-900">Number</th>
                    <th scope="col" class="px-6 py-4 font-bold text-gray-900">Service</th>
                    <th scope="col" class="px-6 py-4 font-bold text-gray-900">Date</th>
                    <th scope="col" class="px-6 py-4 font-bold text-gray-900">Time</th>
                    <th scope="col" class="px-6 py-4 font-bold text-gray-900">Staff Assigned</th>

                    @if (auth()->user()->role->name  == 'Admin' || auth()->user()->role->name  == 'Employee')

                    <th scope="col" class="px-6 py-4 font-bold text-gray-900">Customer</th>
                        <th scope="col" class="px-6 py-4 font-bold text-gray-900">Contact No</th>
                    @endif
                    <th scope="col" class="px-6 py-4 font-bold text-gray-900">Payment</th>
                    @if ($selectFilter == 'cancelled')
                    <th scope="col" class="px-6 py-4 font-bold text-gray-900">Reason</th>
                    @endif
                    <th scope="col" class="px-6 py-4 font-bold text-gray-900">Action</th>


                </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 border-t border-gray-100">
                @if($appointments->count() == 0)
                    <tr class="hover:bg-gray-50 text-center">
                        <td class="pl-6 py-4  max-w-0
                        " colspan="9">No Appointments Found</td>
                    </tr>
                @else
                @foreach ($appointments as $appointment)
                    <tr class="hover:bg-gray-50">
                        <td class="pl-6 py-4  max-w-0">{{ $appointment->id }}</td>

                        <td class="px-6 py-4 max-w-xs font-medium text-gray-700">{{ $appointment->service->name}}</td>
                        <td class="px-6 py-4 max-w-xs font-medium text-gray-700">{{ $appointment->date}}</td>
                        <td class="px-6 py-4 max-w-xs font-medium text-gray-700">
                            {{ \Carbon\Carbon::createFromFormat('H:i:s', $appointment->time)->format('h:i A') }}
                        </td>
                        <td class="px-6 py-4 max-w-xs font-medium text-gray-700">{{ $appointment->first_name}}</td>

                        @if (auth()->user()->role->name == 'Admin' || auth()->user()->role->name == 'Employee')
                            <td class="px-6 py-4 max-w-xs font-medium text-gray-700">{{ $appointment->user->name}}</td>
                            <td class="px-6 py-4 max-w-xs font-medium text-gray-700">{{ $appointment->user->phone_number}}</td>
                            <td class="px-6 py-4 max-w-xs font-medium text-gray-700">{{ $appointment->payment }} @if ($appointment->payment === 'online' && $appointment->last_four_digits)
                                <p>Proof of Payment: Last Four Digits - {{ $appointment->last_four_digits }}</p>
                            @endif</td>
                            @if ($selectFilter == 'cancelled')
                            <td class="px-6 py-4 max-w-xs font-medium text-gray-700">{{ $appointment->cancellation_reason}}</td>
                            @endif
                            @endif


                            <td class="px-6 py-4 gap-2">

                           
                                @if ($selectFilter == 'upcoming')

                                <button wire:click="openPaymentModal({{ $appointment->id }} )" class="text-white bg-gradient-to-r from-purple-500 via-purple-600 to-purple-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-purple-300 dark:focus:ring-purple-800 rounded-lg text-xs px-4 py-2 inline-flex items-center me-1 mb-2">



                                    Completed

                                </button>

                                <button
                                wire:click="openRescheduleModal({{ $appointment->id }})"
                                class="px-4 py-2 flex items-center bg-blue-600 text-white rounded-lg text-xs hover:bg-blue-700 focus:ring-2 focus:ring-blue-400">

                                Reschedule
                            </button>


                               {{--@php
                                                $appointmentTime = Carbon\Carbon::parse($appointment->date . ' ' . $appointment->time);
                                                $timeDifference = $appointmentTime->diffInHours(now());
                                            @endphp

                                            @if ($timeDifference > 12) <!-- Adjust to 24 if needed -->
                                                <x-danger-button wire:click="setAppointmentIdToCancel({{ $appointment->id }})" wire:loading.attr="disabled">
                                                    {{ __('Cancel') }}
                                                </x-danger-button>
                                            @else
                                                <button disabled class="text-gray-500 bg-gray-300 rounded-md px-4 py-2 cursor-not-allowed">
                                                    {{ __('Cannot Cancel') }}
                                                </button>
                                            @endif --}}

                                @endif
                        </td>
                    </tr>
                @endforeach
                @endif

                </tbody>
            </table>
            <div class="p-5">
                {{ $appointments->links() }}
            </div>

        <x-dialog-modal wire:model="showPaymentModal">
            <x-slot name="title">
                {{ __('Mark as Complete') }}
            </x-slot>

            <x-slot name="content">
                <h2 class="text-lg font-semibold mb-4">Are you sure you want to mark this as complete?</h2>
            </x-slot>

            <x-slot name="footer">
                <div class="flex gap-3">
                    <x-button wire:click="completeAppointment" wire:loading.attr="disabled">
                        {{ __('Confirm') }}
                    </x-secondary-button>

                    <x-danger-button wire:click="closePaymentModal" wire:loading.attr="disabled">
                        {{ __('Cancel') }}
                    </x-danger-button>
                </div>

            </x-slot>
        </x-dialog-modal>

        <x-dialog-modal wire:model="confirmingAppointmentCancellation">
            <x-slot name="title">
                Cancel Appointment
            </x-slot>

            <x-slot name="content">
                <p>Are you sure you want to cancel this appointment?</p>

                <!-- Reason selection dropdown -->
                <div class="mt-4">
                    <label for="cancellationReason" class="block text-sm font-medium text-gray-700">Reason for cancellation</label>
                    <select id="cancellationReason" wire:model="cancellationReason" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Select a reason</option>
                        <option value="Not needed anymore">Not needed anymore</option>
                        <option value="Scheduling conflict">Scheduling conflict</option>
                        <option value="Found a better provider">Found a better provider</option>
                        <option value="Other">Other</option>
                    </select>
                    @error('cancellationReason') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </x-slot>

            <x-slot name="footer">
                <div class="flex gap-3">
                    <x-secondary-button wire:click="$set('confirmingAppointmentCancellation', false)" wire:loading.attr="disabled">
                        Back
                    </x-secondary-button>

                    <x-danger-button wire:click="cancelAppointment" wire:loading.attr="disabled">
                        Confirm Cancellation
                    </x-danger-button>
                </div>
            </x-slot>
        </x-dialog-modal>

        <div>
            <!-- Triggered Modal -->
            @if ($showRescheduleModal)
                <div class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50">
                    <div class="bg-white p-6 rounded-lg shadow-lg max-w-lg w-full">
                        <h3 class="text-xl font-semibold text-gray-800">Reschedule Appointment</h3>

                        <form wire:submit.prevent="rescheduleAppointment" class="mt-4 space-y-4">
                            <!-- Date Input -->
                            <div>
                                <label for="newDate" class="block text-sm font-medium text-gray-700">New Date</label>
                                <input
                                    type="date"
                                    wire:model="newDate"
                                    id="newDate"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                />
                                @error('newDate')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Time Input -->
                            <div>
                                <label for="newTime" class="block text-sm font-medium text-gray-700">New Time</label>
                                <select
                                    wire:model="newTime"
                                    id="newTime"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                >
                                    <option value="">Select a time</option>
                                    @foreach (range(8, 20) as $hour)
                                        @php
                                            $time = \Carbon\Carbon::createFromTime($hour, 0)->format('h:i A');
                                        @endphp
                                        <option value="{{ \Carbon\Carbon::createFromTime($hour, 0)->format('H:i') }}">
                                            {{ $time }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('newTime')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Modal Actions -->
                            <div class="flex justify-end space-x-2">
                                <button
                                    type="button"
                                    wire:click="closeRescheduleModal"
                                    class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 focus:outline-none">
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 focus:ring-4 focus:ring-purple-300">
                                    Reschedule
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>


        <script>
            document.addEventListener('livewire:load', function () {
                    Livewire.on('rescheduleSuccess', function () {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Appointment has been rescheduled.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        });
                    });

                    Livewire.on('rescheduleError', function () {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Failed to reschedule appointment. Please try again.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    });
                });
            document.addEventListener('livewire:load', function () {
                Livewire.on('appointmentCompleted', function () {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Appointment marked as completed.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                });

                Livewire.on('appointmentError', function () {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Appointment not found.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                });
            });
        </script>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const datepicker = document.querySelector('#default-datepicker');
            new Datepicker(datepicker, {
                format: 'yyyy-mm-dd',
                autohide: true
            });
        });
        </script>
        </div>
    </div>
</div>

