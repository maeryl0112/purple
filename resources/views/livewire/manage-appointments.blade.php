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
        

        <div class="overflow-auto rounded-lg border border-gray-200 shadow-md m-5">
        <div class="w-full m-4 flex">
        <div class="w-1/1 mx-2">

        <!-- Search Input -->
        <label for="default-search" class="my-2 text-sm font-medium text-gray-900 sr-only ">Search</label>
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
        <div class="pt-2.5">
       
           <input 
               type="date" 
               wire:model="filterDate" 
               id="filterDate" 
              class="border text-gray-900 px-5 pt-2.5 me-2  border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
               placeholder="Select date">

               <select 
                id="timeFilter" 
                wire:model="timeFilter" 
                class="border text-gray-900 px-5 pt-2.5 me-2  border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                <option value="">All</option>
                <option value="08:00:00">8:00 AM</option>
                <option value="09:00:00">9:00 AM</option>
                <option value="10:00:00">10:00 AM</option>
                <option value="11:00:00">11:00 AM</option>
                <option value="12:00:00">12:00 PM</option>
                <option value="13:00:00">1:00 PM</option>
                <option value="14:00:00">2:00 PM</option>
                <option value="15:00:00">3:00 PM</option>
                <option value="16:00:00">4:00 PM</option>
                <option value="17:00:00">5:00 PM</option>
                <option value="18:00:00">6:00 PM</option>
                <option value="19:00:00">7:00 PM</option>
                <option value="20:00:00">8:00 PM</option>


            </select>

      
                <select wire:model="selectFilter" class="border text-gray-900 px-5 pt-2.5 me-2  border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    <option value="upcoming">Upcoming</option>
                    <option value="previous">Previous</option>
                    <option value="cancelled">Cancelled</option>
                </select>
  

            <select wire:model="employeeId" wire:model="employeeId" class="border text-gray-900 px-5 pt-2.5 me-2  border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
            <option value="">All Employees</option>
            @foreach($employees as $employee)
                <option value="{{ $employee->id }}">{{ $employee->first_name }}</option>
            @endforeach
        </select>

        <select wire:model="serviceId" id="serviceFilter" class="border text-gray-900 px-5 pt-2.5 me-2  border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
        <option value="">All Services</option>
        @foreach($services as $service)
            <option value="{{ $service->id }}">{{ $service->name }}</option>
        @endforeach
    </select>

    @if(Auth::user()->role_id == 1 )
            <select id="branchId" wire:model="branchId"  class="border text-gray-900 px-5 pt-2.5 me-2  border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    <option value="" selected>All Branch<i class="fas fa-code-branch    "></i></option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
                @endif

    
            
            <select 
                id="paymentFilter" 
                wire:model="paymentFilter" 
                class="border text-gray-900 px-5 pt-2.5 me-2  border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                <option value="">All</option>
                <option value="cash">Cash</option>
                <option value="online">Online</option>
            </select>
        
        </div>

</div>
    </div>


            <table class="w-full border-collapse bg-white text-left text-sm text-gray-500 overflow-x-scroll min-w-screen">
                <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="pl-6 py-4 font-bold text-gray-900">Number</th>
                    <th scope="col" class="px-6 py-4 font-bold text-gray-900">Service</th>
                    <th scope="col" class="px-6 py-4 font-bold text-gray-900">Appointed Date</th>
                    <th scope="col" class="px-6 py-4 font-bold text-gray-900">Time</th>
                    <th scope="col" class="px-6 py-4 font-bold text-gray-900">Staff Assigned</th>

                    @if (auth()->user()->role->name  == 'Admin' || auth()->user()->role->name  == 'Employee')

                    <th scope="col" class="px-6 py-4 font-bold text-gray-900">Customer</th>
                    <th scope="col" class="px-6 py-4 font-bold text-gray-900">Branch</th>

                        <th scope="col" class="px-6 py-4 font-bold text-gray-900">Contact No</th>
                    @endif
                    <th scope="col" class="px-6 py-4 font-bold text-gray-900">Payment</th>
                    @if ($selectFilter == 'cancelled')
                    <th scope="col" class="px-6 py-4 font-bold text-gray-900">Reason</th>
                    @endif
                    <th scope="col" class="px-6 py-4 font-bold text-gray-900">Created Date</th>
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
                        <td class="px-6 py-4 max-w-xs font-medium text-gray-700">
    {{ \Carbon\Carbon::parse($appointment->date)->format('F j') }}
</td>

                        <td class="px-6 py-4 max-w-xs font-medium text-gray-700">
                            {{ \Carbon\Carbon::createFromFormat('H:i:s', $appointment->time)->format('h:i A') }}
                        </td>
                        <td class="px-6 py-4 max-w-xs font-medium text-gray-700">{{ $appointment->first_name}}</td>

                        @if (auth()->user()->role->name == 'Admin' || auth()->user()->role->name == 'Employee')
                            <td class="px-6 py-4 max-w-xs font-medium text-gray-700">{{ $appointment->user->name}}</td>
                                <td class="px-6 py-4 max-w-xs font-medium text-gray-700"> {{ $appointment->employee?->branch?->name }}

                                </td>

                            <td class="px-6 py-4 max-w-xs font-medium text-gray-700">{{ $appointment->user->phone_number}}</td>
                            <td class="px-6 py-4 max-w-xs font-medium text-gray-700">{{ $appointment->payment }} @if ($appointment->payment === 'online' && $appointment->last_four_digits)
                                <p>Reference Last 4 Digits - {{ $appointment->last_four_digits }}</p>
                            @endif</td>
                            @if ($selectFilter == 'cancelled')
                            <td class="px-6 py-4 max-w-xs font-medium text-gray-700">{{ $appointment->cancellation_reason}}</td>
                            @endif
                            @endif
                            <td class="px-6 py-4 max-w-xs font-medium text-gray-700">
    {{ $appointment->created_at->format('F j, Y g:i A') }}
</td>

                            <td class="px-6 py-4 gap-2">

                           
                                @if ($selectFilter == 'upcoming' || $selectFilter == 'previous')

                                <button wire:click="openPaymentModal('{{ $appointment->id }}')" 
                                       
                                        class="text-white bg-gradient-to-r from-purple-500 via-purple-600 to-purple-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-purple-300 dark:focus:ring-purple-800 rounded-lg text-xs px-7 py-2 inline-flex items-center me-1 mb-2 w-24">
                                    Confirm
                                </button>

                                <button
                                wire:click="openRescheduleModal({{ $appointment->id }})"
                                class="px-4 py-2 flex items-center bg-blue-600 text-white rounded-lg text-xs hover:bg-blue-700 focus:ring-2 focus:ring-blue-400">

                                Reschedule
                            </button>
                            <button wire:click="setAppointmentIdToCancel({{ $appointment->id }})" wire:loading.attr="disabled" class="px-8 py-2 mt-2 flex items-center bg-red-600 text-white rounded-lg text-xs hover:bg-red-700 focus:ring-2 focus:ring-red-400 w-24">
                                                {{ __('Cancel') }}
                                            </button>

                              

                

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
                {{ __('Confirming Appointment') }}
            </x-slot>

            <x-slot name="content">
                <h2 class="text-lg font-semibold mb-4">Are you sure you want to confirm this as appointment?</h2>
            </x-slot>

            <x-slot name="footer">
                <div class="flex gap-3">
        <button wire:click="completeAppointment({{ $appointmentId }})" class="bg-purple-500 text-white px-4 py-2 rounded">
        Confirm
    </button>



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
            <div class="mt-4">
                <label for="cancellationReason" class="block text-sm font-medium text-gray-700">Reason for cancellation</label>
                <select id="cancellationReason" wire:model="cancellationReason" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">Select a reason</option>
                    <option value="Not needed anymore">Not needed anymore</option>
                    <option value="Scheduling conflict">Scheduling conflict</option>
                    <option value="Found a better provider">Found a better provider</option>
                    <option value="Other">Other</option>
                </select>
                @error('cancellationReason') 
                    <span class="text-red-500 text-sm">{{ $message }}</span> 
                @enderror
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('confirmingAppointmentCancellation', false)">
                Back
            </x-secondary-button>
            <x-danger-button wire:click="cancelAppointment">
                Confirm Cancellation
            </x-danger-button>
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
                        text: 'Appointment confirmed. Email Details Sent!',
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
                Livewire.on('appointmentError', function () {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to cancel appointment. Please try again.',
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

