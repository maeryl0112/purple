<x-dashboard>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create User') }}
        </h2>
    </x-slot>

    <div>
        <form action="{{ route('manageusers.store')}}" method="post" class="w-1/2 mx-auto bg-white rounded-lg p-5">
            @csrf
              <!-- Name -->
             <!-- Role Selection -->
             <div class="col-span-6 sm:col-span-4 my-2">
                <x-label for="role" value="{{ __('Role') }}" />
                <select name="role" id="role" class="border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-md shadow-sm" onchange="toggleEmployeeSelection()">
                    <option value="customer">Customer</option>
                    @if(Auth::user()->role_id == 1 )
                    <option value="employee">Employee</option>
                    @endif
                </select>
                <x-input-error for="role" class="mt-2" />
            </div>

            <div id="employee-selection" class="col-span-6 sm:col-span-4 my-2 hidden">
                <x-label for="employee_id" value="{{ __('Select Employee') }}" />
                <select name="employee_id" id="employee_id" class="border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-md shadow-sm">
                    <option value="">Select an employee</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->first_name    }}</option>
                    @endforeach
                </select>
                <x-input-error for="employee_id" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4 my-2" id="branch-field">
    <x-label for="branch_id" value="{{ __('Branch') }}" />
    <select name="branch_id" id="branch_id" class="border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-md shadow-sm" 
        {{ Auth::user()->role_id != 1 ? 'disabled' : '' }}>
        @foreach($branches as $branch)
            <option value="{{ $branch->id }}" 
                {{ Auth::user()->role_id != 1 && Auth::user()->branch_id == $branch->id ? 'selected' : '' }}>
                {{ $branch->name }}
            </option>
        @endforeach
    </select>
    <x-input-error for="branch_id" class="mt-2" />
</div>



        <!-- Name, Email, Phone Number (Only for Customers) -->
            <div id="customer-fields">
                <div class="col-span-6 sm:col-span-4 my-2">
                    <x-label for="name" value="{{ __('Name') }}" />
                    <x-input id="name" type="text" class="mt-1 block w-full" name="name" />
                    <x-input-error for="name" class="mt-2" />
                </div>

                <div class="col-span-6 sm:col-span-4 my-2">
                    <x-label for="email" value="{{ __('Email') }}" />
                    <x-input id="email" type="email" class="mt-1 block w-full" name="email" />
                    <x-input-error for="email" class="mt-2" />
                </div>

                <div class="col-span-6 sm:col-span-4 my-2">
                    <x-label for="phone_number" value="{{ __('Phone Number') }}" />
                    <x-input id="phone_number" type="text" class="mt-1 block w-full" name="phone_number" />
                    <x-input-error for="phone_number" class="mt-2" />
                </div>
            </div>

        <!-- Password and Confirm Password (Required for Both) -->
        <div class="col-span-6 sm:col-span-4 my-2">
            <x-label for="password" value="{{ __('Password') }}" />
            <x-input id="password" type="password" class="mt-1 block w-full" name="password"/>
            <x-input-error for="password" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4 my-2">
            <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
            <x-input id="password_confirmation" type="password" class="mt-1 block w-full" name="password_confirmation" />
            <x-input-error for="password_confirmation" class="mt-2" />
        </div>

       
          

            <div class="flex items-center justify-end mt-4">
                <x-button class="ml-4">
                    {{ __('Create User') }}
                </x-button>
            </div>
        </form>
    </div>
    <script>
        function toggleEmployeeSelection() {
            let role = document.getElementById("role").value;
            let employeeSelection = document.getElementById("employee-selection");
            let customerFields = document.getElementById("customer-fields");
            let branchField = document.getElementById("branch_id");

            if (role === "employee") {
                employeeSelection.classList.remove("hidden");
                customerFields.classList.add("hidden");
                branchField.disabled = true; // Disable branch field for employees
            } else {
                employeeSelection.classList.add("hidden");
                customerFields.classList.remove("hidden");
                branchField.disabled = false; // Ensure branch field is enabled for customers
            }
        }
        
</script>
</x-dashboard>
