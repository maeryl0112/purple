<div class="grid gap-4 mb-4 sm:grid-cols-2">
    <div>
        <label for="first_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">First Name:</label>
        <input type="text" wire:model="newEmployee.first_name" id="first_name" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
        @error('newEmployee.first_name') <span class="text-red-500">{{ $message }}</span>@enderror
    </div>
    <div>
        <label for="last_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Last Name:</label>
        <input type="text" wire:model="newEmployee.last_name" id="last_name" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
        @error('newEmployee.last_name') <span class="text-red-500">{{ $message }}</span>@enderror
    </div>
    <div>
        <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email:</label>
        <input type="text" wire:model="newEmployee.email" id="email" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
        @error('newEmployee.email') <span class="text-red-500">{{ $message }}</span>@enderror
    </div>
    <div>
        <label for="phone_number" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Phone Number</label>
        <input type="text" wire:model="newEmployee.phone_number" id="age" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">

        @error('newEmployee.phone_number') <span class="text-red-500">{{ $message }}</span>@enderror

    </div>


    <div>
        <label for="job_category_id" class="block text-sm font-medium text-gray-700">Job Category</label>
        <select id="job_category_id" wire:model.defer="newEmployee.job_category_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 ">
            <option value="">Select a category</option>
            @foreach ($job_categories as $job_category)
                <option value="{{ $job_category->id }}">{{ $job_category->name }}</option>
            @endforeach
        </select>
        @error('newEmployee.job_category_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
        <textarea id="address" wire:model="newEmployee.address"  class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"></textarea>
        @error('newEmployee.address') <span class="text-red-500">{{ $message }}</span>@enderror
    </div>
</div>

    <div class="grid grid-cols-1 gap-6 mt-4 sm:grid-cols-3">
        <div>
            <label for="birthday" class="block text-sm font-medium text-gray-700">Birthday:</label>
            <input type="date" id="birthday" wire:model="newEmployee.birthday"  class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"></input>
            @error('newEmployee.birthday') <span class="text-red-500">{{ $message }}</span>@enderror

        </div>

        <div>
            <label for="age" class="block text-sm font-medium text-gray-700">Age</label>
            <input type="text" wire:model="newEmployee.age" id="age" aria-label="disabled input" class="mb-5 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500">
        </div>
        <div>
            <label for="date_started" class="block text-sm font-medium text-gray-700">Date Started:</label>
            <input type="date" id="date_started" wire:model="newEmployee.date_started"  class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"></input>
            @error('newEmployee.date_started') <span class="text-red-500">{{ $message }}</span>@enderror
            </div>

            <div>
            <label class="block text-sm font-medium text-gray-700"> Branch</label>

            @if(auth()->user()->role_id == 1)
                <select wire:model="newEmployee.branch_id" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" >
                    <option value="">Select Branch</option>
                    @foreach(\App\Models\Branch::all() as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
            @else
                <input type="text" class="form-input w-full bg-gray-200" value="{{ auth()->user()->branch->name }}" readonly>
            @endif
        </div>
        </div>
    <div class="sm:col-span-2">
        <div class="flex items-center me-4">
            <label>Select Working Days:</label>
            @foreach($allDays as $day)
                <div>
                    <input type="checkbox" id="day-{{ $day }}" value="{{ $day }}"
                        wire:model="workingDays" class="rounded text-purple-500 focus:ring-purple-500 focus:border-purple-500">
                    <label for="day-{{ $day }}">{{ $day }}</label>
                </div>
            @endforeach
        </div>
    </div>
    <div class="grid gap-4 mb-4 sm:grid-cols-2">


        <div>
            <label for="image">Image</label>
            <input type="file" wire:model="image" id="image" class="rounded-lg">
            @if ($image)
                <p>Preview:</p>
                <img src="{{ $image->temporaryUrl() }}" alt="Image Preview" style="max-width: 100px;">
            @elseif(isset($newEmployee['image']))
                <p>Current Image:</p>
                <img src="{{ asset('storage/' . $newEmployee['image']) }}" alt="Employee Image" style="max-width: 100px;">
            @endif
        </div>
    </div>

