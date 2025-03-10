<section >
    <h3 class="text-2xl font-bold mb-1">Book Your Appointment</h3>
    <h3 class="text-sm font-medium text-red-500">*Please Select Date first.</h3>

    <form wire:submit.prevent="addToCart">

        <!-- Date Picker -->
       <!-- Date Picker -->
<div class="mt-2">
    <h4 class="text-lg font-semibold text-gray-900">Select Date</h4>
    <fieldset>
        <input type="date"
               class="rounded py-2 px-4 border border-gray-300"
               wire:model.debounce="selectedDate"
               min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
               max="{{ \Carbon\Carbon::now()->addDays(30)->format('Y-m-d') }}"
               required>
    </fieldset>
</div>

<!-- Time Slot Selection -->
<!-- Time Slot Selection -->
<div class="mt-5">
    <h4 class="text-lg font-semibold text-gray-900">Select Time</h4>
    <div class="grid grid-cols-3 gap-4">
        @php
            $currentDate = \Carbon\Carbon::now()->format('Y-m-d');
            $currentTime = \Carbon\Carbon::now();
        @endphp

        @for($hour = 8; $hour <= 20; $hour++)
            @php
                $time = \Carbon\Carbon::today()->setHour($hour)->setMinute(0)->setSecond(0);
                $isDisabled = ($selectedDate === $currentDate && $time->isPast());
                $isSelected = ($selectedTime === $time->format('H:i'));
            @endphp

            <label class="flex items-center">
                <input type="radio"
                       wire:model="selectedTime"
                       value="{{ $time->format('H:i') }}"
                       class="hidden"
                       {{ $isDisabled ? 'disabled' : '' }}>

                <span class="py-2 px-4 text-center border rounded-lg w-full
                                {{ $isDisabled ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-white text-gray-900 hover:bg-purple-100' }}
                             {{ $isSelected ? 'bg-purple-500 text-salonPurple border-purple-600' : '' }}">
                    {{ $time->format('g:i a') }}
                </span>
            </label>
        @endfor
    </div>
</div>  

 
      <!-- Employee Selection -->
      <div class="mt-5">
    <h4 class="text-lg font-semibold text-gray-900">Select Staff</h4>
    <fieldset class="mt-2">
        <legend class="sr-only">Select an Employee</legend>
        <div class="grid grid-cols-3 gap-4" x-data="{ selectedEmployee : @entangle('selectedEmployee').defer }">
            @foreach($employees as $employee)
                <div wire:key="employee-{{ $employee->id }}-element">
                    @if($employee->available)
                        <!-- Available Employee -->
                        <label wire:key="employee-{{ $employee->id }}-available"
                               class="group relative flex items-center text-gray-800 justify-center rounded-md border py-3 px-4 text-sm font-medium uppercase focus:outline-none sm:flex-1 cursor-pointer shadow-sm"
                               x-bind:class="{
                                   'bg-purple-500 text-white': selectedEmployee === {{ $employee->id }},
                                   'bg-gray-50 hover:bg-purple-100': selectedEmployee !== {{ $employee->id }}
                               }">
                            <input type="radio" name="employee-choice"
                                   value="{{ $employee->id }}"
                                   class="sr-only"
                                   x-on:change="selectedEmployee = {{ $employee->id }}"
                                   aria-labelledby="employee-choice-{{ $employee->id }}-label">
                            <span class="text-center" id="employee-choice-{{ $employee->id }}-label">
                                {{ $employee->first_name }}
                                <br>
                                <span>{{ $employee->jobCategory?->name }}</span>
                                <span>{{ $employee->branch?->name }}</span>
                            </span>
                        </label>
                    @else
                        <!-- Unavailable Employee -->
                        <label wire:key="employee-{{ $employee->id }}-unavailable"
                               class="group relative flex items-center justify-center rounded-md border py-3 px-4 text-sm font-medium uppercase cursor-not-allowed"
                               x-bind:class="{
                                   'bg-red-500 text-white': '{{ $employee->reason }}' === 'taken',
                                   'bg-red-500 text-white': '{{ $employee->reason }}' === 'off_duty'
                               }">
                            <input type="radio" name="employee-choice"
                                   value="{{ $employee->id }}" disabled class="sr-only"
                                   aria-labelledby="employee-choice-{{ $employee->id }}-label">
                            <span class="text-center"  id="employee-choice-{{ $employee->id }}-label">
                                {{ $employee->first_name }}
                                <br>
                                <span>{{ $employee->jobCategory?->name }}</span>
                                <span>{{ $employee->branch?->name }}</span>
                                <br>
                                @if($employee->reason === 'taken')
                                    <span class="text-xs italic">(Taken)</span>
                                @elseif($employee->reason === 'off_duty')
                                    <span class="text-xs italic">(Off)</span>
                                @endif
                            </span>
                            <span aria-hidden="true"
                                  class="pointer-events-none absolute -inset-px rounded-md border-2"
                                  x-bind:class="{
                                      'border-red-500': '{{ $employee->reason }}' === 'taken',
                                      'border-gray-400': '{{ $employee->reason }}' === 'off_duty'
                                  }">
                                <svg class="absolute inset-0 h-full w-full stroke-2"
                                     x-bind:class="{
                                         'text-red-500': '{{ $employee->reason }}' === 'taken',
                                         'text-gray-400': '{{ $employee->reason }}' === 'off_duty'
                                     }"
                                     viewBox="0 0 100 100" preserveAspectRatio="none" stroke="currentColor">
                                    <line x1="0" y1="0" x2="0" y2="0" vector-effect="non-scaling-stroke"/>
                                </svg>
                            </span>
                        </label>
                    @endif
                </div>
            @endforeach
        </div>
    </fieldset>
</div>


        <!-- Submit Button -->
        <button type="submit"
                class="mt-6 flex w-full items-center justify-center rounded-md border border-transparent bg-purple-600 px-8 py-3 text-base font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                :disabled="!selectedTimeSlot || !selectedEmployee">
            Confirm
        </button>
    </form>

    <script>
        document.addEventListener('swal:alert', function (event) {
            const { title, text, icon, redirect_url } = event.detail;

            Swal.fire({
                title: title || 'Alert',
                text: text || '',
                icon: icon || 'info',
            }).then((result) => {
                if (redirect_url) {
                    window.location.href = redirect_url; // Redirect after closing the alert
                }
            });
        });
    </script>

</section>
